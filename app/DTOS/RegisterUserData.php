<?php

declare(strict_types=1);

namespace App\DTOS;

use App\Http\Requests\Auth\RegisterRequest;

final readonly class RegisterUserData
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {}

    public static function fromRequest(RegisterRequest $request): self
    {
        $data = $request->validated();

        return new self($data['name'], $data['email'], $data['password']);
    }
}
