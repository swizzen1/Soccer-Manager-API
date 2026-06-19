<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Contracts\Repositories\TransferListingRepositoryInterface;
use App\Enums\TransferListingStatus;
use App\Models\Player;
use App\Models\TransferListing;
use Illuminate\Pagination\LengthAwarePaginator;

final class TransferListingRepository implements TransferListingRepositoryInterface
{
    /** @return array<int|string, mixed> */
    private function listingRelations(): array
    {
        return [
            'player',
            'sellerTeam' => fn ($query) => $query->withSum('players as players_market_value_sum', 'market_value'),
        ];
    }

    public function activeListings(int $perPage): LengthAwarePaginator
    {
        return TransferListing::query()
            ->with($this->listingRelations())
            ->where('status', TransferListingStatus::ACTIVE)
            ->latest()
            ->paginate($perPage);
    }

    public function hasActiveListing(Player $player): bool
    {
        return $player->activeTransferListing()->exists();
    }

    public function create(array $data): TransferListing
    {
        return TransferListing::query()->create($data)->load($this->listingRelations());
    }

    public function activeForPlayer(Player $player): ?TransferListing
    {
        return $player->activeTransferListing()->first();
    }

    public function lockWithRelations(TransferListing $listing): TransferListing
    {
        return TransferListing::query()
            ->with($this->listingRelations())
            ->lockForUpdate()
            ->findOrFail($listing->id);
    }

    public function updateStatus(TransferListing $listing, TransferListingStatus $status): TransferListing
    {
        $listing->update(['status' => $status]);

        return $listing->fresh()->load($this->listingRelations());
    }
}
