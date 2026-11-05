
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

        <div class="price-field">
            <label for="renewal_price">
                Giá gia hạn (VNĐ)
            </label>
            <input id="renewal_price"
                   type="number"
                   name="renewal_price"
                   min="0"
                   max="1000000000"
                   step="1"
                   value="{{ old('renewal_price') }}"
                   required>
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

                        <td>{{ $price->updated_at }}</td>
                        
                        <td>
                            @if($price->is_active)
                                <span style="
                                    background:#dcfce7;
                                    color:#166534;
                                    padding:7px 12px;
                                    border-radius:20px;
                                    font-weight:600;
                                    font-size:12px;
                                ">
                                    Đang bán
                                </span>
                            @else
                                <span style="
                                    background:#f1f5f9;
                                    color:#64748b;
                                    padding:7px 12px;
                                    border-radius:20px;
                                    font-weight:600;
                                    font-size:12px;
                                ">
                                    Tạm ngừng
                                </span>
                            @endif
                        </td>


                        <td>
                            <a class="edit-button"
                               href="{{ route('admin.domain-prices.edit', ['id' => $price->id]) }}">
                                Chỉnh sửa
                                
<form
    method="POST"
    action="{{ route('admin.domain-prices.toggle', ['id' => $price->id]) }}"
    style="margin-top:10px;"
>
    @csrf
    @method('PATCH')

    <button
        type="submit"
        onclick="return confirm('Bạn chắc chắn muốn thay đổi trạng thái kinh doanh?')"
        style="
            padding:9px 13px;
            border-radius:8px;
            border:1px solid #cbd5e1;
            background:#ffffff;
            color:#172c49;
            cursor:pointer;
            font-weight:600;
        "
    >
        {{ $price->is_active ? 'Tắt kinh doanh' : 'Bật kinh doanh' }}
    </button>
</form>

                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-cell">
                            Chưa có bảng giá tên miền.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

@endsection
