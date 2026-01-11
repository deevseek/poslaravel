<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class FaceRecognitionException extends RuntimeException
{
    public function __construct(
        string $message,
        private readonly ?int $statusCode = null,
        private readonly array $context = [],
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $statusCode ?? 0, $previous);
    }

    public function statusCode(): ?int
    {
        return $this->statusCode;
    }

    public function context(): array
    {
        return $this->context;
    }
}
