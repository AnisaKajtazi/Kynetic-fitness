<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('exercise_plan_items', function (Blueprint $table) {
        $table->integer('reps')->default(10);
    });
}

    public function down(): void
    {
        Schema::table('exercise_plan_items', function (Blueprint $table) {
        });
    }
};
