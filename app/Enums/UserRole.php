<?php

namespace App\Enums;

/**
 * Enum representing user roles in the system.
 *
 * This enum defines the different types of users that can access the application,
 * along with helper methods to list all available roles and get human-readable labels.
 *
 * @method static UserRole ADMIN() Admin user with full system access
 * @method static UserRole INVESTOR() Investor user role
 * @method static UserRole ISSUER() Issuer user role
 * @method static array all() Returns an array of all available user roles
 * @method string label() Returns a human-readable French label for the role
 */
enum UserRole: string
{
    case ADMIN = 'admin';
    case INVESTOR = 'investor';
    case ISSUER = 'issuer';

    public static function all(): array
    {
        return [
            self::ADMIN,
            self::INVESTOR,
            self::ISSUER,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Administrator',
            self::INVESTOR => 'Investor',
            self::ISSUER => 'Issuer',
        };
    }
}
