<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\TransferListing;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin TransferListing */
final class TransferListingResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'asking_price' => (float) $this->asking_price,
            'status' => $this->status->value,
            'player' => $this->whenLoaded('player', fn () => new PlayerResource($this->player)),
            'seller_team' => $this->whenLoaded('sellerTeam', fn () => new TeamResource($this->sellerTeam)),
        ];
    }
}
