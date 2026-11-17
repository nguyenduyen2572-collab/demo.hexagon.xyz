
@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa giá - HEXAGON Admin')
@section('topbar_title', 'Chỉnh sửa bảng giá')

@section('content')

<a class="back-button"
   href="{{ route('admin.domain-prices.index') }}">
    ← Quay lại bảng giá
</a>

<div class="page-heading">
    <h1>Chỉnh sửa giá của đuôi tên miền " {{ $price->extension }}"</h1>
</div>

@if($errors->any())
    <div class="alert alert-error">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
@endif

<section class="panel price-edit-panel">

    <div class="panel-heading">
        <h2>Thông tin bảng giá</h2>
    </div>

    <form method="POST"
          action="{{ route('admin.domain-prices.update', ['id' => $price->id]) }}"
          class="price-edit-form">
        @csrf
        @method('PATCH')

        <div class="price-field">
            <label>Đuôi tên miền</label>
            <input value="{{ $price->extension }}"
                   readonly>
        </div>

        <div class="price-field">
            <label for="registration_price">
                Giá đăng ký (VNĐ)
            </label>

            <input type="number"
                   id="registration_price"
                   name="registration_price"
                   min="0"
                   max="1000000000"
                   step="1"
                   value="{{ old('registration_price', $price->registration_price) }}"
                   required>
        </div>

        <!-- HX_RENEWAL_PERIOD_V1_1_GROUP -->
        <div class="price-field hx-renewal-combined-field">
            <label for="renewal_price">
                Giá gia hạn (VNĐ)
            </label>
            <div class="hx-renewal-combined" role="group" aria-label="Giá gia hạn và thời hạn gia hạn">
                <input type="number"
                    id="renewal_price"
                    name="renewal_price"
                    min="0"
                    max="1000000000"
                    step="1"
                    value="{{ old('renewal_price', $price->renewal_price) }}"
                    required>
                <select aria-label="Thời hạn gia hạn" id="renewal_period_months"
                    class="hx-renewal-period-select"
                    name="renewal_period_months"
                    required>
                    @foreach([1, 3, 6, 12, 24] as $month)
                        <option value="{{ $month }}" @selected((int) old('renewal_period_months', $price->renewal_period_months ?? 12) === $month)>{{ $month }} tháng</option>
                    @endforeach
                </select>
            </div>
        </div>

        <button type="submit" class="price-button">
            Lưu thay đổi
        </button>
    </form>
</section>

@endsection
