<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\UserRepositoryInterface;
use App\Models\User;

final class UserRepository implements UserRepositoryInterface
{
    /**
     * Creates a new user with the provided data and returns the created instance.
     * @return User
     * @param array $data The data to create the user with.
     */
    public function create(array $data): User
    {
        return User::query()->create($data);
    }

    /**
     * Finds a user by their email address and returns the user instance if found, or null if not found.
     * @return User|null
     * @param string $email The email address to search for.
     */

    public function findByEmail(string $email): ?User
    {
        return User::query()->where('email', $email)->first();
    }
}
