<?php

declare(strict_types=1);

namespace App\Http\Requests\Transfer;

use App\Models\Player;
use Illuminate\Foundation\Http\FormRequest;

final class CancelTransferListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $player = $this->route('player');

        return $player instanceof Player
            && $this->user()?->can('cancelTransfer', $player);
    }

    public function rules(): array
    {
        return [];
    }
}
