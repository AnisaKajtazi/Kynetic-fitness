<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('meals', function (Blueprint $table) {
            $table->increments('MealID');
            $table->string('name', 255); 
            $table->text('description')->nullable(); 
            $table->string('category', 100)->nullable(); 
            $table->string('image', 255)->nullable();
            $table->integer('calories')->nullable(); 
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meals');
    }
};
