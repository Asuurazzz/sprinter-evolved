<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stage extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'board_id',
        'name',
        'color',
        'position',
        'conclusion',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'conclusion' => 'boolean',
            'deleted_at' => 'datetime',
        ];
    }

    // ========================================
    // Domain Methods - Profile Updates
    // ========================================

    public function updateProfile(string $name, string $color): void
    {
        $this->name = $name;
        $this->color = $color;
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
    // Domain Methods - Conclusion Management
    // ========================================

    public function markAsConclusion(): void
    {
        if (! $this->conclusion) {
            $this->conclusion = true;
            $this->save();
        }
    }

    public function unmarkAsConclusion(): void
    {
        if ($this->conclusion) {
            $this->conclusion = false;
            $this->save();
        }
    }

    // ========================================
    // Query Helpers
    // ========================================

    public function isConclusion(): bool
    {
        return $this->conclusion;
    }

    public function isActive(): bool
    {
        return ! $this->trashed();
    }

    // ========================================
    // Relationships
    // ========================================

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }
}
