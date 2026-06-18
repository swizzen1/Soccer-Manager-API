<?php

use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createTeamWithPlayers(int $players = 20): array
{
    $user = User::factory()->create();
    $team = Team::factory()->for($user)->create();
    Player::factory()->count($players)->for($team)->create(['market_value' => 1_000_000]);

    return [$user, $team];
}

it('authenticated user can see own team with calculated value', function (): void {
    [$user, $team] = createTeamWithPlayers();

    $this->actingAs($user, 'sanctum')
        ->getJson('/api/team')
        ->assertOk()
        ->assertJsonPath('data.id', $team->id)
        ->assertJsonPath('data.team_value', 20_000_000);
});

it('team owner can update team name and country', function (): void {
    [$user] = createTeamWithPlayers();

    $this->actingAs($user, 'sanctum')
        ->putJson('/api/team', [
            'name' => 'Dinamo API',
            'country' => 'Georgia',
        ])
        ->assertOk()
        ->assertJsonPath('data.name', 'Dinamo API')
        ->assertJsonPath('data.country', 'Georgia');
});

it('team owner can partially update team editable fields', function (): void {
    [$user, $team] = createTeamWithPlayers();

    $this->actingAs($user, 'sanctum')
        ->putJson('/api/team', [
            'name' => 'Dinamo API',
        ])
        ->assertOk()
        ->assertJsonPath('data.name', 'Dinamo API')
        ->assertJsonPath('data.country', $team->country);
});

it('team owner cannot update financial or ownership fields', function (): void {
    [$user, $team] = createTeamWithPlayers();

    $this->actingAs($user, 'sanctum')
        ->putJson('/api/team', [
            'name' => 'Dinamo API',
            'budget' => 99_000_000,
            'user_id' => User::factory()->create()->id,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['budget', 'user_id']);

    $team->refresh();

    expect($team->name)->not->toBe('Dinamo API')
        ->and((float) $team->budget)->toBe(5_000_000.0)
        ->and($team->user_id)->toBe($user->id);
});

it('user cannot access team endpoints without authentication', function (): void {
    $this->getJson('/api/team')
        ->assertUnauthorized()
        ->assertJsonPath('success', false);
});

it('uses georgian messages when accept language is ka', function (): void {
    [$user] = createTeamWithPlayers();

    $this->actingAs($user, 'sanctum')
        ->withHeader('Accept-Language', 'ka')
        ->getJson('/api/team')
        ->assertOk()
        ->assertJsonPath('message', 'გუნდი წარმატებით დაბრუნდა.');
});
