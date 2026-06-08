<?php

namespace App\Enums;

enum SaleStatus: string
{
    case Completed = 'completed';
    case Pending   = 'pending';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Completed => 'Terminée',
            self::Pending   => 'En attente',
            self::Cancelled => 'Annulée',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Completed => 'green',
            self::Pending   => 'yellow',
            self::Cancelled => 'red',
        };
    }

    public function isCancellable(): bool
    {
        return $this !== self::Cancelled;
    }
}
