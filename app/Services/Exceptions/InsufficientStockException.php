<?php

namespace App\Services\Exceptions;

class InsufficientStockException extends BusinessLogicException
{
    public function __construct()
    {
        parent::__construct('Stock insuficiente para registrar la salida.');
    }
}
