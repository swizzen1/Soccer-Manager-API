<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Contracts\TeamServiceInterface;
use App\DTOS\UpdateTeamData;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

final class TeamService implements TeamServiceInterface
{
    public function __construct(private readonly TeamRepositoryInterface $teams) {}

    public function getForUser(User $user): Team
    {
        return $this->teams->getForUser($user);
    }

    public function update(User $user, UpdateTeamData $data): Team
    {
        $team = $this->getForUser($user);

        Gate::forUser($user)->authorize('update', $team);

        return $this->teams->update($team, $data->toArray());
    }
}
