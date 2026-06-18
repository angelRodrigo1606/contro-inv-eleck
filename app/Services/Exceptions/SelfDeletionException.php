<?php

namespace App\Services\Exceptions;

class SelfDeletionException extends BusinessLogicException
{
    public function __construct()
    {
        parent::__construct('No puedes eliminar tu propio usuario.');
    }
}
