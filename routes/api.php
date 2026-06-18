<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PlayerController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\TransferMarketController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/market', [TransferMarketController::class, 'index']);

Route::middleware('auth:sanctum')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/team', [TeamController::class, 'show']);
    Route::put('/team', [TeamController::class, 'update']);

    Route::get('/team/players', [PlayerController::class, 'index']);
    Route::get('/players/{player}', [PlayerController::class, 'show']);
    Route::put('/players/{player}', [PlayerController::class, 'update']);

    Route::post('/players/{player}/transfer-list', [TransferMarketController::class, 'store']);
    Route::delete('/players/{player}/transfer-list', [TransferMarketController::class, 'destroy']);
    Route::post('/market/{listing}/buy', [TransferMarketController::class, 'buy']);
});
