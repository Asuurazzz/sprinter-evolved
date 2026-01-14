<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoardMember extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'board_id',
        'user_id',
    ];

    // ========================================
    // Query Helpers
    // ========================================

    public function canView(): bool
    {
        return $this->board->canUserView($this->user);
    }

    public function canCreateTasks(): bool
    {
        return $this->board->canUserCreateTasks($this->user);
    }

    // ========================================
    // Relationships
    // ========================================

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
