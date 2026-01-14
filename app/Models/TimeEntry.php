<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TimeEntry extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'task_id',
        'user_id',
        'minutes',
        'description',
        'started_at',
        'ended_at',
    ];

    protected function casts(): array
    {
        return [
            'minutes' => 'integer',
            'started_at' => 'datetime',
            'ended_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // ========================================
    // Domain Methods
    // ========================================

    public function updateMinutes(int $minutes): void
    {
        if ($minutes < 0) {
            throw new \DomainException('Minutes must be non-negative.');
        }

        $this->minutes = $minutes;
        $this->save();
    }

    public function updateDescription(string $description): void
    {
        $this->description = $description;
        $this->save();
    }

    public function updateTimeRange(\DateTime $startedAt, \DateTime $endedAt): void
    {
        if ($startedAt > $endedAt) {
            throw new \DomainException('Start time must be before end time.');
        }

        $this->started_at = $startedAt;
        $this->ended_at = $endedAt;

        $diffInMinutes = (int) $startedAt->diff($endedAt)->format('%i');
        $this->minutes = $diffInMinutes;

        $this->save();
    }

    // ========================================
    // Query Helpers
    // ========================================

    public function hours(): float
    {
        return round($this->minutes / 60, 2);
    }

    // ========================================
    // Relationships
    // ========================================

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
