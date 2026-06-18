<?php

use App\Enums\PlayerPosition;
use App\Models\Team;
use App\Models\User;
use App\Services\PlayerGenerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('generates the required squad composition', function (): void {
    $team = Team::factory()->for(User::factory())->create();

    app(PlayerGenerationService::class)->generateForTeam($team);

    expect($team->players)->toHaveCount(20)
        ->and($team->players()->where('position', PlayerPosition::GOALKEEPER)->count())->toBe(3)
        ->and($team->players()->where('position', PlayerPosition::DEFENDER)->count())->toBe(6)
        ->and($team->players()->where('position', PlayerPosition::MIDFIELDER)->count())->toBe(6)
        ->and($team->players()->where('position', PlayerPosition::ATTACKER)->count())->toBe(5);
});

it('generates players with the required age range and initial market value', function (): void {
    $team = Team::factory()->for(User::factory())->create();

    app(PlayerGenerationService::class)->generateForTeam($team);

    $team->players()->each(function ($player): void {
        expect($player->age)->toBeGreaterThanOrEqual(18)
            ->and($player->age)->toBeLessThanOrEqual(40)
            ->and((float) $player->market_value)->toBe(1_000_000.0);
    });
});
