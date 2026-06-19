<?php

declare(strict_types=1);

namespace App\Contracts\MarketValue;

use App\Models\Player;
use App\Models\TransferListing;

interface PlayerMarketValueCalculatorInterface
{
    public function calculate(Player $player, TransferListing $listing): float;
}
