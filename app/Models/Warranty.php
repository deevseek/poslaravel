<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warranty extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_CLAIMED = 'claimed';

    public const TYPE_PRODUCT = 'product';
    public const TYPE_SERVICE = 'service';

    protected $fillable = [
        'type',
        'reference_id',
        'customer_id',
        'start_date',
        'end_date',
        'description',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public static function statuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_EXPIRED,
            self::STATUS_CLAIMED,
        ];
    }

    public static function types(): array
    {
        return [
            self::TYPE_PRODUCT,
            self::TYPE_SERVICE,
        ];
    }

    public static function refreshExpired(): void
    {
        static::where('status', '!=', self::STATUS_EXPIRED)
            ->whereDate('end_date', '<', now()->toDateString())
            ->update(['status' => self::STATUS_EXPIRED]);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function claims(): HasMany
    {
        return $this->hasMany(WarrantyClaim::class);
    }
}
