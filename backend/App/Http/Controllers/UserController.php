<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Merr të gjithë përdoruesit
    public function index() {
        return User::all();
    }

    // Merr një përdorues sipas ID
    public function show($id) {
        return User::findOrFail($id);
    }

    // Krijon përdorues të ri
    public function store(Request $request) {
        $data = $request->validate([
            'username'       => 'required|string|unique:users,username|max:100',
            'name'           => 'required|string|max:255',
            'surname'        => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email|max:255',
            'password'       => 'required|string|min:6',
            'RoleID'         => 'nullable|integer',
            'phone'          => 'nullable|string|max:255',
            'address'        => 'nullable|string|max:255',
            'dob'            => 'nullable|date',
            'gender'         => 'nullable|in:male,female,other',
            'fitness_goal'   => 'nullable|in:lose_fat,gain_muscle,stay_fit',
            'activity_level' => 'nullable|in:low,medium,high',
            'training_days'  => 'nullable|integer|min:0|max:7',
            'focus_area'     => 'nullable|in:upper_body,lower_body,cardio',
        ]);

        // Hash password
        $data['password'] = Hash::make($data['password']);

        // Default RoleID = 2 (normal user) nëse nuk është dhënë
        $data['RoleID'] = $data['RoleID'] ?? 2;

        $user = User::create($data);

        return response()->json($user, 201);
    }

    // Përditëson një përdorues ekzistues
    public function update(Request $request, $id) {
        $user = User::findOrFail($id);

        $data = $request->validate([
            'username'       => 'sometimes|string|unique:users,username,' . $id,
            'name'           => 'sometimes|string|max:255',
            'surname'        => 'sometimes|string|max:255',
            'email'          => 'sometimes|email|unique:users,email,' . $id,
            'password'       => 'sometimes|string|min:6',
            'RoleID'         => 'sometimes|integer',
            'phone'          => 'sometimes|string|max:255',
            'address'        => 'sometimes|string|max:255',
            'dob'            => 'sometimes|date',
            'gender'         => 'sometimes|in:male,female,other',
            'fitness_goal'   => 'sometimes|in:lose_fat,gain_muscle,stay_fit',
            'activity_level' => 'sometimes|in:low,medium,high',
            'training_days'  => 'sometimes|integer|min:0|max:7',
            'focus_area'     => 'sometimes|in:upper_body,lower_body,cardio',
        ]);

        // Hash password nëse është dhënë
        if(isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json($user);
    }

    // Fshin një përdorues
    public function destroy($id) {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }
}
