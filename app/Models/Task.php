<?php

namespace App\Models;

use App\Enums\RecurrencePattern;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'board_id',
        'stage_id',
        'project_id',
        'sprint_id',
        'creator_id',
        'parent_task_id',
        'task_number',
        'title',
        'description',
        'priority',
        'status',
        'story_points',
        'estimated_minutes',
        'start_date',
        'due_date',
        'completed_at',
        'position',
        'is_recurring',
        'recurrence_pattern',
        'recurrence_config',
        'recurrence_start_date',
        'recurrence_end_date',
        'has_active_blocker',
        'blocker_reason',
        'blocker_created_at',
        'last_modified_by',
    ];

    protected function casts(): array
    {
        return [
            'priority' => TaskPriority::class,
            'status' => TaskStatus::class,
            'recurrence_pattern' => RecurrencePattern::class,
            'story_points' => 'integer',
            'estimated_minutes' => 'integer',
            'start_date' => 'date',
            'due_date' => 'date',
            'completed_at' => 'datetime',
            'position' => 'integer',
            'is_recurring' => 'boolean',
            'recurrence_config' => 'array',
            'recurrence_start_date' => 'date',
            'recurrence_end_date' => 'date',
            'has_active_blocker' => 'boolean',
            'blocker_created_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // ========================================
    // Domain Methods - Profile Updates
    // ========================================

    public function updateProfile(string $title, string $description): void
    {
        $this->title = $title;
        $this->description = $description;
        $this->touchLastModified();
        $this->save();
    }

    public function updatePriority(TaskPriority $priority): void
    {
        if ($this->priority !== $priority) {
            $this->priority = $priority;
            $this->touchLastModified();
            $this->save();
        }
    }

    public function updateStoryPoints(?int $storyPoints): void
    {
        if ($storyPoints !== null && $storyPoints < 0) {
            throw new \DomainException('Story points must be non-negative.');
        }

        $this->story_points = $storyPoints;
        $this->touchLastModified();
        $this->save();
    }

    public function updateEstimatedMinutes(?int $estimatedMinutes): void
    {
        if ($estimatedMinutes !== null && $estimatedMinutes < 0) {
            throw new \DomainException('Estimated minutes must be non-negative.');
        }

        $this->estimated_minutes = $estimatedMinutes;
        $this->touchLastModified();
        $this->save();
    }

    public function updateDates(?\DateTime $startDate, ?\DateTime $dueDate): void
    {
        if ($startDate && $dueDate && $startDate > $dueDate) {
            throw new \DomainException('Start date must be before due date.');
        }

        $this->start_date = $startDate;
        $this->due_date = $dueDate;
        $this->touchLastModified();
        $this->save();
    }

    public function updatePosition(int $position): void
    {
        if ($this->position !== $position) {
            $this->position = $position;
            $this->touchLastModified();
            $this->save();
        }
    }

    public function moveToStage(string $stageId): void
    {
        if ($this->stage_id !== $stageId) {
            $this->stage_id = $stageId;
            $this->touchLastModified();
            $this->save();
        }
    }

    public function assignToProject(?string $projectId): void
    {
        if ($this->project_id !== $projectId) {
            $this->project_id = $projectId;
            $this->touchLastModified();
            $this->save();
        }
    }

    public function assignToSprint(?string $sprintId): void
    {
        if ($this->sprint_id !== $sprintId) {
            $this->sprint_id = $sprintId;
            $this->touchLastModified();
            $this->save();
        }
    }

    // ========================================
    // Domain Methods - Status Management
    // ========================================

    public function complete(): void
    {
        if (! $this->status->isActive()) {
            throw new \DomainException('Only active tasks can be completed.');
        }

        if ($this->hasActiveBlockingDependencies()) {
            throw new \DomainException('Cannot complete task with active blocking dependencies.');
        }

        $this->status = TaskStatus::COMPLETED;
        $this->completed_at = now();
        $this->touchLastModified();
        $this->save();
    }

    public function reopen(): void
    {
        if (! $this->status->isCompleted()) {
            throw new \DomainException('Only completed tasks can be reopened.');
        }

        $this->status = TaskStatus::ACTIVE;
        $this->completed_at = null;
        $this->touchLastModified();
        $this->save();
    }

    public function archive(): void
    {
        if ($this->status->isArchived()) {
            return;
        }

        $this->status = TaskStatus::ARCHIVED;
        $this->touchLastModified();
        $this->save();
    }

    public function unarchive(): void
    {
        if (! $this->status->isArchived()) {
            return;
        }

        $this->status = TaskStatus::ACTIVE;
        $this->touchLastModified();
        $this->save();
    }

    // ========================================
    // Domain Methods - Blockers
    // ========================================

    public function block(string $reason): void
    {
        if (! $this->has_active_blocker) {
            $this->has_active_blocker = true;
            $this->blocker_reason = $reason;
            $this->blocker_created_at = now();
            $this->touchLastModified();
            $this->save();
        }
    }

    public function unblock(): void
    {
        if ($this->has_active_blocker) {
            $this->has_active_blocker = false;
            $this->blocker_reason = null;
            $this->blocker_created_at = null;
            $this->touchLastModified();
            $this->save();
        }
    }

    public function updateBlockerStatusFromDependencies(): void
    {
        $hasBlockingDeps = $this->hasActiveBlockingDependencies();

        if ($hasBlockingDeps && ! $this->has_active_blocker) {
            $this->has_active_blocker = true;
            $this->blocker_created_at = now();
            $this->save();
        } elseif (! $hasBlockingDeps && $this->has_active_blocker && $this->blocker_reason === null) {
            $this->has_active_blocker = false;
            $this->blocker_created_at = null;
            $this->save();
        }
    }

    public function hasActiveBlockingDependencies(): bool
    {
        return $this->blockedByDependencies()
            ->whereHas('dependsOnTask', function ($query) {
                $query->where('status', TaskStatus::ACTIVE);
            })
            ->exists();
    }

    // ========================================
    // Domain Methods - Assignment Management
    // ========================================

    public function assignUser(string $userId): TaskAssignee
    {
        if ($this->hasAssignee($userId)) {
            throw new \DomainException("User {$userId} is already assigned to task {$this->id}.");
        }

        $assignee = $this->assignees()->create([
            'user_id' => $userId,
        ]);

        $this->touchLastModified();

        return $assignee;
    }

    public function unassignUser(string $userId): void
    {
        $this->assignees()
            ->where('user_id', $userId)
            ->delete();

        $this->touchLastModified();
    }

    public function hasAssignee(string $userId): bool
    {
        return $this->assignees()
            ->where('user_id', $userId)
            ->exists();
    }

    // ========================================
    // Query Helpers
    // ========================================

    public function isActive(): bool
    {
        return $this->status->isActive() && ! $this->trashed();
    }

    public function isCompleted(): bool
    {
        return $this->status->isCompleted();
    }

    public function isArchived(): bool
    {
        return $this->status->isArchived();
    }

    public function isBlocked(): bool
    {
        return $this->has_active_blocker;
    }

    public function isOverdue(): bool
    {
        if (! $this->due_date || $this->isCompleted()) {
            return false;
        }

        return now()->startOfDay() > $this->due_date;
    }

    public function trackedMinutes(): int
    {
        return $this->timeEntries()->sum('minutes');
    }

    protected function touchLastModified(): void
    {
        if (auth()->check()) {
            $this->last_modified_by = auth()->id();
        }
    }

    // ========================================
    // Relationships
    // ========================================

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function sprint(): BelongsTo
    {
        return $this->belongsTo(Sprint::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function lastModifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_modified_by');
    }

    public function parentTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    public function childTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    public function assignees(): HasMany
    {
        return $this->hasMany(TaskAssignee::class);
    }

    public function taskLabels(): HasMany
    {
        return $this->hasMany(TaskLabel::class);
    }

    public function dependencies(): HasMany
    {
        return $this->hasMany(TaskDependency::class);
    }

    public function blockedByDependencies(): HasMany
    {
        return $this->hasMany(TaskDependency::class)->where('type', 'blocks');
    }

    public function checklistGroups(): HasMany
    {
        return $this->hasMany(ChecklistGroup::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function timeEntries(): HasMany
    {
        return $this->hasMany(TimeEntry::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
