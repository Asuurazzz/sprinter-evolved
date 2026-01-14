<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'team_id',
        'name',
        'description',
        'objectives',
        'status',
        'planned_start_date',
        'planned_end_date',
        'actual_start_date',
        'actual_end_date',
        'progress',
    ];

    protected function casts(): array
    {
        return [
            'status' => ProjectStatus::class,
            'planned_start_date' => 'date',
            'planned_end_date' => 'date',
            'actual_start_date' => 'date',
            'actual_end_date' => 'date',
            'progress' => 'decimal:2',
            'deleted_at' => 'datetime',
        ];
    }

    // ========================================
    // Domain Methods - Profile Updates
    // ========================================

    public function updateProfile(string $name, string $description, string $objectives): void
    {
        $this->name = $name;
        $this->description = $description;
        $this->objectives = $objectives;
        $this->save();
    }

    public function updatePlannedDates(?\DateTime $startDate, ?\DateTime $endDate): void
    {
        if ($startDate && $endDate && $startDate > $endDate) {
            throw new \DomainException('Planned start date must be before end date.');
        }

        $this->planned_start_date = $startDate;
        $this->planned_end_date = $endDate;
        $this->save();
    }

    public function updateProgress(float $progress): void
    {
        if ($progress < 0 || $progress > 100) {
            throw new \DomainException('Progress must be between 0 and 100.');
        }

        $this->progress = $progress;
        $this->save();
    }

    // ========================================
    // Domain Methods - Status Management
    // ========================================

    public function start(): void
    {
        if (! $this->status->isPlanning()) {
            throw new \DomainException('Only projects in planning can be started.');
        }

        $this->status = ProjectStatus::IN_PROGRESS;
        $this->actual_start_date = now();
        $this->save();
    }

    public function complete(): void
    {
        if (! $this->status->isInProgress()) {
            throw new \DomainException('Only projects in progress can be completed.');
        }

        $this->status = ProjectStatus::COMPLETED;
        $this->actual_end_date = now();
        $this->progress = 100;
        $this->save();
    }

    public function cancel(): void
    {
        if ($this->status->isCompleted() || $this->status->isArchived()) {
            throw new \DomainException('Cannot cancel completed or archived projects.');
        }

        $this->status = ProjectStatus::CANCELLED;
        $this->save();
    }

    public function archive(): void
    {
        if (! in_array($this->status, [ProjectStatus::COMPLETED, ProjectStatus::CANCELLED], true)) {
            throw new \DomainException('Only completed or cancelled projects can be archived.');
        }

        $this->status = ProjectStatus::ARCHIVED;
        $this->save();
    }

    public function reopen(): void
    {
        if (! $this->status->isCancelled()) {
            throw new \DomainException('Only cancelled projects can be reopened.');
        }

        $this->status = ProjectStatus::PLANNING;
        $this->save();
    }

    // ========================================
    // Query Helpers
    // ========================================

    public function isActive(): bool
    {
        return $this->status->isActive() && ! $this->trashed();
    }

    public function isPlanning(): bool
    {
        return $this->status->isPlanning();
    }

    public function isInProgress(): bool
    {
        return $this->status->isInProgress();
    }

    public function isCompleted(): bool
    {
        return $this->status->isCompleted();
    }

    public function isCancelled(): bool
    {
        return $this->status->isCancelled();
    }

    public function isArchived(): bool
    {
        return $this->status->isArchived();
    }

    public function isOverdue(): bool
    {
        if (! $this->planned_end_date || $this->isCompleted()) {
            return false;
        }

        return now()->startOfDay() > $this->planned_end_date;
    }

    // ========================================
    // Relationships
    // ========================================

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
