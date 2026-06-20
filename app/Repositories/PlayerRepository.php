<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\PlayerRepositoryInterface;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

final class PlayerRepository implements PlayerRepositoryInterface
{
    /**
     * Creates a new player for the given team with the provided data and returns the created instance.
     * @return Player
     * @param Team $team The team to create the player for.
     * @param array $data The data to create the player with.
     */

    public function createForTeam(Team $team, array $data): Player
    {
        return $team->players()->create($data);
    }

    /**
     * Retrieves a list of players for the given team, ordered by their ID in descending order.
     * @return Collection
     * @param Team $team The team whose players are to be listed.
     */

    public function listForTeam(Team $team): Collection
    {
        return $team->players()->latest('id')->get();
    }

    /**
     * Loads the team relationship for the given player and returns the player instance with the loaded team.
     * @return Player
     * @param Player $player The player for which to load the team.
     */

    public function loadTeam(Player $player): Player
    {
        return $player->load('team');
    }

    /**
     * Locks the given player for update and returns the locked instance.
     * @return Player
     * @param Player $player The player to lock for update.
     */

    public function lock(Player $player): Player
    {
        return Player::query()->lockForUpdate()->findOrFail($player->id);
    }

    /**
     * Updates the given player with the provided data and returns the updated instance.
     * @return Player
     * @param Player $player The player to update.
     * @param array $data The data to update the player with.
     */

    public function update(Player $player, array $data): Player
    {
        $player->update($data);

        return $player->fresh()->load('team');
    }
}
