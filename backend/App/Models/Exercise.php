<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    use HasFactory;

    protected $table = 'exercises';
    protected $primaryKey = 'ExerciseID';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
        'duration',
        'image',
    ];


    public function favoritedBy()
    {
        return $this->hasMany(UserFavorite::class, 'exercise_id', 'ExerciseID');
    }
}
