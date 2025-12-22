<?php

namespace App\Tenancy\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    protected $fillable = [
        'name',
        'code',
        'price',
        'billing_cycle',
        'features',
    ];

    protected $casts = [
        'features' => 'array',
    ];

    public function getConnectionName()
    {
        return config('tenancy.central_connection', 'mysql');
    }
}
