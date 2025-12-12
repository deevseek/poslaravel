<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\WaLog;
use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    public function sendMessage(string $phone, string $message, string $type = 'service'): bool
    {
        $status = 'failed';

        try {
            $enabled = filter_var(Setting::getValue(Setting::WHATSAPP_ENABLED, false), FILTER_VALIDATE_BOOLEAN);
            $gatewayUrl = Setting::getValue(Setting::WHATSAPP_GATEWAY_URL);

            if (! $enabled || ! $gatewayUrl) {
                throw new \RuntimeException('WhatsApp tidak aktif atau gateway belum diatur.');
            }

            $response = Http::asJson()->post($gatewayUrl, [
                'phone' => $phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                $status = 'sent';
            }
        } catch (\Throwable $exception) {
            report($exception);
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
