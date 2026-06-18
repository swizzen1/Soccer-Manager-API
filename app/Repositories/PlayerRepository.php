<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\PlayerRepositoryInterface;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

final class PlayerRepository implements PlayerRepositoryInterface
{
    public function createForTeam(Team $team, array $data): Player
    {
        return $team->players()->create($data);
    }

    public function listForTeam(Team $team): Collection
    {
        return $team->players()->latest('id')->get();
    }

    public function loadTeam(Player $player): Player
    {
        return $player->load('team');
    }

    public function lock(Player $player): Player
    {
        return Player::query()->lockForUpdate()->findOrFail($player->id);
    }

    public function update(Player $player, array $data): Player
    {
        $player->update($data);

        return $player->fresh()->load('team');
    }
}
