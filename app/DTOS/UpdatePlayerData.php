<?php

declare(strict_types=1);

namespace App\DTOS;

use App\Http\Requests\Player\UpdatePlayerRequest;

final readonly class UpdatePlayerData
{
    /**
     * @param array{first_name?: string, last_name?: string, country?: string} $attributes
     */
    public function __construct(
        private array $attributes,
    ) {}

    public static function fromRequest(UpdatePlayerRequest $request): self
    {
        return new self($request->validated());
    }

    /**
     * @return array{first_name?: string, last_name?: string, country?: string}
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
