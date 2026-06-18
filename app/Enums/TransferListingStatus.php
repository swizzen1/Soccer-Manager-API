<?php

declare(strict_types=1);

namespace App\Enums;

enum TransferListingStatus: string
{
    case ACTIVE = 'active';
    case SOLD = 'sold';
    case CANCELLED = 'cancelled';
}
