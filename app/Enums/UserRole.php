<?php

namespace App\Enums;

enum UserRole: string
{
    case SuperAdminGlobal = 'super_admin_global';
    case SuperAdmin       = 'super_admin';
    case Admin            = 'admin';
    case Manager          = 'manager';
    case Cashier          = 'cashier';
    case Storekeeper      = 'storekeeper';

    public function label(): string
    {
        return match($this) {
            self::SuperAdminGlobal => 'Super Admin Global',
            self::SuperAdmin       => 'Super Admin',
            self::Admin            => 'Administrateur',
            self::Manager          => 'Gérant',
            self::Cashier          => 'Caissier',
            self::Storekeeper      => 'Magasinier',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::SuperAdminGlobal => 'purple',
            self::SuperAdmin       => 'violet',
            self::Admin            => 'red',
            self::Manager          => 'blue',
            self::Cashier          => 'green',
            self::Storekeeper      => 'orange',
        };
    }

    public function canManageStock(): bool
    {
        return in_array($this, [
            self::SuperAdminGlobal,
            self::SuperAdmin,
            self::Admin,
            self::Manager,
            self::Storekeeper,
        ]);
    }

    public function canManageSales(): bool
    {
        return in_array($this, [
            self::SuperAdminGlobal,
            self::SuperAdmin,
            self::Admin,
            self::Manager,
            self::Cashier,
        ]);
    }

    public function canManageUsers(): bool
    {
        return in_array($this, [
            self::SuperAdminGlobal,
            self::SuperAdmin,
        ]);
    }

    public static function assignableBy(self $assignerRole): array
    {
        return match($assignerRole) {
            self::SuperAdmin => [self::Admin, self::Manager, self::Cashier, self::Storekeeper],
            self::Admin      => [self::Manager, self::Cashier, self::Storekeeper],
            default          => [],
        };
    }
}
