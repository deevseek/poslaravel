<?php

namespace App\Modules\Attendance\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendance_logs';

    protected $fillable = [
        'user_id',
        'type',
        'confidence',
        'captured_at',
        'ip_address',
        'device_info',
        'created_at',
    ];

    protected $casts = [
        'captured_at' => 'datetime',
        'created_at' => 'datetime',
        'confidence' => 'float',
    ];

    public const UPDATED_AT = null;
}
