<?php

namespace App\Modules\Attendance\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class FaceVerificationService
{
    public function verify(UploadedFile $image): FaceVerificationResult
    {
        $url = config('attendance.face_api_url');
        $timeout = (int) config('attendance.timeout');

        try {
            $response = Http::timeout($timeout)
                ->acceptJson()
                ->attach('image', file_get_contents($image->getRealPath()), $image->getClientOriginalName())
                ->post($url);
        } catch (ConnectionException $exception) {
            throw new RuntimeException('Face API connection failed.', 0, $exception);
        }

        if (! $response->successful()) {
            throw new RuntimeException('Face API returned an error.', $response->status());
        }

        $payload = $response->json();

        if (! is_array($payload) || ! array_key_exists('matched', $payload) || ! array_key_exists('confidence', $payload)) {
            throw new RuntimeException('Face API returned an invalid response.');
        }

        return new FaceVerificationResult(
            (bool) $payload['matched'],
            (float) $payload['confidence'],
        );
    }
}
