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
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    
    public function getJWTIdentifier()
    {
        return (string) $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

   
}
