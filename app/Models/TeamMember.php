<?php

namespace App\Models;

use App\Enums\MemberRole;
use App\Enums\MemberStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamMember extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'team_id',
        'user_id',
        'role',
        'status',
        'joined_at',
    ];

    protected function casts(): array
    {
        return [
            'role' => MemberRole::class,
            'status' => MemberStatus::class,
            'joined_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // ========================================
    // Domain Methods - Role & Status
    // ========================================

    public function changeRole(MemberRole $newRole): void
    {
        if ($this->role->isOwner()) {
            throw new \DomainException('Cannot change owner role.');
        }

        $this->role = $newRole;
        $this->save();
    }

    public function suspend(): void
    {
        if ($this->status !== MemberStatus::SUSPENDED) {
            $this->status = MemberStatus::SUSPENDED;
            $this->save();
        }
    }

    public function activate(): void
    {
        if ($this->status !== MemberStatus::ACTIVE) {
            $this->status = MemberStatus::ACTIVE;

            if ($this->joined_at === null) {
                $this->joined_at = now();
            }

            $this->save();
        }
    }

    // ========================================
    // Permission Checks
    // ========================================

    public function canInvite(): bool
    {
        return $this->isActive() && $this->role->canInvite();
    }

    public function canRemove(): bool
    {
        return $this->isActive() && $this->role->canRemove();
    }

    public function canUpdate(): bool
    {
        return $this->isActive() && $this->role->canUpdate();
    }

    public function canManageSettings(): bool
    {
        return $this->isActive() && $this->role->canManageSettings();
    }

    public function hasHigherRoleThan(TeamMember $other): bool
    {
        return $this->role->isHigherThan($other->role);
    }

    // ========================================
    // Query Helpers
    // ========================================

    public function isOwner(): bool
    {
        return $this->role->isOwner();
    }

    public function isActive(): bool
    {
        return $this->status->isActive() && ! $this->trashed();
    }

    public function isSuspended(): bool
    {
        return $this->status->isSuspended();
    }

    // ========================================
    // Relationships
    // ========================================

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
