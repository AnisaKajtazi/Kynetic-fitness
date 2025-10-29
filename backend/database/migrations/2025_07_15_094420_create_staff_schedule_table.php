<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffScheduleTable extends Migration
{
    public function up(): void
    {
        Schema::create('staff_schedule', function (Blueprint $table) {
            $table->increments('ScheduleID');
            $table->unsignedBigInteger('UserID');
            $table->string('day');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('RoleID');
            $table->boolean('isAvailable')->default(true);
            $table->timestamps();

            $table->foreign('UserID')->references('UserID')->on('users')->onDelete('cascade');
            $table->foreign('RoleID')->references('RoleID')->on('roles')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_schedule');
    }
}
