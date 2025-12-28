<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StaffSchedule extends Model
{
    use HasFactory;

    protected $table = 'staff_schedule';
    protected $primaryKey = 'ScheduleID';

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'UserID',
        'day',
        'start_time',
        'end_time',
        'RoleID',
        'isAvailable',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'UserID');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'RoleID', 'RoleID');
    }
}
