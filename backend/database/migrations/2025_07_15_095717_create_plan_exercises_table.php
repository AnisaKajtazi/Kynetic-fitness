<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanExercisesTable extends Migration
{
    public function up(): void
    {
        Schema::create('plan_exercises', function (Blueprint $table) {
            $table->increments('Plan_ExercisesID');
            $table->unsignedBigInteger('Fitness_PlanID');
            $table->unsignedInteger('ExerciseID'); 
            $table->integer('day_number');
            $table->timestamps();

           $table->foreign('Fitness_PlanID')->references('Fitness_PlanID')->on('fitness_plans')->onDelete('cascade');
            $table->foreign('ExerciseID')->references('ExerciseID')->on('exercises')->onDelete('cascade');

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plan_exercises');
    }
}
