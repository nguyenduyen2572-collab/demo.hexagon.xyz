<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DomainController;

// API mặc định của Laravel
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API tìm kiếm tên miền HEXAGON

Route::get(
    '/v1/domains/check',
    [DomainController::class, 'check']
)->middleware('throttle:hexagon-domain-search')->middleware(\App\Http\Middleware\HexagonDomainSearchCooldown::class);

