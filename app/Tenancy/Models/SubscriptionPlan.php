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

    public function getFeatureLabelsAttribute(): array
    {
        $featureOptions = collect(config('modules.subscription_features', []))
            ->mapWithKeys(fn (array $feature) => [$feature['value'] => $feature['label']]);

        return collect($this->features ?? [])
            ->map(fn (string $feature) => $featureOptions->get($feature, $feature))
            ->values()
            ->all();
    }

    public function getConnectionName()
    {
        return config('tenancy.central_connection', 'mysql');
    }
}
