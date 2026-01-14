<?php

namespace App\Enums;

enum TaskCreationPermission: string
{
    case ALL = 'all';
    case MODERATORS_ONLY = 'moderators_only';

    public function label(): string
    {
        return match ($this) {
            self::ALL => 'Todos',
            self::MODERATORS_ONLY => 'Apenas Moderadores',
        };
    }

    public function isAll(): bool
    {
        return $this === self::ALL;
    }

    public function isModeratorsOnly(): bool
    {
        return $this === self::MODERATORS_ONLY;
    }
}
