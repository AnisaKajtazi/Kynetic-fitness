<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $table->enum('category', [
                'Upper Body',
                'Lower Body',
                'Core',
                'Cardio',
                'Full Body'
            ])->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $table->string('category', 100)->nullable()->change();
        });
    }
};
