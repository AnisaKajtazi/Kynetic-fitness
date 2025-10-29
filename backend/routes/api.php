<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

Route::prefix('roles')->controller(RoleController::class)->group(function () {
    Route::get('/', 'index'); 
    Route::post('/', 'store');
    Route::get('/{id}', 'show');
    Route::put('/{id}', 'update');
    Route::delete('/{id}', 'destroy');
});

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });

    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
});

Route::prefix('users')->controller(UserController::class)->middleware('auth:api')->group(function () {
    Route::get('/', 'index');    
    Route::post('/', 'store');  
    Route::get('/{id}', 'show'); 
    Route::put('/{id}', 'update'); 
    Route::delete('/{id}', 'destroy'); 
});
