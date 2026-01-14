<?php

namespace App\Enums;

enum BoardVisibility: string
{
    case PUBLIC = 'public';
    case PRIVATE = 'private';
    case RESTRICTED = 'restricted';

    public function label(): string
    {
        return match ($this) {
            self::PUBLIC => 'Público',
            self::PRIVATE => 'Privado',
            self::RESTRICTED => 'Restrito',
        };
    }

    public function isPublic(): bool
    {
        return $this === self::PUBLIC;
    }

    public function isPrivate(): bool
    {
        return $this === self::PRIVATE;
    }

    public function isRestricted(): bool
    {
        return $this === self::RESTRICTED;
    }
}
