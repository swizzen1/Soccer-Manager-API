<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class TransferListingResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'asking_price' => (float) $this->asking_price,
            'status' => $this->status?->value ?? $this->status,
            'player' => $this->whenLoaded('player', fn () => new PlayerResource($this->player)),
            'seller_team' => $this->whenLoaded('sellerTeam', fn () => new TeamResource($this->sellerTeam)),
        ];
    }
}
