<?php

namespace App\Enums;

enum TaskStatus: string
{
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Ativa',
            self::COMPLETED => 'Concluída',
            self::ARCHIVED => 'Arquivada',
        };
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isCompleted(): bool
    {
        return $this === self::COMPLETED;
    }

    public function isArchived(): bool
    {
        return $this === self::ARCHIVED;
    }
}
