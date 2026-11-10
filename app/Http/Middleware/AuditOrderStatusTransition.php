<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\HexagonOrderStatus;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

/**
 * Đặt trước AdminOrderController@updateStatus.
 * Khóa dòng đơn hàng đến khi Controller xử lý xong, sau đó chỉ ghi lịch sử
 * nếu trạng thái thực sự thay đổi. Mọi lỗi đều rollback đồng thời.
 */
class AuditOrderStatusTransition
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless($request->user() && (bool) $request->user()->is_admin, 403);

        $validated = $request->validate([
            'status' => ['required', 'string', Rule::in(HexagonOrderStatus::values())],
            'status_note' => ['nullable', 'string', 'max:500'],
        ], [
            'status.required' => 'Vui lòng chọn trạng thái đơn hàng.',
            'status.in' => 'Trạng thái đơn hàng không hợp lệ.',
            'status_note.max' => 'Ghi chú không được vượt quá 500 ký tự.',
        ]);

        $id = $request->route('id');
        abort_unless(is_scalar($id) && preg_match('/^[1-9][0-9]*$/D', (string) $id), 404);

        return DB::transaction(function () use ($request, $next, $validated, $id): Response {
            // MySQL/InnoDB lock tránh hai Admin đổi trạng thái đồng thời.
            $order = DB::table('orders')
                ->where('id', $id)
                ->lockForUpdate()
                ->first(['id', 'status']);

            abort_if($order === null, 404);

            $current = (string) $order->status;
            $target = (string) $validated['status'];

            if (!HexagonOrderStatus::canTransition($current, $target)) {
                return redirect()->back()->withErrors([
                    'status' => 'Không thể chuyển đơn từ "'
                        . HexagonOrderStatus::label($current)
                        . '" sang "' . HexagonOrderStatus::label($target) . '".',
                ])->withInput();
            }

            // Giữ nguyên nghiệp vụ/kiểm tra có sẵn trong AdminOrderController.
            $response = $next($request);
            if (!($response instanceof Response)) {
                throw new \RuntimeException('Bộ xử lý trạng thái phải trả về HTTP Response.');
            }

            $savedStatus = (string) DB::table('orders')->where('id', $id)->value('status');
            if ($savedStatus === $target && $savedStatus !== $current) {
                DB::table('order_status_histories')->insert([
                    'order_id' => (int) $order->id,
                    'changed_by' => (int) $request->user()->id,
                    'from_status' => $current,
                    'to_status' => $savedStatus,
                    'note' => trim((string) ($validated['status_note'] ?? '')) ?: null,
                    'created_at' => now(),
                ]);
            }

            return $response;
        }, 3);
    }
}
