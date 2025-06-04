<?php

namespace App\Enums;

/**
 * Enum representing origin types.
 *
 * This enum defines whether an entity is of national or foreign origin,
 * along with helper methods to list all available types and get human-readable labels.
 *
 * @method static Origin NATIONAL() National origin
 * @method static Origin FOREIGN() Foreign origin
 * @method static array all() Returns an array of all available origin types
 * @method string label() Returns a human-readable label for the origin type
 */
enum Origin: string
{
    case NATIONAL = 'national';
    case FOREIGN = 'foreign';

    /**
     * Get all available origin types.
     *
     * @return array
     */
    public static function all(): array
    {
        return [
            self::NATIONAL,
            self::FOREIGN,
        ];
    }

    /**
     * Get a human-readable label for the origin type.
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::NATIONAL => 'National',
            self::FOREIGN => 'Foreign',
        };
    }
}
