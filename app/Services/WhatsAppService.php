<?php

namespace App\Services;

class WhatsAppService
{
    public function normalizePhone(?string $phone): ?string
    {
        if (! $phone) {
            return null;
        }

        $digits = preg_replace('/\D+/', '', $phone);

        if (! $digits) {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        return $digits;
    }

    public function buildLink(?string $phone, string $message): ?string
    {
        $normalized = $this->normalizePhone($phone);

        if (! $normalized) {
            return null;
        }

        return 'https://wa.me/' . $normalized . '?text=' . urlencode($message);
    }
}
