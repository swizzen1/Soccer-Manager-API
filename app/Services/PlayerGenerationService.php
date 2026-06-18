<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\PlayerGenerationServiceInterface;
use App\Contracts\Repositories\PlayerRepositoryInterface;
use App\Enums\PlayerPosition;
use App\Models\Player;
use App\Models\Team;

final class PlayerGenerationService implements PlayerGenerationServiceInterface
{
    public function __construct(private readonly PlayerRepositoryInterface $players) {}

    private const SQUAD_COMPOSITION = [
        PlayerPosition::GOALKEEPER->value => 3,
        PlayerPosition::DEFENDER->value => 6,
        PlayerPosition::MIDFIELDER->value => 6,
        PlayerPosition::ATTACKER->value => 5,
    ];

    /** @return list<Player> */
    public function generateForTeam(Team $team): array
    {
        $players = [];

        foreach (self::SQUAD_COMPOSITION as $position => $count) {
            for ($i = 0; $i < $count; $i++) {
                $players[] = $this->players->createForTeam($team, [
                    'first_name' => fake()->firstName(),
                    'last_name' => fake()->lastName(),
                    'country' => fake()->country(),
                    'position' => $position,
                    'age' => random_int(Player::MIN_AGE, Player::MAX_AGE),
                    'market_value' => Player::INITIAL_MARKET_VALUE,
                ]);
            }
        }

        return $players;
    }
}
