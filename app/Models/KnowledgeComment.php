<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class KnowledgeComment extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'content_id',
        'author_id',
        'content',
        'is_edited',
        'edited_at',
    ];

    protected function casts(): array
    {
        return [
            'is_edited' => 'boolean',
            'edited_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // ========================================
    // Domain Methods
    // ========================================

    public function updateContent(string $content): void
    {
        if ($this->content !== $content) {
            $this->content = $content;
            $this->is_edited = true;
            $this->edited_at = now();
            $this->save();
        }
    }

    // ========================================
    // Query Helpers
    // ========================================

    public function isEdited(): bool
    {
        return $this->is_edited;
    }

    public function isAuthoredBy(string $userId): bool
    {
        return $this->author_id === $userId;
    }

    // ========================================
    // Relationships
    // ========================================

    public function knowledgeContent(): BelongsTo
    {
        return $this->belongsTo(KnowledgeContent::class, 'content_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
