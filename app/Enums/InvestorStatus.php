<?php

namespace App\Enums;

/**
 * Enum representing investor status types.
 *
 * This enum defines the status of an investor in a meeting (pending, confirmed, refused, attended, absent),
 * along with helper methods to list all available statuses and get human-readable labels.
 *
 * @method static InvestorStatus PENDING() Pending status
 * @method static InvestorStatus CONFIRMED() Confirmed status
 * @method static InvestorStatus REFUSED() Refused status
 * @method static InvestorStatus ATTENDED() Attended status
 * @method static InvestorStatus ABSENT() Absent status
 * @method static array all() Returns an array of all available status types
 * @method string label() Returns a human-readable label for the status
 * @method string color() Returns the CSS color class for the status badge
 */
enum InvestorStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case REFUSED = 'refused';
    case ATTENDED = 'attended';
    case ABSENT = 'absent';

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
            self::ATTENDED,
            self::ABSENT,
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
            self::ATTENDED => 'Attended',
            self::ABSENT => 'Absent',
        };
    }

    /**
     * Get the CSS color class for the status badge.
     *
     * @return string
     */
    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::CONFIRMED => 'info',
            self::REFUSED => 'error',
            self::ATTENDED => 'success',
            self::ABSENT => 'neutral',
        };
    }
}
