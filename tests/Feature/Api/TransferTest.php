<?php

use App\Contracts\MarketValue\PlayerMarketValueCalculatorInterface;
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

it('owner can relist player after cancelling active listing', function (): void {
    $user = User::factory()->create();
    $team = Team::factory()->for($user)->create();
    $player = Player::factory()->for($team)->create();
    TransferListing::factory()->create([
        'player_id' => $player->id,
        'seller_team_id' => $team->id,
        'status' => TransferListingStatus::CANCELLED,
    ]);

    $this->actingAs($user, 'sanctum')
        ->postJson("/api/players/{$player->id}/transfer-list", ['asking_price' => 1_500_000])
        ->assertCreated()
        ->assertJsonPath('data.status', TransferListingStatus::ACTIVE->value);
});

it('user cannot list another teams player', function (): void {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $team = Team::factory()->for($owner)->create();
    Team::factory()->for($other)->create();
    $player = Player::factory()->for($team)->create();

    $this->actingAs($other, 'sanctum')
        ->postJson("/api/players/{$player->id}/transfer-list", ['asking_price' => 1_500_000])
        ->assertForbidden();
});

it('users can see market list', function (): void {
    $listing = TransferListing::factory()->create();

    $this->getJson('/api/market')
        ->assertOk()
        ->assertJsonPath('data.0.id', $listing->id)
        ->assertJsonPath('meta.per_page', 15)
        ->assertJsonPath('meta.total', 1)
        ->assertJsonStructure(['data' => [['player', 'seller_team']]]);
});

it('users can paginate market list', function (): void {
    TransferListing::factory()->count(3)->create();

    $this->getJson('/api/market?per_page=2')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('meta.per_page', 2)
        ->assertJsonPath('meta.total', 3);
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

it('uses the configured market value strategy when buying a player', function (): void {
    $this->app->bind(
        PlayerMarketValueCalculatorInterface::class,
        fn () => new class implements PlayerMarketValueCalculatorInterface
        {
            public function calculate(Player $player, TransferListing $listing): float
            {
                return 2_500_000.0;
            }
        }
    );

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
        ->assertOk();

    expect((float) $player->fresh()->market_value)->toBe(2_500_000.0)
        ->and($buyerTeam->fresh()->budget)->toBe('3500000.00');
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

it('buyer cannot buy inactive listing', function (): void {
    $seller = User::factory()->create();
    $buyer = User::factory()->create();
    $sellerTeam = Team::factory()->for($seller)->create();
    Team::factory()->for($buyer)->create();
    $player = Player::factory()->for($sellerTeam)->create();
    $listing = TransferListing::factory()->create([
        'player_id' => $player->id,
        'seller_team_id' => $sellerTeam->id,
        'status' => TransferListingStatus::CANCELLED,
    ]);

    $this->actingAs($buyer, 'sanctum')
        ->postJson("/api/market/{$listing->id}/buy")
        ->assertConflict();
});

it('buyer cannot buy stale listing when player no longer belongs to seller', function (): void {
    $seller = User::factory()->create();
    $buyer = User::factory()->create();
    $other = User::factory()->create();
    $sellerTeam = Team::factory()->for($seller)->create();
    Team::factory()->for($buyer)->create();
    $otherTeam = Team::factory()->for($other)->create();
    $player = Player::factory()->for($sellerTeam)->create();
    $listing = TransferListing::factory()->create([
        'player_id' => $player->id,
        'seller_team_id' => $sellerTeam->id,
        'status' => TransferListingStatus::ACTIVE,
    ]);

    $player->update(['team_id' => $otherTeam->id]);

    $this->actingAs($buyer, 'sanctum')
        ->postJson("/api/market/{$listing->id}/buy")
        ->assertConflict();
});
