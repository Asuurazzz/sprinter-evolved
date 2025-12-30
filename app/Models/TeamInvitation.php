<?php

namespace App\Models;

use App\Enums\MemberRole;
use App\Enums\MemberStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamInvitation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'team_id',
        'inviter_id',
        'email',
        'token',
        'role',
        'expires_at',
        'accepted_at',
    ];

    protected function casts(): array
    {
        return [
            'role' => MemberRole::class,
            'expires_at' => 'datetime',
            'accepted_at' => 'datetime',
        ];
    }

    // ========================================
    // Domain Methods
    // ========================================

    public function accept(string $userId): TeamMember
    {
        if (! $this->isActive()) {
            throw new \DomainException('Invitation is no longer active.');
        }

        $this->accepted_at = now();
        $this->save();

        return $this->team->addMember($userId, $this->role, MemberStatus::ACTIVE);
    }

    public function isActive(): bool
    {
        return $this->expires_at > now() && $this->accepted_at === null;
    }

    public function isExpired(): bool
    {
        return $this->expires_at <= now();
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    public function matchesToken(string $token): bool
    {
        return hash_equals($this->token, $token);
    }

    // ========================================
    // Relationships
    // ========================================

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inviter_id');
    }
}
