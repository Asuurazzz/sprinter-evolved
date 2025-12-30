<?php

namespace App\Enums;

enum Theme: string
{
    case LIGHT = 'light';
    case DARK = 'dark';
    case SYSTEM = 'system';

    public function label(): string
    {
        return match ($this) {
            self::LIGHT => 'Claro',
            self::DARK => 'Escuro',
            self::SYSTEM => 'Sistema',
        };
    }
}
