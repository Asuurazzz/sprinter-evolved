<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChecklistGroup extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'task_id',
        'name',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }

    // ========================================
    // Domain Methods
    // ========================================

    public function updateName(string $name): void
    {
        $this->name = $name;
        $this->save();
    }

    public function updatePosition(int $position): void
    {
        if ($this->position !== $position) {
            $this->position = $position;
            $this->save();
        }
    }

    // ========================================
    // Query Helpers
    // ========================================

    public function completionPercentage(): float
    {
        $total = $this->items()->count();

        if ($total === 0) {
            return 0;
        }

        $completed = $this->items()->where('is_completed', true)->count();

        return round(($completed / $total) * 100, 2);
    }

    public function isFullyCompleted(): bool
    {
        return $this->items()->count() > 0 &&
               $this->items()->where('is_completed', false)->count() === 0;
    }

    // ========================================
    // Relationships
    // ========================================

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ChecklistItem::class)->orderBy('position');
    }
}
