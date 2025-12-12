<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Service extends Model
{
    use HasFactory;

    public const STATUS_MENUNGGU = 'menunggu';
    public const STATUS_DIKERJAKAN = 'dikerjakan';
    public const STATUS_SELESAI = 'selesai';
    public const STATUS_DIAMBIL = 'diambil';

    public const STATUSES = [
        self::STATUS_MENUNGGU,
        self::STATUS_DIKERJAKAN,
        self::STATUS_SELESAI,
        self::STATUS_DIAMBIL,
    ];

    protected $fillable = [
        'customer_id',
        'transaction_id',
        'device',
        'complaint',
        'diagnosis',
        'notes',
        'service_fee',
        'warranty_days',
        'status',
    ];

    protected $casts = [
        'service_fee' => 'decimal:2',
        'warranty_days' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ServiceItem::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(ServiceLog::class)->latest();
    }

    public function totalItems(): float
    {
        return (float) $this->items()->sum('total');
    }

    public function addLog(string $message): void
    {
        $this->logs()->create([
            'message' => $message,
            'user_id' => Auth::id(),
        ]);
    }
}
