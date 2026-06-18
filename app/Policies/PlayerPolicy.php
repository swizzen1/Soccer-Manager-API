<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Player;
use App\Models\User;

final class PlayerPolicy
{
    public function update(User $user, Player $player): bool
    {
        return $player->team()
            ->where('user_id', $user->id)
            ->exists();
    }

    public function listForTransfer(User $user, Player $player): bool
    {
        return $this->update($user, $player);
    }

    public function cancelTransfer(User $user, Player $player): bool
    {
        return $this->update($user, $player);
    }
}
