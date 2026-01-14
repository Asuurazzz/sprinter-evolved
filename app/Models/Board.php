<?php

namespace App\Models;

use App\Enums\BoardStatus;
use App\Enums\BoardVisibility;
use App\Enums\TaskCreationPermission;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Board extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'team_id',
        'name',
        'description',
        'visibility',
        'task_creation_permission',
        'status',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'visibility' => BoardVisibility::class,
            'task_creation_permission' => TaskCreationPermission::class,
            'status' => BoardStatus::class,
            'position' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }

    // ========================================
    // Domain Methods - Profile Updates
    // ========================================

    public function updateProfile(string $name, string $description): void
    {
        $this->name = $name;
        $this->description = $description;
        $this->save();
    }

    public function updateVisibility(BoardVisibility $visibility): void
    {
        if ($this->visibility !== $visibility) {
            $this->visibility = $visibility;
            $this->save();
        }
    }

    public function updateTaskCreationPermission(TaskCreationPermission $permission): void
    {
        if ($this->task_creation_permission !== $permission) {
            $this->task_creation_permission = $permission;
            $this->save();
        }
    }

    public function updatePosition(int $position): void
    {
        if ($this->position !== $position) {
            $this->position = $position;
            $this->save();
        }
    }

    // ========================================
    // Domain Methods - Status Management
    // ========================================

    public function archive(): void
    {
        if ($this->status !== BoardStatus::ARCHIVED) {
            $this->status = BoardStatus::ARCHIVED;
            $this->save();
        }
    }

    public function unarchive(): void
    {
        if ($this->status !== BoardStatus::ACTIVE) {
            $this->status = BoardStatus::ACTIVE;
            $this->save();
        }
    }

    // ========================================
    // Domain Methods - Member Management
    // ========================================

    public function addMember(string $userId): BoardMember
    {
        if ($this->hasMember($userId)) {
            throw new \DomainException("User {$userId} is already a member of board {$this->id}.");
        }

        return $this->boardMembers()->create([
            'user_id' => $userId,
        ]);
    }

    public function removeMember(string $userId): void
    {
        $this->boardMembers()
            ->where('user_id', $userId)
            ->delete();
    }

    public function hasMember(string $userId): bool
    {
        return $this->boardMembers()
            ->where('user_id', $userId)
            ->exists();
    }

    // ========================================
    // Domain Methods - Access Control
    // ========================================

    public function canUserView(User $user): bool
    {
        if (! $user->isActive()) {
            return false;
        }

        if ($this->visibility->isPublic()) {
            return true;
        }

        $teamMember = $this->team->getActiveMember($user->id);

        if (! $teamMember) {
            return false;
        }

        if ($this->visibility->isPrivate()) {
            return $teamMember->role->canManageSettings();
        }

        if ($this->visibility->isRestricted()) {
            return $this->hasMember($user->id);
        }

        return false;
    }

    public function canUserCreateTasks(User $user): bool
    {
        if (! $this->canUserView($user)) {
            return false;
        }

        if ($this->task_creation_permission->isAll()) {
            return true;
        }

        $teamMember = $this->team->getActiveMember($user->id);

        if (! $teamMember) {
            return false;
        }

        return $teamMember->role->canInvite();
    }

    public function isUserModerator(User $user): bool
    {
        $teamMember = $this->team->getActiveMember($user->id);

        if (! $teamMember) {
            return false;
        }

        return $teamMember->role->canInvite();
    }

    // ========================================
    // Query Helpers
    // ========================================

    public function isActive(): bool
    {
        return $this->status->isActive() && ! $this->trashed();
    }

    public function isArchived(): bool
    {
        return $this->status->isArchived();
    }

    public function isPublic(): bool
    {
        return $this->visibility->isPublic();
    }

    public function isPrivate(): bool
    {
        return $this->visibility->isPrivate();
    }

    public function isRestricted(): bool
    {
        return $this->visibility->isRestricted();
    }

    // ========================================
    // Relationships
    // ========================================

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function boardMembers(): HasMany
    {
        return $this->hasMany(BoardMember::class);
    }

    public function stages(): HasMany
    {
        return $this->hasMany(Stage::class)->orderBy('position');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
}
