<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case PLANNING = 'planning';
    case IN_PROGRESS = 'in_progress';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case ARCHIVED = 'archived';

    public function label(): string
    {
        return match ($this) {
            self::PLANNING => 'Planejamento',
            self::IN_PROGRESS => 'Em Progresso',
            self::COMPLETED => 'Concluído',
            self::CANCELLED => 'Cancelado',
            self::ARCHIVED => 'Arquivado',
        };
    }

    public function isPlanning(): bool
    {
        return $this === self::PLANNING;
    }

    public function isInProgress(): bool
    {
        return $this === self::IN_PROGRESS;
    }

    public function isCompleted(): bool
    {
        return $this === self::COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this === self::CANCELLED;
    }

    public function isArchived(): bool
    {
        return $this === self::ARCHIVED;
    }

    public function isActive(): bool
    {
        return in_array($this, [self::PLANNING, self::IN_PROGRESS], true);
    }
}
