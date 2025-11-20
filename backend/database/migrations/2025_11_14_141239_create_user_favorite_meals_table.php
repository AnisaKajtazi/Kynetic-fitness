<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::create('user_favorite_meals', function (Blueprint $table) {
    $table->id();

    $table->unsignedBigInteger('user_id');
    $table->unsignedInteger('meal_id');

    $table->timestamps();

    $table->foreign('user_id')->references('UserID')->on('users')->onDelete('cascade');
    $table->foreign('meal_id')->references('MealID')->on('meals')->onDelete('cascade');
});

    }

    public function down(): void
    {
        Schema::dropIfExists('user_favorite_meals');
    }
};
