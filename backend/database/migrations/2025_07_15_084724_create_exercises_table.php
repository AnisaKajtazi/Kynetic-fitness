<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->increments('ExerciseID'); 
            $table->string('name', 255); 
            $table->text('description')->nullable(); 
            $table->integer('duration')->nullable(); 
            $table->string('image', 255)->nullable();
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercises');
    }
};
