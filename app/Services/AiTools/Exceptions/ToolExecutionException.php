<?php

namespace App\Services\AiTools\Exceptions;

use Exception;

class ToolExecutionException extends Exception
{
    public function __construct(string $message, public readonly ?string $errorCode = null)
    {
        parent::__construct($message);
    }
}
