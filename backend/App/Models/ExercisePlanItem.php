<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExercisePlanItem extends Model
{
    protected $fillable = [
        'plan_id',
        'exercise_id',
        'day_of_week',
        'completed',
        'completed_at',
        'reps'
    ];
    protected $casts = [
        'completed' => 'boolean',
    ];

    public function exercise()
    {
        return $this->belongsTo(Exercise::class, 'exercise_id', 'ExerciseID');
    }
}