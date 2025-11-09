<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;

class JwtMiddleware
{
    public function handle($request, Closure $next)
{
    try {
        $token = JWTAuth::parseToken()->getToken();
        \Log::info('Token:', ['token' => (string)$token]);
        $user = JWTAuth::parseToken()->authenticate();
        \Log::info('User:', ['user' => $user]);
    } catch (\Exception $e) {
        \Log::error('JWT Error:', ['message' => $e->getMessage()]);
        return response()->json(['error' => $e->getMessage()], 401);
    }

    return $next($request);
}

}
