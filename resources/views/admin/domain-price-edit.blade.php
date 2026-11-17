@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa giá - HEXAGON Admin')
@section('topbar_title', 'Chỉnh sửa bảng giá')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hexagon-admin-pricing-customers-v5.css') }}?v=5">
@endpush

@section('content')
<a class="back-button" href="{{ route('admin.domain-prices.index') }}">← Quay lại bảng giá</a>
<div class="page-heading"><h1>Chỉnh sửa {{ $price->extension }}</h1><p>Giá bán áp dụng cho những đơn hàng được tạo sau khi cập nhật.</p></div>

@if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
@if($errors->any())<div class="alert alert-error" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

<section class="panel price-edit-panel hxv5-pricing" aria-labelledby="hxv5-edit-title">
    <div class="panel-heading"><h2 id="hxv5-edit-title">Thông tin bảng giá</h2></div>
    <form method="POST" action="{{ route('admin.domain-prices.update', ['id' => $price->id]) }}" class="price-edit-form">
        @csrf @method('PATCH')
        <div class="price-field"><label for="hxv5-current-extension">Đuôi tên miền</label><input id="hxv5-current-extension" value="{{ $price->extension }}" readonly></div>
        <div class="price-field"><label for="registration_price">Giá đăng ký (VNĐ)</label><input type="number" id="registration_price" name="registration_price" min="0" max="1000000000" step="1" value="{{ old('registration_price', $price->registration_price) }}" required></div>
        <div class="price-field hx-renewal-combined-field"><label for="renewal_price">Giá gia hạn (VNĐ)</label>
            <div class="hx-renewal-combined" role="group" aria-label="Giá gia hạn và thời hạn gia hạn">
                <input type="number" id="renewal_price" name="renewal_price" min="0" max="1000000000" step="1" value="{{ old('renewal_price', $price->renewal_price) }}" required>
                <select aria-label="Thời hạn gia hạn" id="renewal_period_months" class="hx-renewal-period-select" name="renewal_period_months" required>
                    @foreach([1, 3, 6, 12, 24] as $month)
                        <option value="{{ $month }}" @selected((int) old('renewal_period_months', $price->renewal_period_months ?? 12) === $month)>{{ $month }} tháng</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button type="submit" class="price-button">Lưu thay đổi</button>
    </form>
</section>

<section class="panel hxv5-pricing hxv5-audit" aria-labelledby="hxv5-history-title">
    <div class="panel-heading"><h2 id="hxv5-history-title">Lịch sử điều chỉnh</h2><span class="muted">12 lần gần nhất</span></div>
    <p class="hxv5-note">Chỉ ghi nhận các thao tác thực hiện sau khi cài V5. Giá đơn hàng đã đặt không bị thay đổi.</p>
    <div class="hxv5-audit-list">
        @forelse($history as $event)
            <article class="hxv5-audit-item">
                <div class="hxv5-audit-meta"><strong>{{ $event->action === 'created' ? 'Thêm đuôi tên miền' : ($event->action === 'toggled' ? 'Thay đổi trạng thái kinh doanh' : 'Điều chỉnh bảng giá') }}</strong><span>{{ \Illuminate\Support\Carbon::parse($event->created_at)->format('d/m/Y H:i') }} · {{ $event->admin_name }}</span></div>
                <ul>
                    @if($event->action === 'created' || $event->old_registration_price != $event->new_registration_price)
                        <li>Giá đăng ký: {{ $event->old_registration_price === null ? '—' : number_format($event->old_registration_price, 0, ',', '.') . 'đ' }} → {{ number_format($event->new_registration_price, 0, ',', '.') }}đ</li>
                    @endif
                    @if($event->action === 'created' || $event->old_renewal_price != $event->new_renewal_price)
                        <li>Giá gia hạn: {{ $event->old_renewal_price === null ? '—' : number_format($event->old_renewal_price, 0, ',', '.') . 'đ' }} → {{ number_format($event->new_renewal_price, 0, ',', '.') }}đ</li>
                    @endif
                    @if($event->action === 'created' || $event->old_renewal_period_months != $event->new_renewal_period_months)
                        <li>Thời hạn gia hạn: {{ $event->old_renewal_period_months === null ? '—' : $event->old_renewal_period_months . ' tháng' }} → {{ $event->new_renewal_period_months }} tháng</li>
                    @endif
                    @if($event->action === 'created' || $event->old_is_active != $event->new_is_active)
                        <li>Trạng thái: {{ $event->old_is_active === null ? '—' : ($event->old_is_active ? 'Đang bán' : 'Tạm ngừng') }} → {{ $event->new_is_active ? 'Đang bán' : 'Tạm ngừng' }}</li>
                    @endif
                </ul>
            </article>
        @empty
            <p class="hxv5-no-history">Chưa có thay đổi nào được ghi nhận từ khi nâng cấp V5.</p>
        @endforelse
    </div>
</section>
@endsection
