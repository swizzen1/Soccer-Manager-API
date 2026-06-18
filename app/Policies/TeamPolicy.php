<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

final class TeamPolicy
{
    public function update(User $user, Team $team): bool
    {
        return $team->user_id === $user->id;
    }
}
