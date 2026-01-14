<?php

namespace App\Enums;

enum TaskDependencyType: string
{
    case BLOCKS = 'blocks';
    case BLOCKED_BY = 'blocked_by';
    case RELATED = 'related';

    public function label(): string
    {
        return match ($this) {
            self::BLOCKS => 'Bloqueia',
            self::BLOCKED_BY => 'Bloqueada por',
            self::RELATED => 'Relacionada',
        };
    }

    public function isBlocks(): bool
    {
        return $this === self::BLOCKS;
    }

    public function isBlockedBy(): bool
    {
        return $this === self::BLOCKED_BY;
    }

    public function isRelated(): bool
    {
        return $this === self::RELATED;
    }
}
