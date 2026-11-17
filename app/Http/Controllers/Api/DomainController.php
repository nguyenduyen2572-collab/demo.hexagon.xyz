<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\HexagonDomainAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DomainController extends Controller
{
    public function check(Request $request, HexagonDomainAvailability $availability)
    {
        // 1. Kiểm tra dữ liệu đầu vào
        $validated = $request->validate([
            'domain' => ['required', 'string', 'max:190'],
        ]);

        $domain = strtolower(trim($validated['domain']));

        if (!filter_var(
            $domain,
            FILTER_VALIDATE_DOMAIN,
            FILTER_FLAG_HOSTNAME
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Tên miền không hợp lệ',
            ], 422);
        }

        // 2. Tìm giá theo đuôi tên miền
        $price = DB::table('domain_prices')
            ->orderByRaw('CHAR_LENGTH(extension) DESC')
            ->get()
            ->first(function ($item) use ($domain) {
                return str_ends_with(
                    $domain,
                    $item->extension
                );
            });

        if (!$price) {
            return response()->json([
                'success' => false,
                'message' => 'Đuôi tên miền chưa được hỗ trợ',
            ], 404);
        }
        if (!(bool) $price->is_active) {
    return response()->json([
        'success' => false,
        'message' => 'HEXAGON đang tạm ngừng kinh doanh đuôi tên miền này.',
    ], 422);
}

        // 3. Tra cứu trực tiếp qua GoDaddy nếu đã cấu hình PAT.
        try {
            $status = $availability->check($domain);
        } catch (\RuntimeException $e) {
            $code = in_array($e->getCode(), [404, 422, 502, 503], true) ? $e->getCode() : 503;
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $code, ['Cache-Control' => 'no-store']);
        }

        // 4. Trả kết quả JSON
        return response()->json([
            'success' => true,
            'data' => [
                'domain' => $domain,
                'extension' => $price->extension,
                'available' => $status['available'],
                'registration_price' => $price->registration_price,
                'renewal_price' => $price->renewal_price,
                'renewal_period_months' => (int) ($price->renewal_period_months ?? 12),
                'currency' => 'VND',
                'is_mock' => $status['is_mock'],
                'source' => $status['source'],
                'definitive' => $status['definitive'],
            ],
        ]);
    }
}
