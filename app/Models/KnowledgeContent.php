<?php

namespace App\Models;

use App\Enums\KnowledgeContentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class KnowledgeContent extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'team_id',
        'category_id',
        'author_id',
        'title',
        'body',
        'status',
        'tags',
        'version',
        'last_edited_by',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => KnowledgeContentStatus::class,
            'tags' => 'array',
            'version' => 'integer',
            'published_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    // ========================================
    // Domain Methods - Content Updates
    // ========================================

    public function updateContent(string $title, string $body, string $editorId): void
    {
        $this->createVersionSnapshot($editorId);

        $this->title = $title;
        $this->body = $body;
        $this->version = ($this->version ?? 0) + 1;
        $this->last_edited_by = $editorId;
        $this->save();
    }

    public function updateTags(array $tags): void
    {
        $this->tags = $tags;
        $this->save();
    }

    public function moveToCategory(?string $categoryId): void
    {
        if ($this->category_id !== $categoryId) {
            $this->category_id = $categoryId;
            $this->save();
        }
    }

    protected function createVersionSnapshot(string $editorId): void
    {
        if ($this->version > 0) {
            $this->versions()->create([
                'editor_id' => $editorId,
                'title' => $this->title,
                'body' => $this->body,
                'version_number' => $this->version,
            ]);
        }
    }

    // ========================================
    // Domain Methods - Status Management
    // ========================================

    public function publish(): void
    {
        if ($this->status->isPublished()) {
            return;
        }

        $this->status = KnowledgeContentStatus::PUBLISHED;
        $this->published_at = now();
        $this->save();
    }

    public function unpublish(): void
    {
        if (! $this->status->isPublished()) {
            return;
        }

        $this->status = KnowledgeContentStatus::DRAFT;
        $this->save();
    }

    public function archive(): void
    {
        if ($this->status->isArchived()) {
            return;
        }

        $this->status = KnowledgeContentStatus::ARCHIVED;
        $this->save();
    }

    public function unarchive(): void
    {
        if (! $this->status->isArchived()) {
            return;
        }

        $this->status = KnowledgeContentStatus::DRAFT;
        $this->save();
    }

    // ========================================
    // Query Helpers
    // ========================================

    public function isDraft(): bool
    {
        return $this->status->isDraft();
    }

    public function isPublished(): bool
    {
        return $this->status->isPublished();
    }

    public function isArchived(): bool
    {
        return $this->status->isArchived();
    }

    public function isAuthoredBy(string $userId): bool
    {
        return $this->author_id === $userId;
    }

    // ========================================
    // Relationships
    // ========================================

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(KnowledgeCategory::class, 'category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function lastEditedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_edited_by');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(KnowledgeVersion::class, 'content_id')->orderByDesc('version_number');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(KnowledgeComment::class, 'content_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
}
