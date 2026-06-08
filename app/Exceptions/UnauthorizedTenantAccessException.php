<?php

namespace App\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UnauthorizedTenantAccessException extends HttpException
{
    public function __construct(string $resource = 'ressource')
    {
        parent::__construct(403, "Vous n'avez pas accès à cette {$resource}.");
    }
}
