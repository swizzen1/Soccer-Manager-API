<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\PlayerGenerationServiceInterface;
use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Contracts\TeamCreationServiceInterface;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class TeamCreationService implements TeamCreationServiceInterface
{
    public function __construct(
        private PlayerGenerationServiceInterface $playerGenerationService,
        private TeamRepositoryInterface $teams,
    ) {}

    public function createForUser(User $user): Team
    {
        return DB::transaction(function () use ($user): Team {
            $team = $this->teams->create([
                'user_id' => $user->id,
                'name' => "{$user->name} FC",
                'country' => 'Georgia',
                'budget' => Team::STARTING_BUDGET,
            ]);

            $this->playerGenerationService->generateForTeam($team);

            return $team->load('players');
        });
    }
}
