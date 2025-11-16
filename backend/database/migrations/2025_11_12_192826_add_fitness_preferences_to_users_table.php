<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('fitness_goal', ['lose fat', 'gain muscle', 'stay fit'])->nullable()->after('status');
            $table->enum('activity_level', ['low', 'medium', 'high'])->nullable()->after('fitness_goal');
            $table->integer('training_days')->nullable()->after('activity_level');
            $table->enum('focus_area', ['upper body', 'lower body', 'cardio'])->nullable()->after('training_days');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['fitness_goal', 'activity_level', 'training_days', 'focus_area']);
        });
    }
};
