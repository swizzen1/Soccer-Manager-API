<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\PlayerServiceInterface;
use App\Contracts\Repositories\PlayerRepositoryInterface;
use App\Contracts\Repositories\TeamRepositoryInterface;
use App\DTOS\UpdatePlayerData;
use App\Models\Player;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Gate;

final class PlayerService implements PlayerServiceInterface
{
    public function __construct(
        private readonly PlayerRepositoryInterface $players,
        private readonly TeamRepositoryInterface $teams,
    ) {}

    /** @return Collection<int, Player> */
    public function listForUser(User $user): Collection
    {
        return $this->players->listForTeam($this->teams->getForUser($user));
    }

    public function show(Player $player): Player
    {
        return $this->players->loadTeam($player);
    }

    public function update(User $user, Player $player, UpdatePlayerData $data): Player
    {
        $player->loadMissing('team');

        Gate::forUser($user)->authorize('update', $player);

        return $this->players->update($player, $data->toArray());
    }
}
