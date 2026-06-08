<?php

namespace App\Exceptions;

use Exception;

class InsufficientStockException extends Exception
{
    public function __construct(string $productName, int $available, int $requested)
    {
        parent::__construct(
            "Stock insuffisant pour '{$productName}'. Disponible : {$available}, Demandé : {$requested}."
        );
    }
}
