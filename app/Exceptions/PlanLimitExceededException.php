<?php

namespace App\Exceptions;

use Exception;

class PlanLimitExceededException extends Exception
{
    public function __construct(string $feature, int $limit)
    {
        parent::__construct(
            "Limite du plan atteinte pour « {$feature} » (maximum : {$limit}). Passez à un plan supérieur pour continuer."
        );
    }
}
