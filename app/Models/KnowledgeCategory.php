<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class KnowledgeCategory extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'team_id',
        'parent_id',
        'name',
        'description',
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
    // Domain Methods - Profile Updates
    // ========================================

    public function updateProfile(string $name, string $description): void
    {
        $this->name = $name;
        $this->description = $description;
        $this->save();
    }

    public function updatePosition(int $position): void
    {
        if ($this->position !== $position) {
            $this->position = $position;
            $this->save();
        }
    }

    public function moveTo(?string $parentId): void
    {
        if ($parentId === $this->id) {
            throw new \DomainException('Category cannot be its own parent.');
        }

        if ($parentId !== null && $this->isAncestorOf($parentId)) {
            throw new \DomainException('Category cannot be moved to one of its descendants.');
        }

        $this->parent_id = $parentId;
        $this->save();
    }

    // ========================================
    // Query Helpers
    // ========================================

    public function isRoot(): bool
    {
        return $this->parent_id === null;
    }

    public function hasChildren(): bool
    {
        return $this->children()->exists();
    }

    public function isAncestorOf(string $categoryId): bool
    {
        $category = self::find($categoryId);

        while ($category !== null) {
            if ($category->parent_id === $this->id) {
                return true;
            }
            $category = $category->parent;
        }

        return false;
    }

    public function depth(): int
    {
        $depth = 0;
        $category = $this;

        while ($category->parent !== null) {
            $depth++;
            $category = $category->parent;
        }

        return $depth;
    }

    // ========================================
    // Relationships
    // ========================================

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(KnowledgeCategory::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(KnowledgeCategory::class, 'parent_id')->orderBy('position');
    }

    public function contents(): HasMany
    {
        return $this->hasMany(KnowledgeContent::class, 'category_id');
    }
}
