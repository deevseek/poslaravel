<?php

namespace App\Services;

use App\Exceptions\FaceRecognitionException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class FaceRecognitionService
{
    private string $baseUrl;
    private int $timeout;

    public function __construct()
    {
        $this->baseUrl = rtrim(
            (string) config('services.face_recognition.base_url', 'http://127.0.0.1:8000'),
            '/',
        );
        $this->timeout = (int) config('services.face_recognition.timeout', 20);
    }

    public function registerFace(int|string $employeeId, string $imagePath): array
    {
        return $this->sendRequest('/register-face', ['user_id' => (string) $employeeId], $imagePath);
    }

    public function verifyFace(int|string $employeeId, string $imagePath): array
    {
        return $this->sendRequest('/verify-face', ['user_id' => (string) $employeeId], $imagePath);
    }

    public function identifyFace(string $imagePath): array
    {
        return $this->sendRequest('/identify-face', [], $imagePath);
    }

    private function sendRequest(string $endpoint, array $fields, string $imagePath): array
    {
        $url = $this->baseUrl . $endpoint;

        if (! is_file($imagePath)) {
            throw new FaceRecognitionException('Snapshot wajah tidak ditemukan.', null, [
                'error' => 'snapshot_missing',
            ]);
        }

        try {
            $response = Http::timeout($this->timeout)
                ->attach('image', fopen($imagePath, 'rb'), $this->guessFilename($imagePath))
                ->post($url, $fields);
        } catch (ConnectionException $exception) {
            throw new FaceRecognitionException('Layanan face recognition tidak tersedia.', null, [
                'error' => 'service_unavailable',
            ], $exception);
        }

        if (! $response->ok()) {
            $payload = $response->json();
            $message = $this->extractErrorMessage($payload) ?? 'Permintaan face recognition gagal.';

            throw new FaceRecognitionException($message, $response->status(), [
                'error' => $payload['error'] ?? null,
                'response' => $payload,
            ]);
        }

        $payload = $response->json();
        if (! is_array($payload)) {
            throw new FaceRecognitionException('Respon face recognition tidak valid.', $response->status(), [
                'error' => 'invalid_response',
            ]);
        }

        return $payload;
    }

    private function extractErrorMessage(mixed $payload): ?string
    {
        if (! is_array($payload)) {
            return null;
        }

        $detail = $payload['detail'] ?? $payload['message'] ?? null;
        if (is_string($detail)) {
            return $detail;
        }

        if (is_array($detail)) {
            if (isset($detail['reason']) && is_string($detail['reason'])) {
                return $detail['reason'];
            }

            if (isset($detail['message']) && is_string($detail['message'])) {
                return $detail['message'];
            }
        }

        return null;
    }

    private function guessFilename(string $imagePath): string
    {
        $extension = pathinfo($imagePath, PATHINFO_EXTENSION);
        if (! $extension) {
            $extension = 'jpg';
        }

        return Str::uuid() . '.' . $extension;
    }
}
