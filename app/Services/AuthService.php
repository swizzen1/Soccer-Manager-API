<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\AuthServiceInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\TeamCreationServiceInterface;
use App\DTOS\LoginUserData;
use App\DTOS\RegisterUserData;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final readonly class AuthService implements AuthServiceInterface
{
    public function __construct(
        private TeamCreationServiceInterface $teamCreationService,
        private UserRepositoryInterface $users,
    ) {}

    /** @return array{user: User, token: string} */
    public function register(RegisterUserData $data): array
    {
        return DB::transaction(function () use ($data): array {
            $user = $this->users->create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => $data->password,
            ]);

            $this->teamCreationService->createForUser($user);

            return [
                'user' => $user->load('team.players'),
                'token' => $user->createToken('api-token')->plainTextToken,
            ];
        });
    }

    /** @return array{user: User, token: string} */
    public function login(LoginUserData $data): array
    {
        $user = $this->users->findByEmail($data->email);

        if (! $user || ! Hash::check($data->password, $user->password)) {
            throw new AuthenticationException(__('messages.auth.invalid_credentials'));
        }

        return [
            'user' => $user->load('team.players'),
            'token' => $user->createToken('api-token')->plainTextToken,
        ];
    }

    public function logout(User $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
