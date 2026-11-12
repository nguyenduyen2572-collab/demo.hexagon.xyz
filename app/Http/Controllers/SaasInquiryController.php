<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class SaasInquiryController extends Controller
{
    public function store(Request $request, string $slug): RedirectResponse
    {
        // Only accept enquiries for public, active services.
        $service = DB::table('saas_services')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first(['id', 'name_vi']);

        abort_if($service === null, 404);

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_email' => ['required', 'email', 'max:190'],
            'customer_phone' => [
                'required', 'string', 'max:30', 'regex:/^[+0-9().\s-]{9,30}$/',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $digits = preg_replace('/\D/', '', (string) $value);
                    if (strlen($digits ?? '') < 9 || strlen($digits ?? '') > 15) {
                        $fail('Số điện thoại phải có từ 9 đến 15 chữ số.');
                    }
                },
            ],
            'message' => ['nullable', 'string', 'max:2000'],
            'consent' => ['accepted'],
            'website' => ['nullable', 'string', 'max:0'], // honeypot
        ], [
            'customer_phone.regex' => 'Số điện thoại không hợp lệ.',
            'consent.accepted' => 'Vui lòng đồng ý để HEXAGON liên hệ về yêu cầu tư vấn.',
            'website.max' => 'Yêu cầu không hợp lệ.',
        ]);

        $now = now();
        DB::table('saas_inquiries')->insert([
            'saas_service_id' => $service->id,
            'service_name' => $service->name_vi,
            'customer_name' => trim($data['customer_name']),
            'customer_email' => strtolower(trim($data['customer_email'])),
            'customer_phone' => trim($data['customer_phone']),
            'message' => isset($data['message']) ? trim($data['message']) : null,
            'status' => 'new',
            'admin_note' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return redirect()->route('saas.show', ['slug' => $slug])
            ->with('saas_inquiry_success', 'HEXAGON đã tiếp nhận yêu cầu. Bộ phận tư vấn sẽ liên hệ với bạn.');
    }
}
