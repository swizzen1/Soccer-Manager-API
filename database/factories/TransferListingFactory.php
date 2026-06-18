<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\TransferListingStatus;
use App\Models\Player;
use App\Models\TransferListing;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TransferListing> */
class TransferListingFactory extends Factory
{
    protected $model = TransferListing::class;

    public function definition(): array
    {
        $player = Player::factory()->create();

        return [
            'player_id' => $player->id,
            'seller_team_id' => $player->team_id,
            'asking_price' => fake()->numberBetween(500_000, 3_000_000),
            'status' => TransferListingStatus::ACTIVE,
        ];
    }
}
