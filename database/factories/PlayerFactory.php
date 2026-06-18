<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PlayerPosition;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Player> */
class PlayerFactory extends Factory
{
    protected $model = Player::class;

    public function definition(): array
    {
        return [
            'team_id' => Team::factory(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'country' => fake()->country(),
            'position' => fake()->randomElement(PlayerPosition::cases()),
            'age' => fake()->numberBetween(Player::MIN_AGE, Player::MAX_AGE),
            'market_value' => Player::INITIAL_MARKET_VALUE,
        ];
    }
}
