<?php

namespace App\Exceptions;

use RuntimeException;
use Throwable;

class FaceApiUnavailableException extends RuntimeException
{
    public function __construct(string $message = 'Face API unavailable.', ?Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
