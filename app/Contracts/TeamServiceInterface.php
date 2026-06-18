<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTOS\UpdateTeamData;
use App\Models\Team;
use App\Models\User;

interface TeamServiceInterface
{
    public function getForUser(User $user): Team;

    public function update(User $user, UpdateTeamData $data): Team;
}
