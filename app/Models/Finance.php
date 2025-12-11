<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Finance extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_session_id',
        'type',
        'category',
        'nominal',
        'note',
        'recorded_at',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'recorded_at' => 'date',
    ];

    public function cashSession(): BelongsTo
    {
        return $this->belongsTo(CashSession::class);
    }
}
