<?php

declare(strict_types=1);

namespace App\Contracts;

use App\DTOS\CreateTransferListingData;
use App\Models\Player;
use App\Models\TransferListing;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface TransferServiceInterface
{
    /** @return Collection<int, TransferListing> */
    public function activeListings(): Collection;

    public function listPlayer(User $user, Player $player, CreateTransferListingData $data): TransferListing;

    public function cancelListing(User $user, Player $player): TransferListing;

    public function buy(User $buyer, TransferListing $listing): TransferListing;
}
