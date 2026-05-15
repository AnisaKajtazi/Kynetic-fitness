<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  public function up()
{
    Schema::create('exercise_plan_items', function (Blueprint $table) {
        $table->id();

        $table->unsignedBigInteger('plan_id');
        $table->unsignedInteger('exercise_id');

        $table->string('day_of_week');

        $table->boolean('completed')->default(false);
        $table->timestamp('completed_at')->nullable();

        $table->timestamps();

        $table->foreign('plan_id')
              ->references('id')
              ->on('weekly_plans')
              ->onDelete('cascade');

        $table->foreign('exercise_id')
              ->references('ExerciseID')
              ->on('exercises')
              ->onDelete('cascade');
    });
}
};
