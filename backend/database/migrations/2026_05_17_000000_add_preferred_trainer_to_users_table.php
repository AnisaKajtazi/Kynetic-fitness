<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('preferred_trainer_id')->nullable()->after('focus_area');
            $table->foreign('preferred_trainer_id')->references('UserID')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['preferred_trainer_id']);
            $table->dropColumn('preferred_trainer_id');
        });
    }
};
