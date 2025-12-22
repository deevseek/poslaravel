<?php

namespace App\Tenancy\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subdomain',
        'database_name',
        'status',
        'plan_id',
    ];

    public function getConnectionName()
    {
        return config('tenancy.central_connection', 'mysql');
    }
}
