<?php
/**
 * Report status.
 */

namespace App\Entity\Enum;

/**
 * Enum ReportStatus.
 */
enum ReportStatus: int
{
    case STATUS_PENDING = 1;
    case STATUS_IN_PROGRESS = 2;
    case STATUS_COMPLETED = 3;
    case STATUS_ARCHIVED = 4;

    /**
     * Get the status label.
     *
     * @return string Status label
     */
    public function label(): string
    {
        return match ($this) {
            ReportStatus::STATUS_PENDING => 'label.pending',
            ReportStatus::STATUS_IN_PROGRESS => 'label.in_progress',
            ReportStatus::STATUS_COMPLETED => 'label.completed',
            ReportStatus::STATUS_ARCHIVED => 'label.archived',
        };
    }

    /**
     * Get random value.
     *
     * @return self Random value from enum
     */
    public static function getRandomValue(): self
    {
        $cases = self::cases();

        return $cases[array_rand($cases)];
    }
}
