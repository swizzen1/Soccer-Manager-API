<?php

declare(strict_types=1);

namespace App\Http\Requests\Transfer;

use App\Models\TransferListing;
use Illuminate\Foundation\Http\FormRequest;

final class BuyTransferListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $listing = $this->route('listing');

        return $listing instanceof TransferListing
            && $this->user()?->can('buy', $listing);
    }

    public function rules(): array
    {
        return [];
    }
}
