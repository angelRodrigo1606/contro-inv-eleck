<?php

namespace App\Services\Exceptions;

class DependencyException extends BusinessLogicException
{
    public function __construct(string $resource)
    {
        parent::__construct("No se puede eliminar {$resource} porque tiene registros asociados.");
    }
}
