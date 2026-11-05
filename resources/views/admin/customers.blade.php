
@extends('admin.layouts.app')

@section('title', 'Khách hàng - HEXAGON Admin')
@section('topbar_title', 'Quản lý khách hàng')

@section('content')

<div class="page-heading">
    <h1>Quản lý khách hàng</h1>
    <p>Danh sách khách hàng đã đặt dịch vụ tên miền.</p>
</div>

<section class="panel">

    <div class="customer-toolbar">
        <form method="GET"
              action="{{ route('admin.customers.index') }}"
              class="customer-search">

            <input
                type="search"
                name="q"
                value="{{ $search }}"
                placeholder="Tìm tên, email hoặc số điện thoại..."
                maxlength="100"
            >

            <button type="submit">
                Tìm kiếm
            </button>

            @if($search !== '')
                <a href="{{ route('admin.customers.index') }}">
                    Xóa lọc
                </a>
            @endif
        </form>

        <span class="customer-muted">
            {{ $customers->total() }} khách hàng
        </span>
    </div>

    <div class="table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Khách hàng</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Số đơn</th>
                    <th>Tổng giá trị demo</th>
                    <th>Đơn gần nhất</th>
                    <th>Thao tác</th>
                </tr>
            </thead>

            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td>
                            <strong>
                                {{ $customer->customer_name }}
                            </strong>
                        </td>

                        <td>
                            {{ $customer->customer_email }}
                        </td>

                        <td>
                            {{ $customer->customer_phone }}
                        </td>

                        <td>
                            {{ $customer->order_count }}
                        </td>

                        <td>
                            <strong>
                                {{ number_format($customer->total_value, 0, ',', '.') }}đ
                            </strong>
                        </td>

                        <td>
                            {{ $customer->last_order_at }}
                        </td>

                        <td>
                            <a class="customer-action"
                               href="{{ route('admin.customers.show', ['id' => $customer->latest_order_id]) }}">
                                Xem chi tiết
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="customer-empty">
                            Không tìm thấy khách hàng phù hợp.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="customer-pagination">
        <span>
            Trang {{ $customers->currentPage() }}
            / {{ $customers->lastPage() }}
        </span>

        <div class="customer-page-links">
            @if($customers->previousPageUrl())
                <a href="{{ $customers->previousPageUrl() }}">
                    ← Trang trước
                </a>
            @endif

            @if($customers->nextPageUrl())
                <a href="{{ $customers->nextPageUrl() }}">
                    Trang sau →
                </a>
            @endif
        </div>
    </div>

</section>

@endsection
