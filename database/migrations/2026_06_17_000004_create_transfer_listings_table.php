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

        if (DB::getDriverName() === 'sqlite') {
            DB::statement(
                "CREATE UNIQUE INDEX transfer_listings_active_player_unique ON transfer_listings (player_id) WHERE status = 'active'"
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transfer_listings');
    }
};
