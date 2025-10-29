<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchasesTable extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->increments('PurchasesID');


            $table->unsignedBigInteger('UserID');
            $table->foreign('UserID')->references('UserID')->on('users')->onDelete('cascade');

            $table->unsignedInteger('MealID');
            $table->foreign('MealID')->references('MealID')->on('meals')->onDelete('cascade');

            $table->integer('quantity');
            $table->decimal('total_price', 8, 2);
            $table->dateTime('datetime');
            $table->string('payment_method');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
}
