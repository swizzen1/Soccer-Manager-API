<?php

use App\Enums\TransferListingStatus;
use App\Models\Player;
use App\Models\Team;
use App\Models\TransferListing;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('owner can list player on transfer market', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->for($user)->create();
    $player = Player::factory()->for($team)->create();

    $this->actingAs($user, 'sanctum')
        ->postJson("/api/players/{$player->id}/transfer-list", ['asking_price' => 1_500_000])
        ->assertCreated()
        ->assertJsonPath('data.asking_price', 1_500_000)
        ->assertJsonPath('data.status', TransferListingStatus::ACTIVE->value);
});

it('owner cannot list same player twice', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->for($user)->create();
    $player = Player::factory()->for($team)->create();
    TransferListing::factory()->create(['player_id' => $player->id, 'seller_team_id' => $team->id]);

    $this->actingAs($user, 'sanctum')
        ->postJson("/api/players/{$player->id}/transfer-list", ['asking_price' => 1_500_000])
        ->assertConflict();
});

it('users can see market list', function (): void {
    $listing = TransferListing::factory()->create();

    $this->getJson('/api/market')
        ->assertOk()
        ->assertJsonPath('data.0.id', $listing->id)
        ->assertJsonStructure(['data' => [['player', 'seller_team']]]);
});

it('buyer can buy listed player and transfer operation updates all values', function (): void {
    $seller = User::factory()->create();
    $buyer = User::factory()->create();
    $sellerTeam = Team::factory()->for($seller)->create(['budget' => 5_000_000]);
    $buyerTeam = Team::factory()->for($buyer)->create(['budget' => 5_000_000]);
    $player = Player::factory()->for($sellerTeam)->create(['market_value' => 1_000_000]);
    $listing = TransferListing::factory()->create([
        'player_id' => $player->id,
        'seller_team_id' => $sellerTeam->id,
        'asking_price' => 1_500_000,
    ]);

    $this->actingAs($buyer, 'sanctum')
        ->postJson("/api/market/{$listing->id}/buy")
        ->assertOk()
        ->assertJsonPath('data.status', TransferListingStatus::SOLD->value);

    $player->refresh();
    $sellerTeam->refresh();
    $buyerTeam->refresh();
    $listing->refresh();

    expect($buyerTeam->budget)->toBe('3500000.00')
        ->and($sellerTeam->budget)->toBe('6500000.00')
        ->and($player->team_id)->toBe($buyerTeam->id)
        ->and($listing->status)->toBe(TransferListingStatus::SOLD)
        ->and((float) $player->market_value)->toBeGreaterThanOrEqual(1_100_000.0)
        ->and((float) $player->market_value)->toBeLessThanOrEqual(2_000_000.0);
});

it('buyer cannot buy own player', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->for($user)->create();
    $player = Player::factory()->for($team)->create();
    $listing = TransferListing::factory()->create([
        'player_id' => $player->id,
        'seller_team_id' => $team->id,
    ]);

    $this->actingAs($user, 'sanctum')
        ->postJson("/api/market/{$listing->id}/buy")
        ->assertForbidden();
});

it('buyer cannot buy player without enough budget', function (): void {
    $seller = User::factory()->create();
    $buyer = User::factory()->create();
    $sellerTeam = Team::factory()->for($seller)->create();
    $buyerTeam = Team::factory()->for($buyer)->create(['budget' => 100]);
    $player = Player::factory()->for($sellerTeam)->create();
    $listing = TransferListing::factory()->create([
        'player_id' => $player->id,
        'seller_team_id' => $sellerTeam->id,
        'asking_price' => 1_500_000,
    ]);

    $this->actingAs($buyer, 'sanctum')
        ->postJson("/api/market/{$listing->id}/buy")
        ->assertUnprocessable();

    expect($buyerTeam->fresh()->budget)->toBe('100.00');
});
