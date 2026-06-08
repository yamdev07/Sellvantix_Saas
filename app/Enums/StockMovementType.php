<?php

namespace App\Enums;

enum StockMovementType: string
{
    case Entry = 'entree';
    case Exit  = 'sortie';

    public function label(): string
    {
        return match($this) {
            self::Entry => 'Entrée',
            self::Exit  => 'Sortie',
        };
    }

    public function cssClass(): string
    {
        return match($this) {
            self::Entry => 'text-green-600 bg-green-100',
            self::Exit  => 'text-red-600 bg-red-100',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::Entry => 'bi-arrow-down-circle',
            self::Exit  => 'bi-arrow-up-circle',
        };
    }

    public function signedQuantity(int $qty): string
    {
        return ($this === self::Entry ? '+' : '-') . ' ' . $qty;
    }

    public function opposite(): self
    {
        return $this === self::Entry ? self::Exit : self::Entry;
    }
}
