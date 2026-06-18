<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTOS\LoginUserData;
use App\DTOS\RegisterUserData;
use App\Models\User;

interface AuthServiceInterface
{
    /** @return array{user: User, token: string} */
    public function register(RegisterUserData $data): array;

    /** @return array{user: User, token: string} */
    public function login(LoginUserData $data): array;

    public function logout(User $user): void;
}
