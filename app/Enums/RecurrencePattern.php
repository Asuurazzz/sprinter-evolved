<?php

namespace App\Enums;

enum RecurrencePattern: string
{
    case DAILY = 'daily';
    case WEEKLY = 'weekly';
    case MONTHLY = 'monthly';
    case CUSTOM = 'custom';

    public function label(): string
    {
        return match ($this) {
            self::DAILY => 'Diário',
            self::WEEKLY => 'Semanal',
            self::MONTHLY => 'Mensal',
            self::CUSTOM => 'Personalizado',
        };
    }

    public function isDaily(): bool
    {
        return $this === self::DAILY;
    }

    public function isWeekly(): bool
    {
        return $this === self::WEEKLY;
    }

    public function isMonthly(): bool
    {
        return $this === self::MONTHLY;
    }

    public function isCustom(): bool
    {
        return $this === self::CUSTOM;
    }
}
