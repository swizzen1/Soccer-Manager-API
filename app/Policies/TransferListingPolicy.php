<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\TransferListing;
use App\Models\User;

final class TransferListingPolicy
{
    public function buy(User $user, TransferListing $transferListing): bool
    {
        return ! $transferListing->sellerTeam()
            ->where('user_id', $user->id)
            ->exists();
    }
}
