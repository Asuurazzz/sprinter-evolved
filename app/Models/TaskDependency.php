<?php

namespace App\Models;

use App\Enums\TaskDependencyType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaskDependency extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'task_id',
        'depends_on_task_id',
        'type',
    ];

    public $timestamps = ['created_at'];

    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'type' => TaskDependencyType::class,
        ];
    }

    // ========================================
    // Query Helpers
    // ========================================

    public function isBlocking(): bool
    {
        return $this->type->isBlocks();
    }

    public function isRelated(): bool
    {
        return $this->type->isRelated();
    }

    // ========================================
    // Relationships
    // ========================================

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function dependsOnTask(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'depends_on_task_id');
    }
}
