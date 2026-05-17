<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Message extends Model
{
    protected $table = 'messages';

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'body',
        'read_at',
    ];

    protected $dates = ['read_at', 'created_at', 'updated_at'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'UserID');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id', 'UserID');
    }
}
