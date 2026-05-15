<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercise_progress', function (Blueprint $table) {

            $table->increments('ExerciseProgressID');

            $table->unsignedInteger('user_id');

            $table->unsignedInteger('exercise_id');

            $table->boolean('completed')->default(false);

            $table->integer('duration_completed')->default(0);

            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->foreign('exercise_id')
                ->references('ExerciseID')
                ->on('exercises')
                ->onDelete('cascade');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_progress');
    }
};