<?php

namespace App\Exceptions;

use Exception;

class SaleCancellationException extends Exception
{
    public function __construct(string $reason = '')
    {
        parent::__construct(
            "Impossible d'annuler la vente" . ($reason !== '' ? " : {$reason}" : '.')
        );
    }
}
