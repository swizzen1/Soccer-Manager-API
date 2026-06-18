<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\TransferListingRepositoryInterface;
use App\Enums\TransferListingStatus;
use App\Models\Player;
use App\Models\TransferListing;
use Illuminate\Database\Eloquent\Collection;

final class TransferListingRepository implements TransferListingRepositoryInterface
{
    public function activeListings(): Collection
    {
        return TransferListing::query()
            ->with(['player', 'sellerTeam'])
            ->where('status', TransferListingStatus::ACTIVE)
            ->latest()
            ->get();
    }

    public function hasActiveListing(Player $player): bool
    {
        return $player->transferListing()
            ->where('status', TransferListingStatus::ACTIVE)
            ->exists();
    }

    public function create(array $data): TransferListing
    {
        return TransferListing::query()->create($data)->load(['player', 'sellerTeam']);
    }

    public function activeForPlayer(Player $player): ?TransferListing
    {
        return $player->transferListing()
            ->where('status', TransferListingStatus::ACTIVE)
            ->first();
    }

    public function lockWithRelations(TransferListing $listing): TransferListing
    {
        return TransferListing::query()
            ->with(['player', 'sellerTeam'])
            ->lockForUpdate()
            ->findOrFail($listing->id);
    }

    public function updateStatus(TransferListing $listing, TransferListingStatus $status): TransferListing
    {
        $listing->update(['status' => $status]);

        return $listing->fresh()->load(['player', 'sellerTeam']);
    }
}
