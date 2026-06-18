<?php

declare(strict_types=1);

namespace App\DTOS;

use App\Http\Requests\Auth\LoginRequest;

final readonly class LoginUserData
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}

    public static function fromRequest(LoginRequest $request): self
    {
        $data = $request->validated();

        return new self($data['email'], $data['password']);
    }
}
