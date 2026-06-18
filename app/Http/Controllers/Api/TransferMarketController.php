<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\TransferServiceInterface;
use App\DTOS\CreateTransferListingData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transfer\BuyTransferListingRequest;
use App\Http\Requests\Transfer\CancelTransferListingRequest;
use App\Http\Requests\Transfer\CreateTransferListingRequest;
use App\Http\Resources\TransferListingResource;
use App\Models\Player;
use App\Models\TransferListing;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

final class TransferMarketController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly TransferServiceInterface $transferService) {}

    public function index(): JsonResponse
    {
        return $this->success(
            __('messages.transfer.index_success'),
            TransferListingResource::collection($this->transferService->activeListings())
        );
    }

    public function store(CreateTransferListingRequest $request, Player $player): JsonResponse
    {
        $listing = $this->transferService->listPlayer(
            $request->user(),
            $player,
            CreateTransferListingData::fromRequest($request)
        );

        return $this->success(__('messages.transfer.listed'), new TransferListingResource($listing), 201);
    }

    public function destroy(CancelTransferListingRequest $request, Player $player): JsonResponse
    {
        $listing = $this->transferService->cancelListing($request->user(), $player);

        return $this->success(__('messages.transfer.cancelled'), new TransferListingResource($listing));
    }

    public function buy(BuyTransferListingRequest $request, TransferListing $listing): JsonResponse
    {
        $listing = $this->transferService->buy($request->user(), $listing);

        return $this->success(__('messages.transfer.sold'), new TransferListingResource($listing));
    }
}
