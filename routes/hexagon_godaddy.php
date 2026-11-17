<?php

use App\Http\Controllers\Api\HexagonDomainIdeasController;
use App\Http\Middleware\HexagonDomainIdeasThrottle;
use Illuminate\Support\Facades\Route;

// Loaded from routes/api.php. Laravel applies the /api prefix automatically.
// Dedicated suggestion limiter; domain checks retain the existing 3-second cooldown.
Route::get('/v1/domains/ideas', HexagonDomainIdeasController::class)
    ->middleware(HexagonDomainIdeasThrottle::class)
    ->name('api.domains.ideas');
