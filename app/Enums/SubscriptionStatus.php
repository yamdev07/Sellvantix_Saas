<?php

namespace App\Enums;

enum SubscriptionStatus: string
{
    case Trial     = 'trial';
    case Active    = 'active';
    case Expired   = 'expired';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::Trial     => "Période d'essai",
            self::Active    => 'Abonnement actif',
            self::Expired   => 'Abonnement expiré',
            self::Cancelled => 'Résilié',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Trial     => 'yellow',
            self::Active    => 'green',
            self::Expired   => 'red',
            self::Cancelled => 'gray',
        };
    }

    public function hasAccess(): bool
    {
        return in_array($this, [self::Trial, self::Active]);
    }
}
