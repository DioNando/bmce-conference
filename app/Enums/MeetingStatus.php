<?php

namespace App\Enums;

/**
 * Enum representing meeting statuses in the system.
 *
 * This enum defines the different states a meeting can have,
 * along with helper methods to list all available statuses and get human-readable labels.
 */
enum MeetingStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case CANCELLED = 'cancelled';
    case SCHEDULED = 'scheduled';
    case COMPLETED = 'completed';
    case DECLINED = 'declined';

    public static function all(): array
    {
        return [
            self::SCHEDULED,
            self::PENDING,
            self::CONFIRMED,
            self::CANCELLED,
            self::COMPLETED,
            self::DECLINED,
        ];
    }

    public function label(): string
    {
        return match ($this) {
            self::SCHEDULED => 'Scheduled',
            self::PENDING => 'Pending',
            self::CONFIRMED => 'Confirmed',
            self::CANCELLED => 'Cancelled',
            self::COMPLETED => 'Completed',
            self::DECLINED => 'Declined',
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
            self::SCHEDULED => 'info',
            self::PENDING => 'warning',
            self::CONFIRMED => 'success',
            self::CANCELLED => 'error',
            self::COMPLETED => 'secondary',
            self::DECLINED => 'error',
        };
    }
}
