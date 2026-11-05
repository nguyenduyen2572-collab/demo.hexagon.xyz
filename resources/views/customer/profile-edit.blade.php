@extends('customer.layouts.settings')

@section('title', 'Chỉnh sửa hồ sơ - HEXAGON')
@section('heading', 'Chỉnh sửa hồ sơ')
@section('description', 'Cập nhật thông tin cá nhân của bạn.')

@section('content')
<section class="ca-card cs-card">
    <div class="cs-card-heading">
        <div class="cs-profile-avatar">{{ mb_strtoupper(mb_substr(trim($user->name), 0, 1)) }}</div>
        <div>
            <h2 class="ca-card-title">Thông tin cá nhân</h2>
            <p class="ca-note">Chỉ thông tin của tài khoản đang đăng nhập được phép chỉnh sửa.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('customer.account.profile.update') }}">
        @csrf
        @method('PATCH')

        <label class="cs-label" for="name">Họ và tên</label>
        <input class="cs-input" id="name" name="name" type="text"
               value="{{ old('name', $user->name) }}" maxlength="150"
               autocomplete="name" required>
        @error('name')<p class="cs-field-error">{{ $message }}</p>@enderror

        <label class="cs-label" for="email">Email tài khoản</label>
        <input class="cs-input" id="email" type="email" value="{{ $user->email }}" readonly>
        <p class="cs-help">Tạm thời chưa hỗ trợ thay đổi email. Chức năng này sẽ được bổ sung sau khi có xác minh email.</p>

        <div class="cs-actions">
            <button class="cs-primary" type="submit">Lưu thay đổi</button>
            <a class="cs-secondary" href="{{ route('customer.account.index') }}">Hủy</a>
        </div>
    </form>
</section>
@endsection
