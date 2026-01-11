<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Password;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Database\QueryException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string|min:6',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid username or password'], 401);
        }

        try {
            $token = JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Failed to generate token',
                'error'   => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => JWTAuth::factory()->getTTL() * 60,
            'user'         => $user
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'username'       => 'required|string|unique:users,username|max:100',
            'name'           => 'required|string|max:255',
            'surname'        => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email|max:255',
            'password'       => 'required|string|min:6|confirmed',
            'phone'          => 'nullable|string|max:255',
            'address'        => 'nullable|string|max:255',
            'dob'            => 'nullable|date',
            'gender'         => 'nullable|in:male,female,other',
            'fitness_goal'   => 'nullable|in:lose fat,gain muscle,stay fit',
            'activity_level' => 'nullable|in:low,medium,high',
            'training_days'  => 'nullable|integer|min:0|max:7',
            'focus_area'     => 'nullable|in:upper body,lower body,cardio',
        ]);

        try {
            $user = User::create([
                'username'       => $data['username'],
                'name'           => $data['name'],
                'surname'        => $data['surname'],
                'email'          => $data['email'],
                'password'       => Hash::make($data['password']),
                'RoleID'         => 2,
                'phone'          => $data['phone'] ?? null,
                'address'        => $data['address'] ?? null,
                'dob'            => $data['dob'] ?? null,
                'gender'         => $data['gender'] ?? null,
                'fitness_goal'   => $data['fitness_goal'] ?? null,
                'activity_level' => $data['activity_level'] ?? null,
                'training_days'  => $data['training_days'] ?? null,
                'focus_area'     => $data['focus_area'] ?? null,
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'message' => 'Failed to create user',
                'error'   => $e->getMessage(),
                'sql'     => $e->getSql(),
                'bindings'=> $e->getBindings()
            ], 500);
        }

        try {
            $token = JWTAuth::fromUser($user);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Failed to generate token',
                'error'   => $e->getMessage()
            ], 500);
        }

        return response()->json([
            'user'         => $user,
            'access_token' => $token,
            'token_type'   => 'bearer',
            'expires_in'   => JWTAuth::factory()->getTTL() * 60
        ], 201);
    }


    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::parseToken());
            return response()->json(['message' => 'Successfully logged out']);
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'Failed to logout, token invalid',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function me(Request $request)
{
    try {
        $user = JWTAuth::parseToken()->authenticate();

        return response()->json([
            'id' => $user->id,
            'role_id' => $user->RoleID,
            'staff_type' => $user->staff_type,
            'name' => $user->name,
            'surname' => $user->surname,
            'email' => $user->email,
        ]);

    } catch (JWTException $e) {
        return response()->json([
            'message' => 'Token not valid',
            'error'   => $e->getMessage()
        ], 401);
    }
}


    public function forgotPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['message' => 'Reset link sent! Check your email.']);
        }

        return response()->json([
            'message' => 'Failed to send reset link.',
            'status'  => $status
        ], 500);
    }
}
