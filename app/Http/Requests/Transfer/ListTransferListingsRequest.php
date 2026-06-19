<?php

declare(strict_types=1);

namespace App\Http\Requests\Transfer;

use Illuminate\Foundation\Http\FormRequest;

final class ListTransferListingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function perPage(): int
    {
        return (int) $this->validated('per_page', 15);
    }
}
