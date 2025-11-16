<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    protected $table = 'meals';
    protected $primaryKey = 'MealID'; 
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'name',
        'description',
        'category',
        'image',
        'calories',
        'fitness_goal',
        'activity_level',
        'training_days',
        'focus_area',
        'price',
    ];

    protected $casts = [
        'price' => 'float',
    ];


    public function favoritedBy()
    {
        return $this->belongsToMany(
            User::class,
            'user_favorite_meals',
            'meal_id',
            'user_id'
        );
    }

    public function personalisedBy()
    {
        return $this->belongsToMany(
            User::class,
            'user_personalised_meals',
            'meal_id',
            'user_id'
        );
    }
}
