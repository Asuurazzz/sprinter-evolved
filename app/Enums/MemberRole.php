<?php

namespace App\Enums;

enum MemberRole: string
{
    case OWNER = 'owner';
    case ADMIN = 'admin';
    case MEMBER = 'member';
    case VIEWER = 'viewer';

    public function label(): string
    {
        return match ($this) {
            self::OWNER => 'Proprietário',
            self::ADMIN => 'Administrador',
            self::MEMBER => 'Membro',
            self::VIEWER => 'Visualizador',
        };
    }

    public function canInvite(): bool
    {
        return in_array($this, [self::OWNER, self::ADMIN], true);
    }

    public function canRemove(): bool
    {
        return in_array($this, [self::OWNER, self::ADMIN], true);
    }

    public function canUpdate(): bool
    {
        return in_array($this, [self::OWNER, self::ADMIN], true);
    }

    public function canManageSettings(): bool
    {
        return $this === self::OWNER;
    }

    public function isHigherThan(self $other): bool
    {
        $hierarchy = [
            self::OWNER->value => 3,
            self::ADMIN->value => 2,
            self::MEMBER->value => 1,
            self::VIEWER->value => 0,
        ];

        return $hierarchy[$this->value] > $hierarchy[$other->value];
    }

    public function isOwner(): bool
    {
        return $this === self::OWNER;
    }
}
