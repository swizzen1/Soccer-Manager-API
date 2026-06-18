<?php

declare(strict_types=1);

namespace App\Http\Requests\Player;

use App\Models\Player;
use Illuminate\Foundation\Http\FormRequest;

final class UpdatePlayerRequest extends FormRequest
{
    public function authorize(): bool
    {
        $player = $this->route('player');

        return $player instanceof Player
            && $this->user()?->can('update', $player);
    }

    public function rules(): array
    {
        return [
            'first_name' => ['sometimes', 'string', 'max:100'],
            'last_name' => ['sometimes', 'string', 'max:100'],
            'country' => ['sometimes', 'string', 'max:100'],
            'age' => ['prohibited'],
            'position' => ['prohibited'],
            'market_value' => ['prohibited'],
            'team_id' => ['prohibited'],
        ];
    }
}
