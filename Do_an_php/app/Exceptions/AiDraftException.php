<?php

namespace App\Exceptions;

use RuntimeException;

class AiDraftException extends RuntimeException
{
    public function __construct(
        string $message,
        protected int $statusCode = 422,
    ) {
        parent::__construct($message);
    }

    /** Mã HTTP phù hợp trả về cho client (422 / 429 / 503). */
    public function status(): int
    {
        return $this->statusCode;
    }
}
