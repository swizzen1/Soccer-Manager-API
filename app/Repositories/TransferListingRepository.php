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

    /**
     * Retrieves a paginated list of active transfer listings, including their related player and seller team information, ordered by the most recent listings first.
     * @return LengthAwarePaginator
     * @param int $perPage The number of listings to display per page.
     */

    public function activeListings(int $perPage): LengthAwarePaginator
    {
        return TransferListing::query()
            ->with($this->listingRelations())
            ->where('status', TransferListingStatus::ACTIVE)
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Checks if the given player has an active transfer listing.
     * @return bool
     * @param Player $player The player to check for an active transfer listing.
     */

    public function hasActiveListing(Player $player): bool
    {
        return $player->activeTransferListing()->exists();
    }

    /**
     * Creates a new transfer listing with the provided data and returns the created instance, including its related player and seller team information.
     * @return TransferListing
     * @param array $data The data to create the transfer listing with.
     */

    public function create(array $data): TransferListing
    {
        return TransferListing::query()->create($data)->load($this->listingRelations());
    }

    /**
     * Retrieves the active transfer listing for the given player, including its related player and seller team information, or returns null if no active listing exists.
     * @return TransferListing|null
     * @param Player $player The player whose active transfer listing is to be retrieved.
     */

    public function activeForPlayer(Player $player): ?TransferListing
    {
        return $player->activeTransferListing()->first();
    }

    /**
     * Locks the given transfer listing for update and returns the locked instance, including its related player and seller team information.
     * @return TransferListing
     * @param TransferListing $listing The transfer listing to lock for update.
     */

    public function lockWithRelations(TransferListing $listing): TransferListing
    {
        return TransferListing::query()
            ->with($this->listingRelations())
            ->lockForUpdate()
            ->findOrFail($listing->id);
    }

    /**
     * Updates the status of the given transfer listing and returns the updated instance, including its related player and seller team information.
     * @return TransferListing
     * @param TransferListing $listing The transfer listing to update.
     * @param TransferListingStatus $status The new status to set for the transfer listing.
     */

    public function updateStatus(TransferListing $listing, TransferListingStatus $status): TransferListing
    {
        $listing->update(['status' => $status]);

        return $listing->fresh()->load($this->listingRelations());
    }
}
