<?php

namespace App\Enums;

enum SprintStatus: string
{
    case PLANNING = 'planning';
    case ACTIVE = 'active';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::PLANNING => 'Planejamento',
            self::ACTIVE => 'Ativo',
            self::COMPLETED => 'Concluído',
        };
    }

    public function isPlanning(): bool
    {
        return $this === self::PLANNING;
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isCompleted(): bool
    {
        return $this === self::COMPLETED;
    }
}
