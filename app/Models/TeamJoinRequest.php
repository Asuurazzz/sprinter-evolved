<?php

namespace App\Models;

use App\Enums\JoinRequestStatus;
use App\Enums\MemberRole;
use App\Enums\MemberStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TeamJoinRequest extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'team_id',
        'user_id',
        'message',
        'status',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => JoinRequestStatus::class,
            'reviewed_at' => 'datetime',
        ];
    }

    // ========================================
    // Domain Methods
    // ========================================

    public function approve(string $reviewerId, MemberRole $role = MemberRole::MEMBER): TeamMember
    {
        if ($this->status->isApproved()) {
            throw new \DomainException('Join request has already been approved.');
        }

        if ($this->status->isRejected()) {
            throw new \DomainException('Cannot approve a rejected join request.');
        }

        $this->status = JoinRequestStatus::APPROVED;
        $this->reviewed_by = $reviewerId;
        $this->reviewed_at = now();
        $this->save();

        return $this->team->addMember($this->user_id, $role, MemberStatus::ACTIVE);
    }

    public function reject(string $reviewerId): void
    {
        if ($this->status->isRejected()) {
            return;
        }

        if ($this->status->isApproved()) {
            throw new \DomainException('Cannot reject an approved join request.');
        }

        $this->status = JoinRequestStatus::REJECTED;
        $this->reviewed_by = $reviewerId;
        $this->reviewed_at = now();
        $this->save();
    }

    public function isPending(): bool
    {
        return $this->status->isPending();
    }

    public function isApproved(): bool
    {
        return $this->status->isApproved();
    }

    public function isRejected(): bool
    {
        return $this->status->isRejected();
    }

    // ========================================
    // Relationships
    // ========================================

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
