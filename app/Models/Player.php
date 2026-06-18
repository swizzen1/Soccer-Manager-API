<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\PlayerPosition;
use Database\Factories\PlayerFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function transferListing(): HasOne
    {
        return $this->hasOne(TransferListing::class);
    }
}
