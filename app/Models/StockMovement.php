<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    public const TYPE_IN = 'in';
    public const TYPE_OUT = 'out';

    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'note',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (StockMovement $movement) {
            $movement->quantity = abs((int) $movement->quantity);
        });

        static::created(function (StockMovement $movement) {
            $movement->applyStockChange($movement->type, $movement->quantity);
        });

        static::updating(function (StockMovement $movement) {
            $movement->revertStockChange($movement->getOriginal('type'), (int) $movement->getOriginal('quantity'));
        });

        static::updated(function (StockMovement $movement) {
            $movement->applyStockChange($movement->type, $movement->quantity);
        });

        static::deleting(function (StockMovement $movement) {
            $movement->revertStockChange($movement->type, $movement->quantity);
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected function applyStockChange(string $type, int $quantity): void
    {
        $difference = $type === self::TYPE_IN ? $quantity : -$quantity;
        $this->adjustProductStock($difference);
    }

    protected function revertStockChange(string $type, int $quantity): void
    {
        $difference = $type === self::TYPE_IN ? -$quantity : $quantity;
        $this->adjustProductStock($difference);
    }

    protected function adjustProductStock(int $difference): void
    {
        $product = $this->product;

        if (! $product) {
            return;
        }

        $product->stock = max(0, $product->stock + $difference);
        $product->save();
    }
}
