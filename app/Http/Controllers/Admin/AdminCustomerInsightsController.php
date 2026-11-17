<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Read-only insights for registered customers. Only routes with auth + EnsureAdmin
 * may point to this controller. Guest orders are deliberately not matched by email.
 */
// HEXAGON-CUSTOMERS-FILTER-CLEAN-V1: verification remains in customer auth, not admin insights.
// HEXAGON-CUSTOMERS-COMPACT-CONTROLLER-V2: no order-count filter or money summary.
final class AdminCustomerInsightsController extends Controller
{
    private const PAGE_SIZE = 15;

    private const ORDER_STATUSES = [
        'pending_demo' => 'Chờ xử lý',
        'processing_demo' => 'Đang xử lý',
        'completed_demo' => 'Hoàn tất',
        'cancelled_demo' => 'Đã hủy',
    ];

    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'sort' => ['nullable', Rule::in(['latest', 'oldest', 'orders'])],
        ]);

        $filters['q'] = trim((string) ($filters['q'] ?? ''));
        $filters['sort'] = $filters['sort'] ?? 'latest';

        $customers = DB::table('users as u')
            ->where('u.is_admin', false)
            ->select(['u.id', 'u.name', 'u.email', 'u.created_at'])
            ->selectSub(
                DB::table('orders as o')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('o.user_id', 'u.id'),
                'orders_count'
            )
            ->selectSub(
                DB::table('orders as o')
                    ->selectRaw('MAX(o.created_at)')
                    ->whereColumn('o.user_id', 'u.id'),
                'last_order_at'
            );

        if ($filters['q'] !== '') {
            // Escape SQL LIKE wildcards, then bind the value (never concatenate into SQL).
            $term = '%' . addcslashes($filters['q'], '%_\\') . '%';
            $customers->where(function ($query) use ($term): void {
                $query->where('u.name', 'like', $term)
                    ->orWhere('u.email', 'like', $term);
            });
        }

        switch ($filters['sort']) {
            case 'oldest':
                $customers->orderBy('u.created_at')->orderBy('u.id');
                break;
            case 'orders':
                $customers->orderByDesc('orders_count')->orderByDesc('u.id');
                break;
            default:
                $customers->orderByDesc('u.created_at')->orderByDesc('u.id');
                break;
        }

        $customers = $customers->paginate(self::PAGE_SIZE)->withQueryString();

        return view('admin.customer-insights.index', compact('customers', 'filters'));
    }

    public function show(string $id)
    {
        abort_unless(ctype_digit($id), 404);

        $customer = DB::table('users')
            ->where('id', (int) $id)
            ->where('is_admin', false)
            ->first(['id', 'name', 'email', 'created_at']);

        abort_if($customer === null, 404);

        $ordersBase = DB::table('orders')->where('user_id', $customer->id);
        $summary = [
            'count' => (clone $ordersBase)->count(),
            'needs_action' => (clone $ordersBase)->whereIn('status', ['pending_demo', 'processing_demo'])->count(),
            'completed' => (clone $ordersBase)->where('status', 'completed_demo')->count(),
            'cancelled' => (clone $ordersBase)->where('status', 'cancelled_demo')->count(),
        ];

        $orders = $ordersBase->orderByDesc('created_at')->orderByDesc('id')->paginate(10);
        $statusLabels = self::ORDER_STATUSES;

        return view('admin.customer-insights.show', compact('customer', 'summary', 'orders', 'statusLabels'));
    }
}
