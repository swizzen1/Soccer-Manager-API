<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Player;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Player */
final class PlayerResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'country' => $this->country,
            'position' => $this->position->value,
            'age' => $this->age,
            'market_value' => (float) $this->market_value,
            'team_id' => $this->team_id,
        ];
    }
}
