<?php

use App\Http\Controllers\Api\HexagonDomainIdeasController;
use App\Http\Middleware\HexagonDomainSearchCooldown;
use Illuminate\Support\Facades\Route;

// Loaded from routes/api.php. Laravel applies the /api prefix automatically.
// Reuse the existing 3-second search cooldown so suggestions and checks share it.
Route::get('/v1/domains/ideas', HexagonDomainIdeasController::class)
    ->middleware(HexagonDomainSearchCooldown::class)
    ->name('api.domains.ideas');
