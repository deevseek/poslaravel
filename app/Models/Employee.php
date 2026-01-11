<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'position',
        'email',
        'phone',
        'address',
        'join_date',
        'base_salary',
        'is_active',
        'retina_signature',
        'retina_registered_at',
        'retina_scan_path',
    ];

    protected $casts = [
        'join_date' => 'date',
        'base_salary' => 'decimal:2',
        'is_active' => 'boolean',
        'retina_registered_at' => 'datetime',
    ];

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
