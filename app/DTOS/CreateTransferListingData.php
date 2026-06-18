<?php

declare(strict_types=1);

namespace App\DTOS;

use App\Http\Requests\Transfer\CreateTransferListingRequest;

final readonly class CreateTransferListingData
{
    public function __construct(public float $askingPrice) {}

    public static function fromRequest(CreateTransferListingRequest $request): self
    {
        $data = $request->validated();

        return new self((float) $data['asking_price']);
    }
}
