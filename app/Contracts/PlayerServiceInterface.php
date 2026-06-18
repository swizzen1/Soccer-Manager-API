<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTOS\UpdatePlayerData;
use App\Models\Player;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface PlayerServiceInterface
{
    /** @return Collection<int, Player> */
    public function listForUser(User $user): Collection;

    public function show(Player $player): Player;

    public function update(User $user, Player $player, UpdatePlayerData $data): Player;
}
