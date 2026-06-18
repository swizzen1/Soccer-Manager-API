<?php

use App\Models\Team;
use App\Models\User;
use App\Services\TeamCreationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates one team for a user with starting budget and generated players', function (): void {
    $user = User::factory()->create(['name' => 'Manager']);

    $team = app(TeamCreationService::class)->createForUser($user);

    expect($team->user_id)->toBe($user->id)
        ->and($team->name)->toBe('Manager FC')
        ->and((float) $team->budget)->toBe((float) Team::STARTING_BUDGET)
        ->and($team->players)->toHaveCount(20);
});
