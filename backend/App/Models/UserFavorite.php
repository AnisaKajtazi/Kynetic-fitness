<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFavorite extends Model
{
    use HasFactory;

    protected $table = 'user_favorites';
    protected $fillable = ['user_id', 'exercise_id'];

   
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'UserID');
    }

 
    public function exercise()
    {
        return $this->belongsTo(Exercise::class, 'exercise_id', 'ExerciseID');
    }
}
