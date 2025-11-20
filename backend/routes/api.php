<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExerciseController;
use App\Http\Controllers\UserFavoriteController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\MyCartController;


Route::prefix('roles')->controller(RoleController::class)->group(function () {
    Route::get('/', 'index'); 
    Route::post('/', 'store')->middleware('jwt.auth');
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

Route::get('/meals', [MealController::class, 'index']);

Route::middleware(['jwt.auth'])->group(function () {
    Route::get('/meals/user', [MealController::class, 'userMeals']);
    Route::post('/meals/fav', [MealController::class, 'addFavorite']);
    Route::post('/meals/personalise', [MealController::class, 'personaliseMeal']);
});

Route::middleware('jwt.auth')->group(function () {
    Route::get('/my-cart', [MyCartController::class, 'index']);
    Route::post('/my-cart', [MyCartController::class, 'store']);
    Route::patch('/my-cart/{meal_id}/quantity', [MyCartController::class, 'updateQuantity']);
    Route::delete('/my-cart/{meal_id}', [MyCartController::class, 'destroy']);
});
