<?php

declare(strict_types=1);

namespace App\Http\Requests\Transfer;

use Illuminate\Foundation\Http\FormRequest;

final class CreateTransferListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'asking_price' => ['required', 'numeric', 'min:0.01'],
        ];
    }
}
