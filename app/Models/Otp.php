<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Otp extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'code_hash',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
