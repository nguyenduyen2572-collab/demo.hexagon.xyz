<?php

use App\Http\Controllers\SaasCatalogController;
use App\Http\Controllers\Admin\SaasServiceController;
use App\Http\Middleware\EnsureAdmin;
use Illuminate\Support\Facades\Route;

// Trang dịch vụ công khai - không phụ thuộc Checkout/Cart hiện có.
Route::get('/saas', [SaasCatalogController::class, 'index'])->name('saas.index');
Route::get('/saas/{slug}', [SaasCatalogController::class, 'show'])->name('saas.show');

// Chỉ Admin đã đăng nhập mới có quyền thêm/sửa/bật tắt dịch vụ.
Route::prefix('admin/saas')
    ->name('admin.saas.')
    ->middleware([\App\Http\Middleware\EnsureAdminSession::class, EnsureAdmin::class])
    ->group(function () {
        Route::get('/', [SaasServiceController::class, 'index'])->name('index');
        Route::post('/translate-preview', [SaasServiceController::class, 'translatePreview'])
            ->middleware('throttle:20,1')->name('translate-preview');
        Route::get('/create', [SaasServiceController::class, 'create'])->name('create');
        Route::post('/', [SaasServiceController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [SaasServiceController::class, 'edit'])
            ->whereNumber('id')->name('edit');
        Route::patch('/{id}', [SaasServiceController::class, 'update'])
            ->whereNumber('id')->name('update');
        Route::patch('/{id}/toggle', [SaasServiceController::class, 'toggle'])
            ->whereNumber('id')->name('toggle');
    });
