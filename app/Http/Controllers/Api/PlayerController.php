<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\PlayerServiceInterface;
use App\DTOS\UpdatePlayerData;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthenticatedRequest;
use App\Http\Requests\Player\UpdatePlayerRequest;
use App\Http\Resources\PlayerResource;
use App\Models\Player;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

final class PlayerController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly PlayerServiceInterface $playerService) {}

    public function index(AuthenticatedRequest $request): JsonResponse
    {
        return $this->success(
            __('messages.player.index_success'),
            PlayerResource::collection($this->playerService->listForUser($request->user()))
        );
    }

    public function show(Player $player): JsonResponse
    {
        return $this->success(
            __('messages.player.show_success'),
            new PlayerResource($this->playerService->show($player))
        );
    }

    public function update(UpdatePlayerRequest $request, Player $player): JsonResponse
    {
        $player = $this->playerService->update($request->user(), $player, UpdatePlayerData::fromRequest($request));

        return $this->success(__('messages.player.updated'), new PlayerResource($player));
    }
}
