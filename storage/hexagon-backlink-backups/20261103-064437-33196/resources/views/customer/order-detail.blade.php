<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">
    <title>Chi tiết đơn hàng - HEXAGON</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            background: #f4f7fb;
            font-family: Arial, sans-serif;
            color: #172c49;
        }
        main {
            max-width: 800px;
            margin: 50px auto;
            padding: 0 20px;
        }
        .card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 28px;
            margin-top: 24px;
        }
        a {
            color: #1465d9;
            text-decoration: none;
            font-weight: 600;
        }
        .code {
            color: #64748b;
            overflow-wrap: anywhere;
        }
        .row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 16px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .total {
            font-weight: bold;
            font-size: 19px;
        }
    </style>
</head>
<body>
<main>
    <a href="{{ route('customer.account.index') }}">
        ← Quay lại tài khoản
    </a>

    <div class="card">
        <h1>Chi tiết đơn hàng</h1>
        <p class="code">{{ $order->order_code }}</p>

        <p>Ngày tạo: {{ $order->created_at }}</p>

        @php
            $labels = [
                'pending_demo' => 'Chờ xử lý',
                'processing_demo' => 'Đang xử lý',
                'completed_demo' => 'Đã xử lý',
                'cancelled_demo' => 'Đã hủy',
            ];
        @endphp

        <p>
            Trạng thái:
            <strong>
                {{ $labels[$order->status] ?? $order->status }}
            </strong>
        </p>

        <h2>Danh sách tên miền</h2>

        @foreach($items as $item)
            <div class="row">
                <div>
                    <strong>{{ $item->domain_name }}</strong>
                    <p>Đăng ký {{ $item->registration_years }} năm</p>
                </div>

                <strong>
                    {{ number_format($item->line_total, 0, ',', '.') }}đ
                </strong>
            </div>
        @endforeach

        <div class="row total">
            <span>Tổng giá trị</span>
            <span>
                {{ number_format($order->total_amount, 0, ',', '.') }}đ
            </span>
        </div>

        <p class="code">
            Đây là đơn hàng thử nghiệm, chưa thanh toán
            hoặc đăng ký tên miền thật.
        </p>
    </div>
</main>
</body>
</html>
