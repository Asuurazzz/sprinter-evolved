<?php

namespace App\Enums;

enum UserStatus: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';
    case PENDING_VERIFICATION = 'pending_verification';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Ativo',
            self::INACTIVE => 'Inativo',
            self::SUSPENDED => 'Suspenso',
            self::PENDING_VERIFICATION => 'Pendente de Verificação',
        };
    }

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function canLogin(): bool
    {
        return in_array($this, [self::ACTIVE, self::PENDING_VERIFICATION]);
    }
}
