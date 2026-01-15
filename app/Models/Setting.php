<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    public const STORE_NAME = 'store_name';
    public const STORE_ADDRESS = 'store_address';
    public const STORE_PHONE = 'store_phone';
    public const STORE_HOURS = 'store_hours';
    public const TRANSACTION_PREFIX = 'transaction_prefix';
    public const TRANSACTION_PADDING = 'transaction_padding';
    public const STORE_LOGO_PATH = 'store_logo_path';
    public const WHATSAPP_ENABLED = 'whatsapp_enabled';
    public const HRD_WORK_START = 'hrd_work_start';
    public const HRD_WORK_END = 'hrd_work_end';

    protected $fillable = [
        'key',
        'value',
    ];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        return static::query()->where('key', $key)->value('value') ?? $default;
    }

    public static function setValue(string $key, mixed $value): self
    {
        return static::updateOrCreate(['key' => $key], ['value' => (string) $value]);
    }
}
