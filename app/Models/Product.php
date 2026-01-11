<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'sku',
        'cost_price',
        'avg_cost',
        'price',
        'pricing_mode',
        'margin_percentage',
        'stock',
        'warranty_days',
        'description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'avg_cost' => 'decimal:2',
        'margin_percentage' => 'decimal:2',
        'stock' => 'integer',
        'warranty_days' => 'integer',
    ];

    public const PRICING_MODE_MANUAL = 'manual';
    public const PRICING_MODE_PERCENTAGE = 'percentage';

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public static function calculateSellingPrice(float $costPrice, float $marginPercentage): float
    {
        return round($costPrice + ($costPrice * $marginPercentage / 100), 2);
    }

    public static function generateSku(Category $category): string
    {
        $prefix = str_pad(Str::upper(Str::substr($category->name, 0, 3)), 3, 'X');
        $datePart = now()->format('ym');
        $basePattern = "$prefix-$datePart-";

        $latestSku = static::where('sku', 'like', "$basePattern%")
            ->orderByDesc('sku')
            ->value('sku');

        $nextSequence = 1;

        if ($latestSku) {
            $nextSequence = ((int) substr($latestSku, -4)) + 1;
        }

        return sprintf('%s%04d', $basePattern, $nextSequence);
    }
}
