<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PlayerPosition;
use App\Enums\TransferListingStatus;
use Database\Factories\PlayerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property PlayerPosition $position
 * @property int $id
 * @property int $team_id
 * @property string $first_name
 * @property string $last_name
 * @property string $country
 * @property int $age
 * @property numeric $market_value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read TransferListing|null $activeTransferListing
 * @property-read Team $team
 * @property-read Collection<int, TransferListing> $transferListings
 * @property-read int|null $transfer_listings_count
 *
 * @method static \Database\Factories\PlayerFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player whereAge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player whereCountry($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player whereMarketValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Player whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
#[Fillable(['team_id', 'first_name', 'last_name', 'country', 'position', 'age', 'market_value'])]
class Player extends Model
{
    /** @use HasFactory<PlayerFactory> */
    use HasFactory;

    public const INITIAL_MARKET_VALUE = 1_000_000;

    public const MIN_AGE = 18;

    public const MAX_AGE = 40;

    protected function casts(): array
    {
        return [
            'position' => PlayerPosition::class,
            'market_value' => 'decimal:2',
            'age' => 'integer',
        ];
    }

    /** @return BelongsTo<Team, $this> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /** @return HasMany<TransferListing, $this> */
    public function transferListings(): HasMany
    {
        return $this->hasMany(TransferListing::class);
    }

    /** @return HasOne<TransferListing, $this> */
    public function activeTransferListing(): HasOne
    {
        return $this->hasOne(TransferListing::class)
            ->where('status', TransferListingStatus::ACTIVE);
    }
}
