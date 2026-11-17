<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

final class AdminDomainPriceController extends Controller
{
    private const PER_PAGE = 12;

    private const MONTHS = [1, 3, 6, 12, 24];

    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:30'],
            'status' => ['nullable', Rule::in(['all', 'active', 'inactive'])],
            'sort' => ['nullable', Rule::in(['extension', 'price_asc', 'price_desc', 'updated'])],
        ]);
        $filters['q'] = trim((string) ($filters['q'] ?? ''));
        $filters['status'] = $filters['status'] ?? 'all';
        $filters['sort'] = $filters['sort'] ?? 'extension';

        $query = DB::table('domain_prices');
        if ($filters['q'] !== '') {
            // Escape LIKE wildcards; the value stays parameter-bound by the query builder.
            $query->where('extension', 'like', '%' . addcslashes($filters['q'], '%_\\') . '%');
        }
        if ($filters['status'] !== 'all') {
            $query->where('is_active', $filters['status'] === 'active');
        }

        switch ($filters['sort']) {
            case 'price_asc':
                $query->orderBy('registration_price')->orderBy('extension');
                break;
            case 'price_desc':
                $query->orderByDesc('registration_price')->orderBy('extension');
                break;
            case 'updated':
                $query->orderByDesc('updated_at')->orderBy('id');
                break;
            default:
                $query->orderBy('extension');
                break;
        }

        $prices = $query->paginate(self::PER_PAGE)->withQueryString();

        return view('admin.domain-prices', compact('prices', 'filters'));
    }

    public function store(Request $request)
    {
        $request->merge(['extension' => strtolower(trim((string) $request->input('extension', '')))]);
        $validated = $request->validate([
            'extension' => [
                'required', 'string', 'max:30',
                'regex:/^\.[a-z0-9]+(?:-[a-z0-9]+)*(?:\.[a-z0-9]+(?:-[a-z0-9]+)*)*$/',
                Rule::unique('domain_prices', 'extension'),
            ],
            'registration_price' => ['required', 'integer', 'min:0', 'max:1000000000'],
            'renewal_price' => ['required', 'integer', 'min:0', 'max:1000000000'],
            'renewal_period_months' => ['required', 'integer', Rule::in(self::MONTHS)],
        ]);

        DB::transaction(function () use ($validated): void {
            $id = DB::table('domain_prices')->insertGetId([
                'extension' => $validated['extension'],
                'registration_price' => (int) $validated['registration_price'],
                'renewal_price' => (int) $validated['renewal_price'],
                'renewal_period_months' => (int) $validated['renewal_period_months'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $this->recordHistory($id, 'created', null, DB::table('domain_prices')->find($id));
        });

        return redirect()->route('admin.domain-prices.index')
            ->with('success', 'Đã thêm đuôi tên miền thành công.');
    }

    public function edit(int $id)
    {
        $price = DB::table('domain_prices')->where('id', $id)->first();
        abort_if($price === null, 404);

        $history = DB::table('domain_price_histories')
            ->where('domain_price_id', $id)
            ->orderByDesc('id')
            ->limit(12)
            ->get();

        return view('admin.domain-price-edit', compact('price', 'history'));
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'registration_price' => ['required', 'integer', 'min:0', 'max:1000000000'],
            'renewal_price' => ['required', 'integer', 'min:0', 'max:1000000000'],
            'renewal_period_months' => ['required', 'integer', Rule::in(self::MONTHS)],
        ]);

        $changed = DB::transaction(function () use ($id, $validated): bool {
            $before = DB::table('domain_prices')->where('id', $id)->lockForUpdate()->first();
            abort_if($before === null, 404);

            $fields = [
                'registration_price' => (int) $validated['registration_price'],
                'renewal_price' => (int) $validated['renewal_price'],
                'renewal_period_months' => (int) $validated['renewal_period_months'],
            ];
            foreach ($fields as $column => $value) {
                if ((int) $before->{$column} !== $value) {
                    DB::table('domain_prices')->where('id', $id)->update($fields + ['updated_at' => now()]);
                    $this->recordHistory($id, 'updated', $before, DB::table('domain_prices')->find($id));
                    return true;
                }
            }
            return false;
        });

        return redirect()->route('admin.domain-prices.index')
            ->with('success', $changed ? 'Đã lưu bảng giá và ghi lịch sử thay đổi.' : 'Không có thông tin nào thay đổi.');
    }

    public function toggle(int $id)
    {
        $enabled = DB::transaction(function () use ($id): bool {
            $before = DB::table('domain_prices')->where('id', $id)->lockForUpdate()->first();
            abort_if($before === null, 404);

            $enabled = ! (bool) $before->is_active;
            DB::table('domain_prices')->where('id', $id)->update([
                'is_active' => $enabled,
                'updated_at' => now(),
            ]);
            $this->recordHistory($id, 'toggled', $before, DB::table('domain_prices')->find($id));
            return $enabled;
        });

        return back()->with('success', $enabled ? 'Đã bật kinh doanh.' : 'Đã tạm ngừng kinh doanh.');
    }

    private function recordHistory(int $id, string $action, ?object $before, object $after): void
    {
        $admin = Auth::guard('admin')->user();
        DB::table('domain_price_histories')->insert([
            'domain_price_id' => $id,
            'extension' => $after->extension,
            'action' => $action,
            'admin_user_id' => $admin?->id,
            'admin_name' => $admin?->name ?? 'Quản trị viên',
            'old_registration_price' => $before?->registration_price,
            'new_registration_price' => $after->registration_price,
            'old_renewal_price' => $before?->renewal_price,
            'new_renewal_price' => $after->renewal_price,
            'old_renewal_period_months' => $before?->renewal_period_months,
            'new_renewal_period_months' => $after->renewal_period_months,
            'old_is_active' => $before?->is_active,
            'new_is_active' => $after->is_active,
            'created_at' => now(),
        ]);
    }
}
