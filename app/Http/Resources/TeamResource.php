<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class TeamResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'country' => $this->country,
            'budget' => (float) $this->budget,
            'team_value' => (float) $this->players()->sum('market_value'),
            'players' => $this->whenLoaded('players', fn () => PlayerResource::collection($this->players)),
        ];
    }
}
