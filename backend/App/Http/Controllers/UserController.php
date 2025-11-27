<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserController extends Controller
{
    public function index() {
        return User::all();
    }

    public function show($UserID) {
    return User::findOrFail($UserID);
}

    public function dynamic() {
        $columns = Schema::getColumnListing('users');

        $columns = array_filter($columns, fn($col) => $col !== 'password');

        $users = User::all();

        return response()->json([
            'columns' => $columns,
            'users' => $users
        ]);
    }

    public function store(Request $request) {
        $data = $request->validate([
    'username'       => 'sometimes|string|unique:users,username',
    'name'           => 'sometimes|string|max:255',
    'surname'        => 'sometimes|string|max:255',
    'email'          => 'sometimes|email|unique:users,email',
    'password'       => 'sometimes|string|min:6',
    'RoleID'         => 'sometimes|integer',
    'phone'          => 'sometimes|string|max:255',
    'address'        => 'sometimes|string|max:255',
    'dob'            => 'sometimes|date',
    'gender'         => 'sometimes|in:male,female,other',
    'fitness_goal'   => 'sometimes|in:lose fat,gain muscle,stay fit',
    'activity_level' => 'sometimes|in:low,medium,high',
    'training_days'  => 'sometimes|integer|min:0|max:7',
    'focus_area'     => 'sometimes|in:upper body,lower body,cardio',
]);

        $data['phone'] = $data['phone'] ?? '';
        $data['address'] = $data['address'] ?? '';
        $data['password'] = Hash::make($data['password']);
        $data['RoleID'] = $data['RoleID'] ?? 2;

        $user = User::create($data);

        return response()->json($user, 201);
    }

    public function update(Request $request, $UserID) {
    $user = User::findOrFail($UserID);

        $data = $request->validate([
            'username'       => 'sometimes|string|unique:users,username,' . $UserID . ',UserID',
            'name'           => 'sometimes|string|max:255',
            'surname'        => 'sometimes|string|max:255',
            'email'          => 'sometimes|email|unique:users,email,' . $UserID . ',UserID',
            'password'       => 'sometimes|string|min:6',
            'RoleID'         => 'sometimes|integer',
            'phone'          => 'sometimes|string|max:255',
            'address'        => 'sometimes|string|max:255',
            'dob'            => 'sometimes|date',
            'gender'         => 'sometimes|in:male,female,other',
            'fitness_goal'   => 'sometimes|in:lose fat,gain muscle,stay fit',
            'activity_level' => 'sometimes|in:low,medium,high',
            'training_days'  => 'sometimes|integer|min:0|max:7',
            'focus_area'     => 'sometimes|in:upper body,lower body,cardio',
        ]);

        if(isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json($user);
    }

    public function destroy($UserID) {
        $user = User::findOrFail($UserID);
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }
}
