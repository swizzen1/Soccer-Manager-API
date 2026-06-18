<?php

declare(strict_types=1);

namespace App\Http\Requests\Transfer;

use App\Models\Player;
use Illuminate\Foundation\Http\FormRequest;

final class CreateTransferListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $player = $this->route('player');

        return $player instanceof Player
            && $this->user()?->can('listForTransfer', $player);
    }

    public function rules(): array
    {
        return [
            'asking_price' => ['required', 'numeric', 'gt:0', 'max:9999999999999.99'],
        ];
    }
}
