<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->bigIncrements('OrderItemID');
            $table->unsignedBigInteger('order_id');
            $table->unsignedInteger('meal_id');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->foreign('order_id')->references('OrderID')->on('orders')->onDelete('cascade');
            $table->foreign('meal_id')->references('MealID')->on('meals')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

