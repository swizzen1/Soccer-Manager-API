<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Player;
use App\Models\Team;

interface PlayerGenerationServiceInterface
{
    /** @return list<Player> */
    public function generateForTeam(Team $team): array;
}
