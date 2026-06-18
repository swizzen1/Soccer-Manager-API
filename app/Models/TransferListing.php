<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\TransferListingStatus;
use Database\Factories\TransferListingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property TransferListingStatus $status
 * @property int $id
 * @property int $player_id
 * @property int $seller_team_id
 * @property numeric $asking_price
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Player $player
 * @property-read Team $sellerTeam
 *
 * @method static \Database\Factories\TransferListingFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransferListing newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransferListing newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransferListing query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransferListing whereAskingPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransferListing whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransferListing whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransferListing wherePlayerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransferListing whereSellerTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransferListing whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|TransferListing whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
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

    /** @return BelongsTo<Player, $this> */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /** @return BelongsTo<Team, $this> */
    public function sellerTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'seller_team_id');
    }
}
