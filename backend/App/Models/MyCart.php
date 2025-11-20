<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MyCart extends Model
{
    use HasFactory;

    protected $table = 'my_cart';
    protected $primaryKey = 'cart_id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'user_id',
        'meal_id',
        'item_name',
        'price',
        'quantity',
    ];

    public function meal()
    {
        return $this->belongsTo(Meal::class, 'meal_id', 'MealID');
    }
}
