<?php

namespace App\Services\AiTools\Exceptions;

use Exception;

class ToolValidationException extends Exception
{
    public function __construct(string $message, public readonly array $errors = [])
    {
        parent::__construct($message);
    }
}
