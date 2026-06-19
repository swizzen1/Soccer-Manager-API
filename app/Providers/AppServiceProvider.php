<?php

namespace App\Providers;

use App\Contracts\AuthServiceInterface;
use App\Contracts\MarketValue\PlayerMarketValueCalculatorInterface;
use App\Contracts\PlayerGenerationServiceInterface;
use App\Contracts\PlayerServiceInterface;
use App\Contracts\Repositories\PlayerRepositoryInterface;
use App\Contracts\Repositories\TeamRepositoryInterface;
use App\Contracts\Repositories\TransferListingRepositoryInterface;
use App\Contracts\Repositories\UserRepositoryInterface;
use App\Contracts\TeamCreationServiceInterface;
use App\Contracts\TeamServiceInterface;
use App\Contracts\TransferServiceInterface;
use App\Repositories\PlayerRepository;
use App\Repositories\TeamRepository;
use App\Repositories\TransferListingRepository;
use App\Repositories\UserRepository;
use App\Services\AuthService;
use App\Services\MarketValue\RandomIncreasePlayerMarketValueCalculator;
use App\Services\PlayerGenerationService;
use App\Services\PlayerService;
use App\Services\TeamCreationService;
use App\Services\TeamService;
use App\Services\TransferService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(TeamCreationServiceInterface::class, TeamCreationService::class);
        $this->app->bind(PlayerGenerationServiceInterface::class, PlayerGenerationService::class);
        $this->app->bind(TeamServiceInterface::class, TeamService::class);
        $this->app->bind(PlayerServiceInterface::class, PlayerService::class);
        $this->app->bind(TransferServiceInterface::class, TransferService::class);
        $this->app->bind(PlayerMarketValueCalculatorInterface::class, RandomIncreasePlayerMarketValueCalculator::class);

        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(TeamRepositoryInterface::class, TeamRepository::class);
        $this->app->bind(PlayerRepositoryInterface::class, PlayerRepository::class);
        $this->app->bind(TransferListingRepositoryInterface::class, TransferListingRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
