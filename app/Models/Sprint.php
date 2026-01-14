<?php

namespace App\Models;

use App\Enums\SprintStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sprint extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'team_id',
        'name',
        'objective',
        'status',
        'start_date',
        'end_date',
        'goal_points',
        'goal_tasks',
    ];

    protected function casts(): array
    {
        return [
            'status' => SprintStatus::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'goal_points' => 'integer',
            'goal_tasks' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }

    // ========================================
    // Domain Methods - Profile Updates
    // ========================================

    public function updateProfile(string $name, string $objective): void
    {
        $this->name = $name;
        $this->objective = $objective;
        $this->save();
    }

    public function updateDates(\DateTime $startDate, \DateTime $endDate): void
    {
        if ($startDate > $endDate) {
            throw new \DomainException('Start date must be before end date.');
        }

        $this->start_date = $startDate;
        $this->end_date = $endDate;
        $this->save();
    }

    public function updateGoals(int $goalPoints, int $goalTasks): void
    {
        if ($goalPoints < 0 || $goalTasks < 0) {
            throw new \DomainException('Goals must be positive values.');
        }

        $this->goal_points = $goalPoints;
        $this->goal_tasks = $goalTasks;
        $this->save();
    }

    // ========================================
    // Domain Methods - Status Management
    // ========================================

    public function start(): void
    {
        if (! $this->status->isPlanning()) {
            throw new \DomainException('Only sprints in planning can be started.');
        }

        $this->status = SprintStatus::ACTIVE;
        $this->save();
    }

    public function complete(): void
    {
        if (! $this->status->isActive()) {
            throw new \DomainException('Only active sprints can be completed.');
        }

        $this->status = SprintStatus::COMPLETED;
        $this->save();
    }

    public function reopen(): void
    {
        if (! $this->status->isCompleted()) {
            throw new \DomainException('Only completed sprints can be reopened.');
        }

        $this->status = SprintStatus::PLANNING;
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

    public function isCompleted(): bool
    {
        return $this->status->isCompleted();
    }

    public function isOverdue(): bool
    {
        if (! $this->end_date || $this->isCompleted()) {
            return false;
        }

        return now()->startOfDay() > $this->end_date;
    }

    public function daysRemaining(): int
    {
        if (! $this->end_date || $this->isCompleted()) {
            return 0;
        }

        $remaining = now()->startOfDay()->diffInDays($this->end_date, false);

        return max(0, (int) $remaining);
    }

    // ========================================
    // Relationships
    // ========================================

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
