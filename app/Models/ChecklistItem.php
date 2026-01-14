<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChecklistItem extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'checklist_group_id',
        'text',
        'is_completed',
        'completed_by',
        'completed_at',
        'position',
    ];

    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
            'position' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }

    // ========================================
    // Domain Methods
    // ========================================

    public function updateText(string $text): void
    {
        $this->text = $text;
        $this->save();
    }

    public function updatePosition(int $position): void
    {
        if ($this->position !== $position) {
            $this->position = $position;
            $this->save();
        }
    }

    public function complete(string $userId): void
    {
        if (! $this->is_completed) {
            $this->is_completed = true;
            $this->completed_by = $userId;
            $this->completed_at = now();
            $this->save();
        }
    }

    public function uncomplete(): void
    {
        if ($this->is_completed) {
            $this->is_completed = false;
            $this->completed_by = null;
            $this->completed_at = null;
            $this->save();
        }
    }

    // ========================================
    // Query Helpers
    // ========================================

    public function isCompleted(): bool
    {
        return $this->is_completed;
    }

    // ========================================
    // Relationships
    // ========================================

    public function checklistGroup(): BelongsTo
    {
        return $this->belongsTo(ChecklistGroup::class);
    }

    public function completedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }
}
