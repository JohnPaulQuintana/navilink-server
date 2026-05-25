<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceSession extends Model
{
    protected $fillable = [
        'user_id',
        'device_name',
        'ip_address',
        'user_agent',
        'refresh_token',
        'expires_at',
        'last_used_at',
    ];
}
