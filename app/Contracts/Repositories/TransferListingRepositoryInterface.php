<?php

declare(strict_types=1);

namespace App\Contracts\Repositories;

use App\Enums\TransferListingStatus;
use App\Models\Player;
use App\Models\TransferListing;
use Illuminate\Database\Eloquent\Collection;

interface TransferListingRepositoryInterface
{
    /** @return Collection<int, TransferListing> */
    public function activeListings(): Collection;

    public function hasActiveListing(Player $player): bool;

    /** @param array{player_id: int, seller_team_id: int, asking_price: float, status: TransferListingStatus} $data */
    public function create(array $data): TransferListing;

    public function activeForPlayer(Player $player): ?TransferListing;

    public function lockWithRelations(TransferListing $listing): TransferListing;

    public function updateStatus(TransferListing $listing, TransferListingStatus $status): TransferListing;
}
