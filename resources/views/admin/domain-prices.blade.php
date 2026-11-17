@extends('admin.layouts.app')

@section('title', 'Bảng giá tên miền - HEXAGON Admin')
@section('topbar_title', 'Quản lý bảng giá tên miền')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/hexagon-admin-pricing-customers-v5.css') }}?v=5">
@endpush

@section('content')
<div class="page-heading">
    <h1>Bảng giá tên miền</h1>
    <p>Quản lý giá đăng ký, gia hạn và trạng thái kinh doanh.</p>
</div>

@if(session('success'))<div class="alert alert-success" role="status">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-error" role="alert">{{ session('error') }}</div>@endif
@if($errors->any())
    <div class="alert alert-error" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
@endif

<section class="panel price-panel hxv5-pricing" aria-labelledby="hxv5-add-title">
    <div class="panel-heading"><h2 id="hxv5-add-title">Thêm đuôi tên miền</h2></div>
    <form class="price-form" method="POST" action="{{ route('admin.domain-prices.store') }}">
        @csrf
        <div class="price-field">
            <label for="extension">Đuôi tên miền</label>
            <input id="extension" name="extension" placeholder=".org" value="{{ old('extension') }}" maxlength="30" required>
        </div>
        <div class="price-field">
            <label for="registration_price">Giá đăng ký (VNĐ)</label>
            <input id="registration_price" type="number" name="registration_price" min="0" max="1000000000" step="1" value="{{ old('registration_price') }}" required>
        </div>
        <div class="price-field hx-renewal-combined-field">
            <label for="renewal_price">Giá gia hạn (VNĐ)</label>
            <div class="hx-renewal-combined" role="group" aria-label="Giá gia hạn và thời hạn gia hạn">
                <input id="renewal_price" type="number" name="renewal_price" min="0" max="1000000000" step="1" value="{{ old('renewal_price') }}" required>
                <select aria-label="Thời hạn gia hạn" id="renewal_period_months" class="hx-renewal-period-select" name="renewal_period_months" required>
                    @foreach([1, 3, 6, 12, 24] as $month)
                        <option value="{{ $month }}" @selected((int) old('renewal_period_months', 12) === $month)>{{ $month }} tháng</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button class="price-button" type="submit">+ Thêm đuôi tên miền</button>
    </form>
</section>

<section class="panel hxv5-pricing" aria-labelledby="hxv5-price-list">
    <div class="panel-heading hxv5-title-row">
        <h2 id="hxv5-price-list">Danh sách bảng giá</h2>
        <span class="muted">{{ number_format($prices->total(), 0, ',', '.') }} kết quả</span>
    </div>
    <form class="hxv5-filters" action="{{ route('admin.domain-prices.index') }}" method="GET" aria-label="Lọc bảng giá tên miền">
        <label><span>Tìm đuôi tên miền</span><input type="search" name="q" value="{{ $filters['q'] }}" placeholder="Ví dụ: .com" maxlength="30"></label>
        <label><span>Trạng thái</span><select name="status">
            <option value="all" @selected($filters['status'] === 'all')>Tất cả</option>
            <option value="active" @selected($filters['status'] === 'active')>Đang bán</option>
            <option value="inactive" @selected($filters['status'] === 'inactive')>Tạm ngừng</option>
        </select></label>
        <label><span>Sắp xếp</span><select name="sort">
            <option value="extension" @selected($filters['sort'] === 'extension')>Theo đuôi tên miền</option>
            <option value="price_asc" @selected($filters['sort'] === 'price_asc')>Giá đăng ký tăng dần</option>
            <option value="price_desc" @selected($filters['sort'] === 'price_desc')>Giá đăng ký giảm dần</option>
            <option value="updated" @selected($filters['sort'] === 'updated')>Vừa cập nhật</option>
        </select></label>
        <div class="hxv5-filter-actions"><button type="submit" class="hxv5-primary">Lọc</button><a href="{{ route('admin.domain-prices.index') }}" class="hxv5-reset">Đặt lại</a></div>
    </form>

    <div class="table-wrap">
        <table class="admin-table">
            <thead><tr>
                <th>Đuôi tên miền</th><th>Giá đăng ký</th><th>Giá gia hạn</th>
                <th>Thời hạn gia hạn</th><th>Cập nhật</th><th>Trạng thái</th><th>Thao tác</th>
            </tr></thead>
            <tbody>
                @forelse($prices as $price)
                    <tr>
                        <td><strong>{{ $price->extension }}</strong></td>
                        <td>{{ number_format($price->registration_price, 0, ',', '.') }}đ</td>
                        <td>{{ number_format($price->renewal_price, 0, ',', '.') }}đ</td>
                        <td><span class="hx-renewal-period-cell">{{ (int) ($price->renewal_period_months ?? 12) }} tháng</span></td>
                        <td>{{ \Illuminate\Support\Carbon::parse($price->updated_at)->format('d/m/Y') }}</td>
                        <td><span class="hx-price-status {{ $price->is_active ? '' : 'is-off' }}">{{ $price->is_active ? 'Đang bán' : 'Tạm ngừng' }}</span></td>
                        <td><div class="hx-price-actions">
                            <a class="edit-button" href="{{ route('admin.domain-prices.edit', ['id' => $price->id]) }}">Chỉnh sửa</a>
                            <form method="POST" action="{{ route('admin.domain-prices.toggle', ['id' => $price->id]) }}" onsubmit="return confirm('Bạn có chắc muốn {{ $price->is_active ? 'tạm ngừng' : 'bật kinh doanh' }} {{ $price->extension }}?')">
                                @csrf @method('PATCH')
                                <button type="submit" class="hx-inline-toggle">{{ $price->is_active ? 'Tạm ngừng' : 'Bật kinh doanh' }}</button>
                            </form>
                        </div></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="empty-cell">Không tìm thấy đuôi tên miền phù hợp với bộ lọc.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($prices->hasPages())<div class="hxv5-pagination">{{ $prices->links() }}</div>@endif
</section>
@endsection
