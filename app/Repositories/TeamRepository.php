<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Models\Team;
use App\Models\User;

final class TeamRepository implements TeamRepositoryInterface
{
    /**
     * Creates a new team with the provided data and returns the created instance.
     * @return Team
     * @param array $data The data to create the team with.
     */

    public function create(array $data): Team
    {
        return Team::query()->create($data);
    }

    /**
     * Retrieves the team associated with the given user, including its players and the sum of their market values.
     * @return Team
     * @param User $user The user whose team is to be retrieved.
     */

    public function getForUser(User $user): Team
    {
        return $user->team()
            ->with('players')
            ->withSum('players as players_market_value_sum', 'market_value')
            ->firstOrFail();
    }

    /**
     * Locks the team associated with the given user for update and returns the locked instance.
     * @return Team
     * @param User $user The user whose team is to be locked for update.
     */

    public function lockForUser(User $user): Team
    {
        return $user->team()->lockForUpdate()->firstOrFail();
    }

    /**
     * Locks the given team for update and returns the locked instance.
     * @return Team
     * @param Team $team The team to lock for update.
     */

    public function lock(Team $team): Team
    {
        return Team::query()->lockForUpdate()->findOrFail($team->id);
    }

    /**
     * Updates the given team with the provided data and returns the updated instance.
     * @return Team
     * @param Team $team The team to update.
     * @param array $data The data to update the team with.
     */

    public function update(Team $team, array $data): Team
    {
        $team->update($data);

        return $team->fresh()
            ->load('players')
            ->loadSum('players as players_market_value_sum', 'market_value');
    }

    /**
     * Increments the budget of the given team by the specified amount.
     * @param Team $team The team whose budget is to be incremented.
     * @param float $amount The amount to increment the budget by.
     */

    public function incrementBudget(Team $team, float $amount): void
    {
        $team->increment('budget', $amount);
    }

    /**
     * Decrements the budget of the given team by the specified amount.
     * @param Team $team The team whose budget is to be decremented.
     * @param float $amount The amount to decrement the budget by.
     */

    public function decrementBudget(Team $team, float $amount): void
    {
        $team->decrement('budget', $amount);
    }
}
