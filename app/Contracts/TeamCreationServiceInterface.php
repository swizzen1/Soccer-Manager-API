<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\Team;
use App\Models\User;

interface TeamCreationServiceInterface
{
    public function createForUser(User $user): Team;
}
