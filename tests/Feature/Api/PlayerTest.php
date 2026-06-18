<?php

use App\Models\Player;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('team owner can update player editable fields', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->for($user)->create();
    $player = Player::factory()->for($team)->create();

    $this->actingAs($user, 'sanctum')
        ->putJson("/api/players/{$player->id}", [
            'first_name' => 'Updated',
            'last_name' => 'Player',
            'country' => 'Georgia',
        ])
        ->assertOk()
        ->assertJsonPath('data.first_name', 'Updated')
        ->assertJsonPath('data.last_name', 'Player')
        ->assertJsonPath('data.country', 'Georgia');
});

it('team owner can partially update player editable fields', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->for($user)->create();
    $player = Player::factory()->for($team)->create([
        'first_name' => 'Initial',
        'last_name' => 'Name',
        'country' => 'France',
    ]);

    $this->actingAs($user, 'sanctum')
        ->putJson("/api/players/{$player->id}", [
            'first_name' => 'Updated',
        ])
        ->assertOk()
        ->assertJsonPath('data.first_name', 'Updated')
        ->assertJsonPath('data.last_name', 'Name')
        ->assertJsonPath('data.country', 'France');
});

it('team owner cannot update locked player fields', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->for($user)->create();
    $player = Player::factory()->for($team)->create([
        'age' => 22,
        'market_value' => 1_000_000,
    ]);

    $this->actingAs($user, 'sanctum')
        ->putJson("/api/players/{$player->id}", [
            'first_name' => 'Allowed',
            'last_name' => 'Change',
            'country' => 'Georgia',
            'age' => 40,
            'market_value' => 9_000_000,
            'team_id' => 999,
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['age', 'market_value', 'team_id']);

    $player->refresh();

    expect($player->age)->toBe(22)
        ->and($player->first_name)->not->toBe('Allowed')
        ->and((float) $player->market_value)->toBe(1_000_000.0)
        ->and($player->team_id)->toBe($team->id);
});

it('other user cannot update another team player', function (): void {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $team = Team::factory()->for($owner)->create();
    Team::factory()->for($other)->create();
    $player = Player::factory()->for($team)->create();

    $this->actingAs($other, 'sanctum')
        ->putJson("/api/players/{$player->id}", [
            'first_name' => 'No',
            'last_name' => 'Access',
            'country' => 'Georgia',
        ])
        ->assertForbidden();
});
