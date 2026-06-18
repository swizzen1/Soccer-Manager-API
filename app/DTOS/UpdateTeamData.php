<?php

declare(strict_types=1);

namespace App\DTOS;

use App\Http\Requests\Team\UpdateTeamRequest;

final readonly class UpdateTeamData
{
    /**
     * @param array{name?: string, country?: string} $attributes
     */
    public function __construct(
        private array $attributes,
    ) {}

    public static function fromRequest(UpdateTeamRequest $request): self
    {
        return new self($request->validated());
    }

    /**
     * @return array{name?: string, country?: string}
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
