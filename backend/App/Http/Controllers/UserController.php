<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search             = $request->query('search');
        $role               = $request->query('role');
        $preferredTrainerId = $request->query('preferred_trainer_id');
        $perPage            = $request->query('per_page', 10);

        $users = User::when($role, function ($query) use ($role) {
                if ($role === 'trainer') {
                    return $query->where('RoleID', 3);
                }

                if (is_numeric($role)) {
                    return $query->where('RoleID', $role);
                }

                return $query;
            })
            ->when($preferredTrainerId, function ($query) use ($preferredTrainerId) {
                return $query->where('preferred_trainer_id', $preferredTrainerId);
            })
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', "%$search%")
                      ->orWhere('surname', 'LIKE', "%$search%")
                      ->orWhere('username', 'LIKE', "%$search%");
            })
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json($users);
    }

    public function show($UserID)
    {
        return response()->json(User::findOrFail($UserID));
    }

    public function trainers()
    {
        $trainers = User::where('RoleID', 3)
            ->orderBy('name')
            ->get([ 'UserID', 'name', 'surname', 'photo', 'description', 'focus_area', 'staff_type' ]);

        return response()->json($trainers);
    }

    public function dynamic()
    {
        $columns = array_filter(
            Schema::getColumnListing('users'),
            fn ($col) => $col !== 'password'
        );

        return response()->json([
            'columns' => array_values($columns),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username'       => 'required|string|unique:users,username',
            'name'           => 'required|string|max:255',
            'surname'        => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|string|min:6',
            'phone'          => 'nullable|string|max:255',
            'address'        => 'nullable|string|max:255',
            'dob'            => 'nullable|date',
            'gender'         => 'nullable|in:male,female,other',
            'fitness_goal'   => 'nullable|in:lose fat,gain muscle,stay fit',
            'activity_level' => 'nullable|in:low,medium,high',
            'training_days'  => 'nullable|integer|min:0|max:7',
            'focus_area'     => 'nullable|in:upper body,lower body,cardio',
            'RoleID'         => 'nullable|integer',
            'staff_type'     => 'nullable|in:trainer,maintenance,service_staff',
            'photo'          => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
            'description'    => 'nullable|string|max:1000',
        ]);

        $data['RoleID'] = $data['RoleID'] ?? 2;

        if ($data['RoleID'] != 3) {
            $data['staff_type'] = null;
        }

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = $file->getClientOriginalName();
            $file->move(public_path('uploads/profilephotos'), $filename);
            $data['photo'] = $filename;
        }

        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return response()->json($user, 201);
    }

    public function update(Request $request, $UserID)
    {
        $user = User::findOrFail($UserID);

        $data = $request->validate([
            'username'       => 'sometimes|string|unique:users,username,' . $UserID . ',UserID',
            'name'           => 'sometimes|string|max:255',
            'surname'        => 'sometimes|string|max:255',
            'email'          => 'sometimes|email|unique:users,email,' . $UserID . ',UserID',
            'password'       => 'nullable|string|min:6',
            'RoleID'         => 'nullable|integer',
            'phone'          => 'nullable|string|max:255',
            'address'        => 'nullable|string|max:255',
            'dob'            => 'nullable|date',
            'gender'         => 'nullable|in:male,female,other',
            'fitness_goal'   => 'nullable|in:lose fat,gain muscle,stay fit',
            'activity_level' => 'nullable|in:low,medium,high',
            'training_days'  => 'nullable|integer|min:0|max:7',
            'focus_area'     => 'nullable|in:upper body,lower body,cardio',
            'staff_type'     => 'nullable|in:trainer,maintenance,service_staff',
            'preferred_trainer_id' => 'nullable|integer|exists:users,UserID',
            'photo'          => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,avif|max:2048',
            'description'    => 'nullable|string|max:1000',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if (array_key_exists('preferred_trainer_id', $data) && $data['preferred_trainer_id']) {
            $trainer = User::find($data['preferred_trainer_id']);
            if (!$trainer || $trainer->RoleID !== 3) {
                return response()->json(['message' => 'Preferred trainer must be a valid trainer.'], 422);
            }
        }

        $roleId = $data['RoleID'] ?? $user->RoleID;
        if ($roleId != 3) {
            $data['staff_type'] = null;
        }

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $filename = $file->getClientOriginalName();

            if (
                $user->photo &&
                $user->photo !== $filename &&
                file_exists(public_path('uploads/profilephotos/' . $user->photo))
            ) {
                unlink(public_path('uploads/profilephotos/' . $user->photo));
            }

            $file->move(public_path('uploads/profilephotos'), $filename);
            $data['photo'] = $filename;
        }

        if (!array_key_exists('RoleID', $data)) {
            $data['RoleID'] = $user->RoleID;
        }

        $user->update($data);

        return response()->json($user);
    }

    public function destroy($UserID)
    {
        $user = User::findOrFail($UserID);

        if (
            $user->photo &&
            file_exists(public_path('uploads/profilephotos/' . $user->photo))
        ) {
            unlink(public_path('uploads/profilephotos/' . $user->photo));
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
