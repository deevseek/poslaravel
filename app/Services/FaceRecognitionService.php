<?php

namespace App\Services;

use Freearhey\LaravelFaceDetection\Facades\FaceDetection;

class FaceRecognitionService
{
    public function hasFace(string $imagePath): bool
    {
        return ! empty($this->detectFaces($imagePath));
    }

    public function extractSignature(string $imagePath): ?array
    {
        $faces = $this->detectFaces($imagePath);

        if (empty($faces)) {
            return null;
        }

        $face = $faces[0];
        $signature = null;

        if (is_array($face)) {
            foreach (['descriptor', 'embedding', 'signature', 'encoding'] as $key) {
                if (array_key_exists($key, $face)) {
                    $signature = $face[$key];
                    break;
                }
            }
        } elseif (is_object($face)) {
            foreach (['descriptor', 'embedding', 'signature', 'encoding'] as $key) {
                if (isset($face->{$key})) {
                    $signature = $face->{$key};
                    break;
                }
            }
        }

        if (is_string($signature)) {
            $decoded = json_decode($signature, true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return is_array($signature) ? $signature : null;
    }

    public function matchSnapshot(string $referencePath, ?string $referenceSignature, string $candidatePath): bool
    {
        if ($this->supportsImageComparison()) {
            return $this->compareByImage($referencePath, $candidatePath);
        }

        if (! $referenceSignature) {
            return false;
        }

        $candidateSignature = $this->extractSignature($candidatePath);
        $referenceVector = json_decode($referenceSignature, true);

        if (! is_array($referenceVector) || ! is_array($candidateSignature)) {
            return false;
        }

        return $this->compareSignatureVectors($referenceVector, $candidateSignature);
    }

    private function detectFaces(string $imagePath): array
    {
        if (! class_exists(FaceDetection::class)) {
            return [];
        }

        foreach (['detect', 'detectFaces'] as $method) {
            if (is_callable([FaceDetection::class, $method])) {
                $faces = FaceDetection::$method($imagePath);
                $normalized = $this->normalizeFaces($faces);
                if ($normalized !== null) {
                    return $normalized;
                }
            }
        }

        return [];
    }

    private function normalizeFaces(mixed $faces): ?array
    {
        if (is_string($faces)) {
            $decoded = json_decode($faces, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $this->normalizeFaces($decoded);
            }
        }

        if (is_object($faces)) {
            if (method_exists($faces, 'toArray')) {
                return $this->normalizeFaces($faces->toArray());
            }

            if ($faces instanceof \JsonSerializable) {
                return $this->normalizeFaces($faces->jsonSerialize());
            }
        }

        if (! is_array($faces)) {
            return null;
        }

        if (array_is_list($faces)) {
            return $faces;
        }

        foreach (['faces', 'detections', 'results', 'data'] as $key) {
            if (array_key_exists($key, $faces) && is_array($faces[$key])) {
                return $faces[$key];
            }
        }

        return $faces;
    }

    private function supportsImageComparison(): bool
    {
        if (! class_exists(FaceDetection::class)) {
            return false;
        }

        foreach (['compare', 'match', 'verify', 'compareFaces'] as $method) {
            if (is_callable([FaceDetection::class, $method])) {
                return true;
            }
        }

        return false;
    }

    private function compareByImage(string $referencePath, string $candidatePath): bool
    {
        foreach (['compare', 'match', 'verify', 'compareFaces'] as $method) {
            if (is_callable([FaceDetection::class, $method])) {
                return (bool) FaceDetection::$method($referencePath, $candidatePath);
            }
        }

        return false;
    }

    private function compareSignatureVectors(array $referenceVector, array $candidateVector): bool
    {
        if (count($referenceVector) !== count($candidateVector)) {
            return false;
        }

        $dot = 0.0;
        $referenceMagnitude = 0.0;
        $candidateMagnitude = 0.0;

        foreach ($referenceVector as $index => $value) {
            if (! isset($candidateVector[$index])) {
                return false;
            }

            $referenceValue = (float) $value;
            $candidateValue = (float) $candidateVector[$index];

            $dot += $referenceValue * $candidateValue;
            $referenceMagnitude += $referenceValue ** 2;
            $candidateMagnitude += $candidateValue ** 2;
        }

        if ($referenceMagnitude === 0.0 || $candidateMagnitude === 0.0) {
            return false;
        }

        $similarity = $dot / (sqrt($referenceMagnitude) * sqrt($candidateMagnitude));

        return $similarity >= 0.8;
    }
}
