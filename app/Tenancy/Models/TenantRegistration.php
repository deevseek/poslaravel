<?php

namespace App\Tenancy\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class TenantRegistration extends Model
{
    protected $fillable = [
        'name',
        'subdomain',
        'admin_name',
        'email',
        'phone',
        'plan_id',
        'status',
        'payment_method',
        'payment_reference',
        'payment_amount',
        'payment_proof_path',
        'password_encrypted',
        'approved_at',
        'rejected_at',
        'admin_note',
    ];

    protected $casts = [
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
        'payment_amount' => 'decimal:2',
    ];

    public function getConnectionName()
    {
        return config('tenancy.central_connection', 'mysql');
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    public function paymentProofUrl(): ?string
    {
        if (! $this->payment_proof_path) {
            return null;
        }

        return Storage::disk('public')->url($this->payment_proof_path);
    }
}
