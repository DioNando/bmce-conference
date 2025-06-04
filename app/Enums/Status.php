<?php

namespace App\Enums;

/**
 * Enum representing status types.
 *
 * This enum defines the status of an entity (pending, confirmed, refused),
 * along with helper methods to list all available statuses and get human-readable labels.
 *
 * @method static Status PENDING() Pending status
 * @method static Status CONFIRMED() Confirmed status
 * @method static Status REFUSED() Refused status
 * @method static array all() Returns an array of all available status types
 * @method string label() Returns a human-readable label for the status
 */
enum Status: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case REFUSED = 'refused';

    /**
     * Get all available status types.
     *
     * @return array
     */
    public static function all(): array
    {
        return [
            self::PENDING,
            self::CONFIRMED,
            self::REFUSED,
        ];
    }

    /**
     * Get a human-readable label for the status.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::CONFIRMED => 'Confirmed',
            self::REFUSED => 'Refused',
        };
    }
}
