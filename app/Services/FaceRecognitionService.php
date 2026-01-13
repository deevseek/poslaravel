<?php

namespace App\Services;

use App\Exceptions\FaceApiBadResponseException;
use App\Exceptions\FaceApiUnavailableException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class FaceRecognitionService
{
    private string $verifyUrl;
    private string $healthUrl;
    private int $timeout;
    private ?string $lastHealthError = null;

    public function __construct()
    {
        $this->verifyUrl = (string) config('attendance.face_api_url');
        $this->healthUrl = (string) config('attendance.face_api_health_url');
        $this->timeout = (int) config('attendance.timeout', 20);
    }

    public function health(): bool
    {
        $this->lastHealthError = null;

        try {
            $response = Http::timeout($this->timeout)->get($this->healthUrl);
        } catch (ConnectionException $exception) {
            $this->lastHealthError = 'connection_failed';
            Log::warning('Face API health check connection failed.', [
                'exception' => $exception->getMessage(),
                'url' => $this->healthUrl,
            ]);

            return false;
        }

        if (! $response->ok()) {
            $this->lastHealthError = 'bad_response';
            Log::warning('Face API health check failed.', [
                'status' => $response->status(),
                'body' => $response->body(),
                'url' => $this->healthUrl,
            ]);

            return false;
        }

        return true;
    }

    public function lastHealthError(): ?string
    {
        return $this->lastHealthError;
    }

    public function verifyFace(string $base64Image, ?string $employeeId = null): array
    {
        $fields = array_filter([
            'user_id' => $employeeId !== null ? (string) $employeeId : null,
            'employee_id' => $employeeId !== null ? (string) $employeeId : null,
        ], static fn ($value) => $value !== null);

        return $this->postImage($this->verifyUrl, $base64Image, $fields, true);
    }

    public function registerFace(int|string $employeeId, string $base64Image): array
    {
        $url = $this->buildEndpointUrl('/register-face');

        return $this->postImage($url, $base64Image, [
            'user_id' => (string) $employeeId,
        ]);
    }

    public function identifyFace(string $base64Image): array
    {
        $url = $this->buildEndpointUrl('/identify-face');

        return $this->postImage($url, $base64Image, [], true);
    }

    private function postImage(string $url, string $base64Image, array $fields, bool $allowErrorPayload = false): array
    {
        [$decodedImage, $extension] = $this->decodeBase64Image($base64Image);
        $filename = Str::uuid() . '.' . $extension;

        try {
            $response = Http::timeout($this->timeout)
                ->attach('image', $decodedImage, $filename)
                ->post($url, $fields);
        } catch (ConnectionException $exception) {
            Log::warning('Face API connection failed.', [
                'exception' => $exception->getMessage(),
                'url' => $url,
            ]);

            throw new FaceApiUnavailableException('Face API unavailable.', $exception);
        }

        $payload = $response->json();

        if (! $response->ok()) {
            Log::warning('Face API bad response.', [
                'status' => $response->status(),
                'body' => $response->body(),
                'url' => $url,
            ]);

            if ($allowErrorPayload && is_array($payload)) {
                return $payload;
            }

            throw new FaceApiBadResponseException(
                'Face API returned a bad response.',
                $response->status(),
                $response->body(),
            );
        }
        if (! is_array($payload)) {
            Log::warning('Face API invalid JSON.', [
                'body' => $response->body(),
                'url' => $url,
            ]);

            throw new FaceApiBadResponseException('Face API returned invalid JSON.', $response->status(), $response->body());
        }

        return $payload;
    }

    private function decodeBase64Image(string $base64Image): array
    {
        if (! preg_match('/^data:image\/(png|jpeg|jpg|webp);base64,/', $base64Image, $matches)) {
            throw new FaceApiBadResponseException('Invalid image data.', 422, $base64Image);
        }

        $extension = match ($matches[1]) {
            'jpeg', 'jpg' => 'jpg',
            'webp' => 'webp',
            default => 'png',
        };

        $encodedImage = substr($base64Image, strpos($base64Image, ',') + 1);
        $decodedImage = base64_decode($encodedImage, true);

        if ($decodedImage === false) {
            throw new FaceApiBadResponseException('Invalid base64 payload.', 422, null);
        }

        return [$decodedImage, $extension];
    }

    private function buildEndpointUrl(string $path): string
    {
        $verifyUrl = rtrim($this->verifyUrl, '/');
        if (str_ends_with($verifyUrl, '/verify-face')) {
            return substr($verifyUrl, 0, -strlen('/verify-face')) . $path;
        }

        return $verifyUrl . $path;
    }
}
