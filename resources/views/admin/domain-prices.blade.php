
@extends('admin.layouts.app')

@section('title', 'Bảng giá tên miền - HEXAGON Admin')
@section('topbar_title', 'Quản lý bảng giá tên miền')

@section('content')

<div class="page-heading">
    <h1>Quản lý bảng giá tên miền</h1>
    <p>Thêm và cập nhật giá dịch vụ tên miền HEXAGON.</p>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif


@if(session('error'))
    <div class="alert alert-error">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-error">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<!-- THÊM TÊN MIỀN MỚI -->
<section class="panel price-panel">

    <div class="panel-heading">
        <h2>Thêm đuôi tên miền mới</h2>
    </div>

    <form class="price-form"
          method="POST"
          action="{{ route('admin.domain-prices.store') }}">
        @csrf

        <div class="price-field">
            <label for="extension">Đuôi tên miền</label>
            <input id="extension"
                   name="extension"
                   placeholder=".org"
                   value="{{ old('extension') }}"
                   maxlength="30"
                   required>
        </div>

        <div class="price-field">
            <label for="registration_price">
                Giá đăng ký (VNĐ)
            </label>
            <input id="registration_price"
                   type="number"
                   name="registration_price"
                   min="0"
                   max="1000000000"
                   step="1"
                   value="{{ old('registration_price') }}"
                   required>
        </div>

        <!-- HX_RENEWAL_PERIOD_V1_1_GROUP -->
        <div class="price-field hx-renewal-combined-field">
            <label for="renewal_price">
                Giá gia hạn (VNĐ)
            </label>
            <div class="hx-renewal-combined" role="group" aria-label="Giá gia hạn và thời hạn gia hạn">
                <input id="renewal_price"
                    type="number"
                    name="renewal_price"
                    min="0"
                    max="1000000000"
                    step="1"
                    value="{{ old('renewal_price') }}"
                    required>
                <select aria-label="Thời hạn gia hạn" id="renewal_period_months"
                    class="hx-renewal-period-select"
                    name="renewal_period_months"
                    required>
                    @foreach([1, 3, 6, 12, 24] as $month)
                        <option value="{{ $month }}" @selected((int) old('renewal_period_months', 12) === $month)>{{ $month }} tháng</option>
                    @endforeach
                </select>
            </div>
        </div>

        <button class="price-button" type="submit">
            + Thêm tên miền
        </button>
    </form>
</section>

<!-- DANH SÁCH BẢNG GIÁ -->
<section class="panel">

    <div class="panel-heading">
        <h2>Danh sách bảng giá</h2>
        <span class="muted">
            {{ $prices->count() }} đuôi tên miền
        </span>
    </div>

    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Đuôi tên miền</th>
                    <th>Giá đăng ký</th>
                    <th>Giá gia hạn</th>
                    <!-- HX_RENEWAL_PERIOD_V1_TABLE -->
                    <th>Thời hạn gia hạn</th>
                    <th>Cập nhật</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>

            <tbody>
                @forelse($prices as $price)
                    <tr>
                        <td>{{ $price->id }}</td>

                        <td>
                            <strong>{{ $price->extension }}</strong>
                        </td>

                        <td>
                            {{ number_format($price->registration_price, 0, ',', '.') }}đ
                        </td>

                        <td>
                            {{ number_format($price->renewal_price, 0, ',', '.') }}đ
                        </td>

                        <td><span class="hx-renewal-period-cell">{{ (int) ($price->renewal_period_months ?? 12) }} tháng</span></td>

                        <td>{{ \Illuminate\Support\Carbon::parse($price->updated_at)->format('d/m/Y') }}</td>
                        
                        <td>
                            <span class="hx-price-status {{ $price->is_active ? '' : 'is-off' }}">{{ $price->is_active ? 'Đang bán' : 'Tạm ngừng' }}</span>
                        </td>
                        <td>
                            <div class="hx-price-actions">
                                <a class="edit-button" href="{{ route('admin.domain-prices.edit', ['id' => $price->id]) }}">Chỉnh sửa</a>
                                <form method="POST" action="{{ route('admin.domain-prices.toggle', ['id' => $price->id]) }}" onsubmit="return confirm('Bạn có chắc muốn thay đổi trạng thái kinh doanh?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="hx-inline-toggle">{{ $price->is_active ? 'Tạm ngừng' : 'Bật kinh doanh' }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="empty-cell">
                            Chưa có bảng giá tên miền.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

@endsection
