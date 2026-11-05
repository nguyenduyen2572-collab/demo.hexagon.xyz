<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đơn hàng của tôi - HEXAGON</title>
    <link rel="stylesheet" href="{{ asset('css/customer-area.css') }}?v=1">
</head>
<body>
<header class="ca-header">
    <a href="/" class="ca-logo">HEXAGON</a>
    <a href="{{ route('customer.account.index') }}" class="ca-home">Tài khoản của tôi</a>
</header>
<main class="ca-main">
    <h1 class="ca-title">Đơn hàng của tôi</h1>
    <p class="ca-subtitle">Theo dõi lịch sử đơn hàng đã liên kết với tài khoản đăng nhập.</p>

    <section class="ca-card">
        <div class="ca-toolbar">
            <span class="ca-muted">Tổng số đơn: <strong>{{ $orders->total() }}</strong></span>
            <a class="ca-action ca-action-secondary" style="margin:0" href="{{ route('customer.account.index') }}">← Quay lại tài khoản</a>
        </div>

        @php
            $labels = [
                'pending_demo' => 'Chờ xử lý',
                'processing_demo' => 'Đang xử lý',
                'completed_demo' => 'Đã xử lý',
                'cancelled_demo' => 'Đã hủy',
            ];
            $classes = [
                'pending_demo' => 'ca-pending',
                'processing_demo' => 'ca-processing',
                'completed_demo' => 'ca-completed',
                'cancelled_demo' => 'ca-cancelled',
            ];
        @endphp

        <div class="ca-table-scroll">
            <table class="ca-table">
                <thead>
                    <tr>
                        <th>Mã đơn hàng</th>
                        <th>Giá trị</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="ca-code">
                                <a href="{{ route('customer.account.orders.show', ['code' => $order->order_code]) }}">{{ $order->order_code }}</a>
                            </td>
                            <td><strong>{{ number_format($order->total_amount, 0, ',', '.') }}đ</strong></td>
                            <td><span class="ca-status {{ $classes[$order->status] ?? 'ca-unknown' }}">{{ $labels[$order->status] ?? $order->status }}</span></td>
                            <td>{{ $order->created_at }}</td>
                            <td><a href="{{ route('customer.account.orders.show', ['code' => $order->order_code]) }}">Xem đơn</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td class="ca-empty" colspan="5">Bạn chưa có đơn hàng nào liên kết với tài khoản này.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="ca-pages">
            <div>
                @if($orders->previousPageUrl())
                    <a href="{{ $orders->previousPageUrl() }}">← Trang trước</a>
                @endif
            </div>
            <span>Trang {{ $orders->currentPage() }} / {{ $orders->lastPage() }}</span>
            <div>
                @if($orders->nextPageUrl())
                    <a href="{{ $orders->nextPageUrl() }}">Trang sau →</a>
                @endif
            </div>
        </div>
    </section>
</main>
</body>
</html>
