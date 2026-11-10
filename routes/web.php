<?php


use App\Http\Controllers\Admin\SeparatedAdminAuthController;
use App\Http\Controllers\Customer\CustomerSettingsController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDomainPriceController;
use App\Http\Controllers\Admin\AdminCustomerController;
use App\Http\Controllers\Customer\CustomerRegisterController;
use App\Http\Controllers\Customer\CustomerLoginController;
use App\Http\Controllers\Customer\CustomerAccountController;
use App\Http\Controllers\Customer\CustomerEmailVerificationController;
use App\Http\Controllers\Customer\CustomerPasswordResetController;

// Trang chủ HEXAGON
Route::get('/', function () {
    $prices = DB::table('domain_prices')
        ->where('is_active', true)
        ->orderBy('registration_price', 'asc')
        ->get();

    return view('home', [
        'prices' => $prices
    ]);
});


// Xác minh email: tên route cố định do Laravel sử dụng.
Route::middleware('auth')->group(function () {
    Route::get('/customer/email/verify',
        [CustomerEmailVerificationController::class, 'notice'])
        ->name('verification.notice');

    Route::get('/customer/email/verify/{id}/{hash}',
        [CustomerEmailVerificationController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');

    Route::post('/customer/email/verification-notification',
        [CustomerEmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

// Khôi phục mật khẩu khách hàng: KHÔNG dùng các URL /admin.
Route::get('/customer/forgot-password',
    [CustomerPasswordResetController::class, 'showForgot'])
    ->name('password.request');

Route::post('/customer/forgot-password',
    [CustomerPasswordResetController::class, 'sendLink'])
    ->middleware('throttle:5,1')
    ->name('password.email');

Route::get('/customer/reset-password/{token}',
    [CustomerPasswordResetController::class, 'showReset'])
    ->name('password.reset');

Route::post('/customer/reset-password',
    [CustomerPasswordResetController::class, 'reset'])
    ->middleware('throttle:5,1')
    ->name('password.update');

// Khu vực tài khoản khách hàng
Route::middleware(['auth', 'verified'])
    ->prefix('customer/account')
    ->name('customer.account.')
    ->group(function () {
        Route::get('/', [CustomerAccountController::class, 'index'])
            ->name('index');

        Route::get('/orders', [CustomerAccountController::class, 'orders'])
            ->name('orders.index');

        Route::get('/orders/{code}', [CustomerAccountController::class, 'showOrder'])
            ->whereUuid('code')
            ->name('orders.show');
        // Trang sửa hồ sơ
        Route::get('/edit', [CustomerSettingsController::class, 'editProfile'])
            ->name('profile.edit');

        // Lưu tên hồ sơ
        Route::patch('/profile', [CustomerSettingsController::class, 'updateProfile'])
            ->name('profile.update');

        // Trang đổi mật khẩu
        Route::get('/change-password', [CustomerSettingsController::class, 'editPassword'])
            ->name('password.edit');

        // Lưu mật khẩu mới; giảm nguy cơ đoán mật khẩu hiện tại
        Route::patch('/change-password', [CustomerSettingsController::class, 'updatePassword'])
            ->middleware('throttle:5,1')
            ->name('password.update');
    });



Route::get('/customer/login', [CustomerLoginController::class, 'showLogin'])
    ->name('customer.login');

Route::post('/customer/login', [CustomerLoginController::class, 'login'])
    ->middleware('throttle:5,1')
    ->name('customer.login.store');

Route::post('/customer/logout', [CustomerLoginController::class, 'logout'])
    ->middleware('auth')
    ->name('customer.logout');

// Trang đăng ký tài khoản khách hàng
Route::get('/customer/register', [
    CustomerRegisterController::class,
    'showRegister'
])->name('customer.register');

// Xử lý đăng ký khách hàng
Route::post('/customer/register', [
    CustomerRegisterController::class,
    'register'
])->middleware('throttle:5,1')
  ->name('customer.register.store');


// Xem giỏ hàng
Route::get('/cart', [CartController::class, 'index'])
    ->name('cart.index');

// Thêm vào giỏ hàng
Route::post('/cart/items', [CartController::class, 'store'])
    ->name('cart.store');

// Xóa khỏi giỏ hàng
Route::delete('/cart/items', [CartController::class, 'destroy'])
    ->name('cart.destroy');
    
// Trang nhập thông tin đặt hàng
Route::get('/checkout', [
    OrderController::class, 'create'
])->name('checkout.create')->middleware(\App\Http\Middleware\EnsureCheckoutCustomer::class);

// Xác nhận và lưu đơn hàng

Route::post('/checkout', [OrderController::class, 'store'])
    ->name('checkout.store')
    ->block(20, 10)->middleware(\App\Http\Middleware\EnsureCheckoutCustomer::class);


// Trang xác nhận đơn hàng
Route::get('/orders/{code}', [
    OrderController::class, 'show'
])->whereUuid('code')->name('orders.show');

Route::get('/admin', function () {
            return redirect()->route('admin.dashboard');
        })->name('admin.index');
// Trang đăng nhập Admin
Route::get('/admin/login', [
    SeparatedAdminAuthController::class, 'showLogin'
])->name('login');

Route::post('/admin/login', [
    SeparatedAdminAuthController::class, 'login'
])->middleware('throttle:5,1')
  ->name('admin.login.submit');

// Chỉ tài khoản đã đăng nhập và có quyền Admin

Route::middleware([\App\Http\Middleware\EnsureAdminSession::class, EnsureAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard tổng quan
        Route::get('/dashboard', [
            AdminDashboardController::class,
            'index'
        ])->name('dashboard');

        // Quản lý đơn hàng
        Route::get('/orders', [
            AdminOrderController::class,
            'index'
        ])->name('orders.index');
        
        // Xem chi tiết một đơn hàng
        Route::get('/orders/{id}', [
            AdminOrderController::class,
            'show'
        ])->whereNumber('id')->name('orders.show');

        // Cập nhật trạng thái đơn hàng
        Route::patch('/orders/{id}/status', [
            AdminOrderController::class,
            'updateStatus'
        ])->whereNumber('id')->name('orders.update-status')->middleware(\App\Http\Middleware\AuditOrderStatusTransition::class);
        
        // Danh sách giá tên miền
        Route::get('/domain-prices', [
            AdminDomainPriceController::class, 'index'
        ])->name('domain-prices.index');

        // Thêm đuôi tên miền mới
        Route::post('/domain-prices', [
            AdminDomainPriceController::class, 'store'
        ])->name('domain-prices.store');

        // Form chỉnh sửa giá
        Route::get('/domain-prices/{id}/edit', [
            AdminDomainPriceController::class, 'edit'
        ])->whereNumber('id')->name('domain-prices.edit');

        // Lưu thay đổi giá
        Route::patch('/domain-prices/{id}', [
            AdminDomainPriceController::class, 'update'
        ])->whereNumber('id')->name('domain-prices.update');
        
        // Bật hoặc tắt kinh doanh đuôi tên miền
        Route::patch('/domain-prices/{id}/toggle', [
            AdminDomainPriceController::class,
            'toggle'
        ])->whereNumber('id')->name('domain-prices.toggle');
        
        // Danh sách khách đã đặt hàng
        Route::get('/customers', [
            \App\Http\Controllers\Admin\AdminCustomerInsightsController::class,
            'index'
        ])->name('customers.index');

        // Chi tiết và lịch sử khách hàng
        Route::get('/customers/{id}', [
            \App\Http\Controllers\Admin\AdminCustomerInsightsController::class,
            'show'
        ])->whereNumber('id')->name('customers.show');



        // Đăng xuất Admin
        Route::post('/logout', [
            SeparatedAdminAuthController::class,
            'logout'
        ])->name('logout');

    });
    require __DIR__.'/hexagon_saas.php';

// HEXAGON public About page
require __DIR__.'/hexagon_about.php';
