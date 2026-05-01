<?php

namespace App\Services\Raiida\Exceptions;

use RuntimeException;

class RaiidaApiException extends RuntimeException
{
    public function __construct(
        string $message,
        private readonly int $statusCode = 500
    ) {
        parent::__construct($message);
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }
}
