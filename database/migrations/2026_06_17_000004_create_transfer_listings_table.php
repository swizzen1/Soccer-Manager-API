<?php

use App\Enums\TransferListingStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transfer_listings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('player_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seller_team_id')->constrained('teams')->cascadeOnDelete();
            $table->decimal('asking_price', 15, 2);
            $table->string('status')->default(TransferListingStatus::ACTIVE->value);
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });

        match (DB::getDriverName()) {
            'sqlite', 'pgsql' => DB::statement(
                "CREATE UNIQUE INDEX transfer_listings_active_player_unique ON transfer_listings (player_id) WHERE status = 'active'"
            ),
            'mysql', 'mariadb' => DB::statement(
                "ALTER TABLE transfer_listings
                    ADD active_player_id BIGINT UNSIGNED GENERATED ALWAYS AS (
                        CASE WHEN status = 'active' THEN player_id ELSE NULL END
                    ) STORED,
                    ADD UNIQUE INDEX transfer_listings_active_player_unique (active_player_id)"
            ),
            'sqlsrv' => DB::statement(
                "CREATE UNIQUE INDEX transfer_listings_active_player_unique ON transfer_listings (player_id) WHERE status = 'active'"
            ),
            default => null,
        };
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_listings');
    }
};
