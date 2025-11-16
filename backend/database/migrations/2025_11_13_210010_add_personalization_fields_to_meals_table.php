<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       Schema::table('meals', function (Blueprint $table) {
    $table->enum('fitness_goal', ['lose weight', 'gain muscle', 'stay fit'])->nullable()->after('calories');
    $table->enum('activity_level', ['low', 'medium', 'high'])->nullable()->after('fitness_goal');
    $table->tinyInteger('training_days')->nullable()->after('focus_area'); 
    $table->enum('focus_area', ['upper body', 'lower body', 'cardio'])->nullable()->after('activity_level');

});
    }

    public function down(): void
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->dropColumn(['fitness_goal', 'activity_level','training_days', 'focus_area']);
        });
    }
};
