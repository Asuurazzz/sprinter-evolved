<?php

namespace App\Observers;

use App\Enums\MemberRole;
use App\Enums\MemberStatus;
use App\Models\Team;

class TeamObserver
{
    public function creating(Team $team): void {}

    public function created(Team $team): void
    {
        $team->members()->create([
            'user_id' => $team->owner_id,
            'role' => MemberRole::OWNER,
            'status' => MemberStatus::ACTIVE,
            'joined_at' => now(),
        ]);

    }

    public function updated(Team $team): void {}

    public function restored(Team $team): void {}

    public function forceDeleted(Team $team): void {}
}
