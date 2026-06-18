<?php

namespace App\Services\Exceptions;

use Exception;

class BusinessLogicException extends Exception
{
    public function __construct(string $message = 'Business logic error', int $code = 0, ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
