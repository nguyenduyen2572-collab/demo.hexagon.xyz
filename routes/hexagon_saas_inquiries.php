<?php

declare(strict_types=1);

use App\Http\Controllers\SaasInquiryController;
use App\Http\Controllers\Admin\SaasInquiryController as AdminSaasInquiryController;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\EnsureAdminSession;
use Illuminate\Support\Facades\Route;

Route::post('/saas/{slug}/inquiries', [SaasInquiryController::class, 'store'])
    ->where('slug', '[a-z0-9-]+')
    ->middleware('throttle:5,1')
    ->name('saas.inquiries.store');

Route::prefix('admin/saas-inquiries')
    ->name('admin.saas-inquiries.')
    ->middleware([EnsureAdminSession::class, EnsureAdmin::class])
    ->group(function (): void {
        Route::get('/', [AdminSaasInquiryController::class, 'index'])->name('index');
        Route::get('/{id}', [AdminSaasInquiryController::class, 'show'])->whereNumber('id')->name('show');
        Route::patch('/{id}', [AdminSaasInquiryController::class, 'update'])->whereNumber('id')->name('update');
    });
