<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\TransferServiceInterface;
use App\Contracts\Repositories\PlayerRepositoryInterface;
use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Contracts\Repositories\TransferListingRepositoryInterface;
use App\DTOS\CreateTransferListingData;
use App\Enums\TransferListingStatus;
use App\Models\Player;
use App\Models\TransferListing;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class TransferService implements TransferServiceInterface
{
    public function __construct(
        private readonly TransferListingRepositoryInterface $listings,
        private readonly TeamRepositoryInterface $teams,
        private readonly PlayerRepositoryInterface $players,
    ) {}

    /** @return Collection<int, TransferListing> */
    public function activeListings(): Collection
    {
        return $this->listings->activeListings();
    }

    public function listPlayer(User $user, Player $player, CreateTransferListingData $data): TransferListing
    {
        $player->loadMissing('team');

        Gate::forUser($user)->authorize('listForTransfer', $player);

        if ($this->listings->hasActiveListing($player)) {
            throw new ConflictHttpException(__('messages.transfer.already_listed'));
        }

        return $this->listings->create([
            'player_id' => $player->id,
            'seller_team_id' => $player->team_id,
            'asking_price' => $data->askingPrice,
            'status' => TransferListingStatus::ACTIVE,
        ]);
    }

    public function cancelListing(User $user, Player $player): TransferListing
    {
        $player->loadMissing('team');

        Gate::forUser($user)->authorize('cancelTransfer', $player);

        $listing = $this->listings->activeForPlayer($player);

        if (! $listing) {
            throw new ConflictHttpException(__('messages.transfer.no_active_listing'));
        }

        return $this->listings->updateStatus($listing, TransferListingStatus::CANCELLED);
    }

    public function buy(User $buyer, TransferListing $listing): TransferListing
    {
        return DB::transaction(function () use ($buyer, $listing): TransferListing {
            $listing = $this->listings->lockWithRelations($listing);

            Gate::forUser($buyer)->authorize('buy', $listing);

            if ($listing->status !== TransferListingStatus::ACTIVE) {
                throw new ConflictHttpException(__('messages.transfer.not_active'));
            }

            $buyerTeam = $this->teams->lockForUser($buyer);
            $sellerTeam = $this->teams->lock($listing->sellerTeam);
            $askingPrice = (float) $listing->asking_price;

            if ((float) $buyerTeam->budget < $askingPrice) {
                throw new UnprocessableEntityHttpException(__('messages.transfer.not_enough_budget'));
            }

            $this->teams->decrementBudget($buyerTeam, $askingPrice);
            $this->teams->incrementBudget($sellerTeam, $askingPrice);

            $player = $this->players->lock($listing->player);
            $oldValue = (float) $player->market_value;
            $percentage = random_int(10, 100);

            $this->players->update($player, [
                'team_id' => $buyerTeam->id,
                'market_value' => round($oldValue + ($oldValue * $percentage / 100), 2),
            ]);

            return $this->listings->updateStatus($listing, TransferListingStatus::SOLD);
        });
    }
}
