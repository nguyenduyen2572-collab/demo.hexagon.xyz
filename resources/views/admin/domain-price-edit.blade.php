
@extends('admin.layouts.app')

@section('title', 'Chỉnh sửa giá - HEXAGON Admin')
@section('topbar_title', 'Chỉnh sửa bảng giá')

@section('content')

<a class="back-button"
   href="{{ route('admin.domain-prices.index') }}">
    ← Quay lại bảng giá
</a>

<div class="page-heading">
    <h1>Chỉnh sửa giá {{ $price->extension }}</h1>
    <p>Cập nhật giá đăng ký và giá gia hạn.</p>
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

        <div class="price-field">
            <label for="renewal_price">
                Giá gia hạn (VNĐ)
            </label>

            <input type="number"
                   id="renewal_price"
                   name="renewal_price"
                   min="0"
                   max="1000000000"
                   step="1"
                   value="{{ old('renewal_price', $price->renewal_price) }}"
                   required>
        </div>

        <button type="submit" class="price-button">
            Lưu thay đổi
        </button>
    </form>
</section>

@endsection
