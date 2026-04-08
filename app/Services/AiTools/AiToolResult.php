<?php

namespace App\Services\AiTools;

/**
 * Verdi-objekt som hvert tool returnerer fra execute().
 * Standardisert format slik at executor og UI kan vise resultatet konsistent.
 */
class AiToolResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly array $data = [],
        public readonly ?string $errorCode = null,
    ) {
    }

    public static function success(string $message, array $data = []): self
    {
        return new self(true, $message, $data);
    }

    public static function failure(string $message, ?string $errorCode = null, array $data = []): self
    {
        return new self(false, $message, $data, $errorCode);
    }

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'data' => $this->data,
            'error_code' => $this->errorCode,
        ];
    }
}
