<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryTable extends Migration
{
    public function up()
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->increments('InventoryID'); 

            $table->unsignedBigInteger('UserID'); 
            $table->unsignedInteger('MealID');

            $table->integer('quantity');
            $table->timestamps(); 
            $table->string('change')->nullable();

            
            $table->foreign('UserID')->references('UserID')->on('users')->onDelete('cascade');
            $table->foreign('MealID')->references('MealID')->on('meals')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory');
    }
}
