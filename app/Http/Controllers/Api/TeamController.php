<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Contracts\TeamServiceInterface;
use App\DTOS\UpdateTeamData;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthenticatedRequest;
use App\Http\Requests\Team\UpdateTeamRequest;
use App\Http\Resources\TeamResource;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

final class TeamController extends Controller
{
    use ApiResponse;

    public function __construct(private readonly TeamServiceInterface $teamService) {}

    public function show(AuthenticatedRequest $request): JsonResponse
    {
        return $this->success(
            __('messages.team.show_success'),
            new TeamResource($this->teamService->getForUser($request->user()))
        );
    }

    public function update(UpdateTeamRequest $request): JsonResponse
    {
        $team = $this->teamService->update($request->user(), UpdateTeamData::fromRequest($request));

        return $this->success(__('messages.team.updated'), new TeamResource($team));
    }
}
