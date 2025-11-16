<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'UserID';
    public $incrementing = true;
    protected $keyType = 'int';

    // Fushtat që mund të plotësohen me mass assignment
    protected $fillable = [
        'username',
        'name',
        'surname',
        'email',
        'password',
        'RoleID',
        'phone',
        'address',
        'dob',
        'gender',
        'status',
        'fitness_goal',
        'activity_level',
        'training_days',
        'focus_area',
    ];

    // Fushat që nuk shfaqen në JSON
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Type casting
    protected $casts = [
        'email_verified_at' => 'datetime',
        'dob' => 'date',
    ];

    /**
     * JWT Methods
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    public function favorites()
{
    return $this->belongsToMany(Meal::class, 'user_favorite_meals', 'user_id', 'meal_id');
}

public function personalisedMeals()
{
    return $this->belongsToMany(Meal::class, 'user_personalised_meals', 'user_id', 'meal_id');
}

}
