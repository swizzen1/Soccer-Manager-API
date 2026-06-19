<?php

declare(strict_types=1);

namespace App\Services\MarketValue;

use App\Contracts\MarketValue\PlayerMarketValueCalculatorInterface;
use App\Models\Player;
use App\Models\TransferListing;

final class RandomIncreasePlayerMarketValueCalculator implements PlayerMarketValueCalculatorInterface
{
    public function calculate(Player $player, TransferListing $listing): float
    {
        $oldValue = (float) $player->market_value;
        $percentage = random_int(10, 100);

        return round($oldValue + ($oldValue * $percentage / 100), 2);
    }
}
