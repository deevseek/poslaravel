<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'message',
        'type',
        'status',
    ];

    public function getWaMeUrlAttribute(): string
    {
        $phone = preg_replace('/\\D+/', '', $this->phone ?? '');
        $message = $this->message ?? '';
        $query = $message !== '' ? '?text=' . urlencode($message) : '';

        return $phone !== '' ? "https://wa.me/{$phone}{$query}" : '';
    }
}
