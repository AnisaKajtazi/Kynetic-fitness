<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\UserFavoriteController;


Route::prefix('roles')->controller(RoleController::class)->group(function () {
    Route::get('/', 'index'); 
    Route::post('/', 'store')->middleware('jwt.auth'); // vetëm user të kyqur
    Route::get('/{id}', 'show'); 
    Route::put('/{id}', 'update')->middleware('jwt.auth'); 
    Route::delete('/{id}', 'destroy')->middleware('jwt.auth'); 
});


Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

    Route::middleware('jwt.auth')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});


Route::prefix('users')->controller(UserController::class)->middleware('jwt.auth')->group(function () {
    Route::get('/', 'index');    
    Route::post('/', 'store');  
    Route::get('/{id}', 'show'); 
    Route::put('/{id}', 'update'); 
    Route::delete('/{id}', 'destroy'); 
});


Route::prefix('exercises')->controller(ExerciseController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'show');

    Route::middleware('jwt.auth')->group(function () {
        Route::post('/', 'store'); 
        Route::delete('/{id}', 'destroy');
    });
});



Route::middleware(['jwt.auth'])->group(function () {
    Route::get('/favorites', [UserFavoriteController::class, 'index']);
    Route::post('/favorites', [UserFavoriteController::class, 'store']);
    Route::delete('/favorites/{exercise_id}', [UserFavoriteController::class, 'destroy']);
});
