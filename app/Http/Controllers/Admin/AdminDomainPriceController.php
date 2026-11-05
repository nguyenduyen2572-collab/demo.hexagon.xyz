<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminDomainPriceController extends Controller
{
    // Danh sách bảng giá
    public function index()
    {
        $prices = DB::table('domain_prices')
            ->orderBy('registration_price')
            ->get();

        return view('admin.domain-prices', [
            'prices' => $prices,
        ]);
    }

    // Thêm đuôi tên miền
    public function store(Request $request)
    {
        $request->merge([
            'extension' => strtolower(
                trim((string) $request->input('extension', ''))
            ),
        ]);

        $validated = $request->validate([
            'extension' => [
                'required',
                'string',
                'max:30',
                'regex:/^\.[a-z0-9]+(?:-[a-z0-9]+)*(?:\.[a-z0-9]+(?:-[a-z0-9]+)*)*$/',
                Rule::unique('domain_prices', 'extension'),
            ],
            'registration_price' => [
                'required', 'integer', 'min:0', 'max:1000000000',
            ],
            'renewal_price' => [
                'required', 'integer', 'min:0', 'max:1000000000',
            ],
        ]);

        DB::table('domain_prices')->insert([
            'extension' => $validated['extension'],
            'registration_price' =>
                (int) $validated['registration_price'],
            'renewal_price' =>
                (int) $validated['renewal_price'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()
            ->route('admin.domain-prices.index')
            ->with('success', 'Đã thêm đuôi tên miền thành công!');
    }

    // Giao diện chỉnh sửa
    public function edit(int $id)
    {
        $price = DB::table('domain_prices')
            ->where('id', $id)
            ->first();

        abort_if(!$price, 404);

        return view('admin.domain-price-edit', [
            'price' => $price,
        ]);
    }

    // Cập nhật giá
    public function update(Request $request, int $id)
    {
        $price = DB::table('domain_prices')
            ->where('id', $id)
            ->first();

        abort_if(!$price, 404);

        $validated = $request->validate([
            'registration_price' => [
                'required', 'integer', 'min:0', 'max:1000000000',
            ],
            'renewal_price' => [
                'required', 'integer', 'min:0', 'max:1000000000',
            ],
        ]);

        DB::table('domain_prices')
            ->where('id', $id)
            ->update([
                'registration_price' =>
                    (int) $validated['registration_price'],
                'renewal_price' =>
                    (int) $validated['renewal_price'],
                'updated_at' => now(),
            ]);

        return redirect()
            ->route('admin.domain-prices.index')
            ->with('success', 'Cập nhật bảng giá thành công!');
    }
    
public function toggle(int $id)
{
    $price = DB::table('domain_prices')
        ->where('id', $id)
        ->first();

    abort_if(!$price, 404);

    $newStatus = !(bool) $price->is_active;

    // Kiểm tra trạng thái cũ trước khi cập nhật
    $updated = DB::table('domain_prices')
        ->where('id', $id)
        ->where('is_active', $price->is_active)
        ->update([
            'is_active' => $newStatus,
            'updated_at' => now(),
        ]);

    if ($updated !== 1) {
        return redirect()
            ->route('admin.domain-prices.index')
            ->with('error', 'Dữ liệu đã thay đổi. Hãy thử lại.');
    }

    return redirect()
        ->route('admin.domain-prices.index')
        ->with(
            'success',
            $newStatus
                ? 'Đã bật kinh doanh ' . $price->extension
                : 'Đã tạm ngừng kinh doanh ' . $price->extension
        );
}

}
