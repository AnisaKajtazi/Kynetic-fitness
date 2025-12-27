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
        $search   = $request->query('search');
        $perPage  = $request->query('per_page', 10);

        $users = User::when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', $search . '%')
                      ->orWhere('surname', 'LIKE', $search . '%')
                      ->orWhere('username', 'LIKE', $search . '%');
            })
            ->orderBy('name')
            ->paginate($perPage);

        return response()->json($users);
    }

    public function show($UserID)
    {
        return response()->json(
            User::findOrFail($UserID)
        );
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
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['RoleID']   = $data['RoleID'] ?? 3;

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
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if (!array_key_exists('RoleID', $data)) {
            $data['RoleID'] = $user->RoleID;
        }

        $user->update($data);

        return response()->json($user);
    }

    
    public function destroy($UserID)
    {
        User::findOrFail($UserID)->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
