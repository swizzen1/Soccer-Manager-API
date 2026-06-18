<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Models\Team;
use App\Models\User;

interface TeamRepositoryInterface
{
    /** @param array{user_id: int, name: string, country: string, budget: int|float} $data */
    public function create(array $data): Team;

    public function getForUser(User $user): Team;

    public function lockForUser(User $user): Team;

    public function lock(Team $team): Team;

    /** @param array{name: string, country: string} $data */
    public function update(Team $team, array $data): Team;

    public function incrementBudget(Team $team, float $amount): void;

    public function decrementBudget(Team $team, float $amount): void;
}
