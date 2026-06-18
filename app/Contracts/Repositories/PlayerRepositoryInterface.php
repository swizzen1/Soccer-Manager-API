<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Eloquent\Collection;

interface PlayerRepositoryInterface
{
    /** @param array<string, mixed> $data */
    public function createForTeam(Team $team, array $data): Player;

    /** @return Collection<int, Player> */
    public function listForTeam(Team $team): Collection;

    public function loadTeam(Player $player): Player;

    public function lock(Player $player): Player;

    /** @param array{first_name?: string, last_name?: string, country?: string, team_id?: int, market_value?: float} $data */
    public function update(Player $player, array $data): Player;
}
