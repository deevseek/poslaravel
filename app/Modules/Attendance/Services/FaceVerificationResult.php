<?php

namespace App\Modules\Attendance\Services;

class FaceVerificationResult
{
    public function __construct(
        public readonly bool $matched,
        public readonly float $confidence,
    ) {
    }
}
