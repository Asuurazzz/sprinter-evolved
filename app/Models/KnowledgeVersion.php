<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeVersion extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'content_id',
        'editor_id',
        'title',
        'body',
        'version_number',
    ];

    public $timestamps = ['created_at'];

    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'version_number' => 'integer',
        ];
    }

    // ========================================
    // Domain Methods
    // ========================================

    public function restore(): void
    {
        $content = $this->content;
        $content->updateContent($this->title, $this->body, $this->editor_id);
    }

    // ========================================
    // Relationships
    // ========================================

    public function content(): BelongsTo
    {
        return $this->belongsTo(KnowledgeContent::class, 'content_id');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editor_id');
    }
}
