<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\WaLog;

class WhatsAppService
{
    public function sendMessage(string $phone, string $message, string $type = 'service'): bool
    {
        $status = 'failed';
        $enabled = filter_var(Setting::getValue(Setting::WHATSAPP_ENABLED, false), FILTER_VALIDATE_BOOLEAN);

        if ($enabled) {
            $status = 'sent';
        }

        WaLog::create([
            'phone' => $phone,
            'message' => $message,
            'type' => $type,
            'status' => $status,
        ]);

        return $status === 'sent';
    }
}
