<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Team */
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
            'team_value' => $this->teamValue(),
            'players' => $this->whenLoaded('players', fn () => PlayerResource::collection($this->players)),
        ];
    }

    private function teamValue(): float
    {
        if (array_key_exists('players_market_value_sum', $this->getAttributes())) {
            return (float) $this->getAttribute('players_market_value_sum');
        }

        if ($this->relationLoaded('players')) {
            return (float) $this->players->sum('market_value');
        }

        return 0.0;
    }
}
