<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Tài khoản của tôi - HEXAGON</title>
    <link rel="stylesheet" href="{{ asset('css/customer-area.css') }}?v=1">
</head>
<body>
<header class="ca-header">
    <a href="/" class="ca-logo">HEXAGON</a>
    <a href="/" class="ca-home">← Về trang chủ</a>
</header>
<main class="ca-main">
    <h1 class="ca-title">Tài khoản của tôi</h1>
    <p class="ca-subtitle">Thông tin tài khoản HEXAGON của bạn.</p>

    <div class="ca-profile-layout">
        <section class="ca-card" aria-label="Thông tin cá nhân">
            <div class="ca-avatar">
                {{ mb_strtoupper(mb_substr(trim($user->name), 0, 1)) }}
            </div>
            <div class="ca-name">{{ $user->name }}</div>
            <p class="ca-email">{{ $user->email }}</p>
            <div class="ca-divider"></div>
            <div class="ca-field">
                <span class="ca-field-label">Họ và tên</span>
                <span class="ca-field-value">{{ $user->name }}</span>
            </div>
            <div class="ca-field">
                <span class="ca-field-label">Email tài khoản</span>
                <span class="ca-field-value">{{ $user->email }}</span>
            </div>
        </section>

        <section class="ca-card">
            <h2 class="ca-card-title">Quản lý hồ sơ</h2>
            <p class="ca-note">Cập nhật thông tin và bảo mật tài khoản.</p>
            <a class="ca-action" href="{{ route('customer.account.profile.edit') }}">Chỉnh sửa hồ sơ →</a>
            <a class="ca-action ca-action-secondary" href="{{ route('customer.account.password.edit') }}">Đổi mật khẩu →</a>
            <div class="ca-divider"></div>
            <h2 class="ca-card-title">Đơn hàng của tôi</h2>
            <p class="ca-note">Danh sách đơn hàng được quản lý ở trang riêng.</p>
            <a class="ca-action ca-action-secondary" href="{{ route('customer.account.orders.index') }}">Xem đơn hàng →</a>
        </section>
    </div>
</main>
</body>
</html>
