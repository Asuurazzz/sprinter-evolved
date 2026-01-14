<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'attachable_type',
        'attachable_id',
        'uploader_id',
        'name',
        'original_name',
        'path',
        'mime_type',
        'size',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'deleted_at' => 'datetime',
        ];
    }

    // ========================================
    // Query Helpers
    // ========================================

    public function sizeInKb(): float
    {
        return round($this->size / 1024, 2);
    }

    public function sizeInMb(): float
    {
        return round($this->size / (1024 * 1024), 2);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }

    // ========================================
    // Relationships
    // ========================================

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }
}
