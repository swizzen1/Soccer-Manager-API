<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TransferListingStatus;
use Database\Factories\TransferListingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['player_id', 'seller_team_id', 'asking_price', 'status'])]
class TransferListing extends Model
{
    /** @use HasFactory<TransferListingFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'asking_price' => 'decimal:2',
            'status' => TransferListingStatus::class,
        ];
    }

    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    public function sellerTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'seller_team_id');
    }
}
