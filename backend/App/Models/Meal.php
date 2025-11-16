<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meal extends Model
{
    use HasFactory;

    protected $table = 'meals';
    protected $primaryKey = 'MealID'; // sepse tabela ka MealID
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
    ];

    // Relacionet me users
    public function favoritedBy()
    {
        return $this->belongsToMany(
            User::class,
            'user_favorite_meals',
            'meal_id', // foreign key ne tabelen pivot per meal
            'user_id'  // foreign key ne tabelen pivot per user
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
