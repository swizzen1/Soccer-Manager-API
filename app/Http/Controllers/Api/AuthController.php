<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\AuthServiceInterface;
use App\DTOS\LoginUserData;
use App\DTOS\RegisterUserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthenticatedRequest;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

final class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly AuthServiceInterface $authService) {}

    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->authService->register(RegisterUserData::fromRequest($request));

        return $this->success(__('messages.auth.register_success'), [
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(LoginUserData::fromRequest($request));

        return $this->success(__('messages.auth.login_success'), [
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ]);
    }

    public function logout(AuthenticatedRequest $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->success(__('messages.auth.logout_success'));
    }

    public function me(AuthenticatedRequest $request): JsonResponse
    {
        return $this->success(
            __('messages.auth.me_success'),
            new UserResource($request->user()->load('team.players'))
        );
    }
}
