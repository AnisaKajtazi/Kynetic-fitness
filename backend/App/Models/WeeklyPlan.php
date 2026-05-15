<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyPlan extends Model
{
    protected $fillable = [
        'user_id',
        'week_start'
    ];

    protected $casts = [
        'week_start' => 'date',
    ];
    public function items()
    {
        return $this->hasMany(ExercisePlanItem::class, 'plan_id');
    }
}