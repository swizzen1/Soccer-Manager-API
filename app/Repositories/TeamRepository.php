<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Models\Team;
use App\Models\User;

final class TeamRepository implements TeamRepositoryInterface
{
    public function create(array $data): Team
    {
        return Team::query()->create($data);
    }

    public function getForUser(User $user): Team
    {
        return $user->team()->with('players')->firstOrFail();
    }

    public function lockForUser(User $user): Team
    {
        return $user->team()->lockForUpdate()->firstOrFail();
    }

    public function lock(Team $team): Team
    {
        return Team::query()->lockForUpdate()->findOrFail($team->id);
    }

    public function update(Team $team, array $data): Team
    {
        $team->update($data);

        return $team->fresh()->load('players');
    }

    public function incrementBudget(Team $team, float $amount): void
    {
        $team->increment('budget', $amount);
    }

    public function decrementBudget(Team $team, float $amount): void
    {
        $team->decrement('budget', $amount);
    }
}
