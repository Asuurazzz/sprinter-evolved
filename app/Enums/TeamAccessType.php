<?php

namespace App\Enums;

enum TeamAccessType: string
{
    case PRIVATE = 'private';
    case PUBLIC = 'public';

    public function label(): string
    {
        return match ($this) {
            self::PRIVATE => 'Privado',
            self::PUBLIC => 'Público',
        };
    }

    public function isPrivate(): bool
    {
        return $this === self::PRIVATE;
    }

    public function isPublic(): bool
    {
        return $this === self::PUBLIC;
    }
}
