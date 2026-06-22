<?php

namespace App\Services\Exceptions;

class InsufficientStockException extends BusinessLogicException
{
    public function __construct(int $availableStock = 0)
    {
        parent::__construct(
            sprintf(
                'Stock insuficiente para registrar la salida. Stock disponible: %d.',
                $availableStock
            )
        );
    }
}
