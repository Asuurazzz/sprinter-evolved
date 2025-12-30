<?php

namespace App\Models;

use App\Enums\MemberRole;
use App\Enums\MemberStatus;
use App\Enums\TeamAccessType;
use App\Models\Concerns\HasAvatar;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    use HasAvatar, HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'avatar_url',
        'access_type',
        'settings',
        'owner_id',
    ];

    protected function casts(): array
    {
        return [
            'settings' => 'array',
            'access_type' => TeamAccessType::class,
            'deleted_at' => 'datetime',
        ];
    }

    // ========================================
    // Domain Methods - Profile Updates
    // ========================================

    public function updateProfile(string $name, string $description, ?string $avatarUrl = null): void
    {
        $this->name = $name;
        $this->description = $description;

        if ($avatarUrl !== null) {
            $this->avatar_url = $avatarUrl;
        }

        $this->save();
    }

    public function updateAccessType(TeamAccessType $accessType): void
    {
        if ($this->access_type !== $accessType) {
            $this->access_type = $accessType;
            $this->save();
        }
    }

    public function updateSettings(array $settings): void
    {
        $this->settings = array_merge($this->settings ?? [], $settings);
        $this->save();
    }

    // ========================================
    // Domain Methods - Member Management
    // ========================================

    public function addMember(string $userId, MemberRole $role, MemberStatus $status = MemberStatus::ACTIVE): TeamMember
    {
        if ($this->hasMember($userId)) {
            throw new \DomainException("User {$userId} is already a member of team {$this->id}.");
        }

        return $this->members()->create([
            'user_id' => $userId,
            'role' => $role,
            'status' => $status,
            'joined_at' => $status === MemberStatus::ACTIVE ? now() : null,
        ]);
    }

    public function removeMember(string $userId): void
    {
        $member = $this->getMember($userId);

        if ($member->role->isOwner()) {
            throw new \DomainException('Cannot remove owner from team.');
        }

        $member->delete();
    }

    public function hasMember(string $userId): bool
    {
        return $this->members()->where('user_id', $userId)->exists();
    }

    public function getMember(string $userId): TeamMember
    {
        $member = $this->members()->where('user_id', $userId)->first();

        if (! $member) {
            throw new \DomainException("User {$userId} is not a member of team {$this->id}.");
        }

        return $member;
    }

    public function getActiveMember(string $userId): ?TeamMember
    {
        return $this->members()
            ->where('user_id', $userId)
            ->where('status', MemberStatus::ACTIVE)
            ->first();
    }

    // ========================================
    // Domain Methods - Invitations
    // ========================================

    public function inviteMember(string $inviterId, string $email, MemberRole $role, int $expirationDays = 7): TeamInvitation
    {
        $inviter = $this->getMember($inviterId);

        if (! $inviter->role->canInvite()) {
            throw new \DomainException("User {$inviterId} does not have permission to invite members.");
        }

        if ($inviter->role->isHigherThan($role)) {
            throw new \DomainException("User {$inviterId} cannot invite members with a higher role.");
        }

        if ($this->hasActiveInvitationFor($email)) {
            throw new \DomainException("An active invitation already exists for email {$email}.");
        }

        return $this->invitations()->create([
            'inviter_id' => $inviterId,
            'email' => $email,
            'token' => bin2hex(random_bytes(32)),
            'role' => $role,
            'expires_at' => now()->addDays($expirationDays),
        ]);
    }

    public function hasActiveInvitationFor(string $email): bool
    {
        return $this->invitations()
            ->where('email', $email)
            ->where('expires_at', '>', now())
            ->whereNull('accepted_at')
            ->exists();
    }

    public function findInvitationByToken(string $token): ?TeamInvitation
    {
        return $this->invitations()
            ->where('token', $token)
            ->where('expires_at', '>', now())
            ->whereNull('accepted_at')
            ->first();
    }

    // ========================================
    // Domain Methods - Join Requests
    // ========================================

    public function hasJoinRequestFrom(string $userId): bool
    {
        return $this->joinRequests()
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->exists();
    }

    // ========================================
    // Query Helpers
    // ========================================

    public function isActive(): bool
    {
        return ! $this->trashed();
    }

    public function isPublic(): bool
    {
        return $this->access_type->isPublic();
    }

    public function isPrivate(): bool
    {
        return $this->access_type->isPrivate();
    }

    public function isOwnedBy(string $userId): bool
    {
        return $this->owner_id === $userId;
    }

    // ========================================
    // Relationships
    // ========================================

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function activeMembers(): HasMany
    {
        return $this->hasMany(TeamMember::class)->where('status', MemberStatus::ACTIVE);
    }

    public function users(): HasManyThrough
    {
        return $this->hasManyThrough(User::class, TeamMember::class, 'team_id', 'id', 'id', 'user_id');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(TeamInvitation::class);
    }

    public function joinRequests(): HasMany
    {
        return $this->hasMany(TeamJoinRequest::class);
    }
}
