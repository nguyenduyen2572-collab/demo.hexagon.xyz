<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Shared lookup for Check -> Cart -> Checkout.
 * Live GoDaddy is an indicative lookup only; this never registers a domain.
 */
final class HexagonDomainAvailability
{
    /** @return array{available:bool,is_mock:bool,source:string,definitive:bool} */
    public function check(string $domain): array
    {
        $mode = strtolower(trim((string) config('hexagon_godaddy.availability_source', 'auto')));
        $token = trim((string) config('hexagon_godaddy.pat', ''));
        if (!in_array($mode, ['auto', 'godaddy', 'mock'], true)) {
            throw new RuntimeException('Cấu hình nguồn tra cứu tên miền không hợp lệ.', 503);
        }
        $live = $mode === 'godaddy' || ($mode === 'auto' && $token !== '');
        if (!$live) {
            $row = DB::table('mock_domain_statuses')->where('domain', $domain)->first();
            if (!$row) {
                throw new RuntimeException('Chưa có dữ liệu mẫu cho tên miền này. Cần kết nối GoDaddy để tra cứu tên miền bất kỳ.', 404);
            }
            return ['available' => (bool) $row->is_available, 'is_mock' => true, 'source' => 'mock', 'definitive' => false];
        }
        if ($token === '') {
            throw new RuntimeException('Chưa cấu hình GoDaddy PAT cho chức năng kiểm tra tên miền.', 503);
        }

        try {
            $request = Http::withToken($token)->acceptJson()->connectTimeout(3)->timeout(10);
            $bundle = trim((string) config('hexagon_godaddy.ca_bundle', ''));
            if ($bundle !== '') {
                if (!is_file($bundle) || !is_readable($bundle)) {
                    Log::warning('HEXAGON availability CA bundle not readable');
                    throw new RuntimeException('Không đọc được chứng chỉ CA đã cấu hình.', 503);
                }
                $request = $request->withOptions(['verify' => $bundle]);
            }
            $response = $request->get('https://api.godaddy.com/v3/domains/check-availability', [
                'domain' => $domain,
                'optimizeFor' => 'SPEED',
            ]);
        } catch (RuntimeException $e) {
            throw $e;
        } catch (\Throwable $e) {
            $curlCode = null;
            if (preg_match('/cURL error ([0-9]+)/i', $e->getMessage(), $match)) {
                $curlCode = (int) $match[1];
            }
            Log::warning('HEXAGON availability connection failed', [
                'exception_type' => get_class($e),
                'curl_error_code' => $curlCode,
            ]);
            throw new RuntimeException('Không kết nối được nhà cung cấp tên miền. Vui lòng thử lại sau.', 503, $e);
        }

        if (!$response->successful()) {
            Log::warning('HEXAGON availability provider returned error', ['status' => $response->status()]);
            $message = match ($response->status()) {
                400, 422 => 'Tên miền không hợp lệ hoặc chưa được nhà cung cấp hỗ trợ tra cứu.',
                401, 403 => 'GoDaddy PAT chưa hợp lệ hoặc thiếu quyền domains.domain:read.',
                429 => 'Dịch vụ tra cứu đang giới hạn yêu cầu. Vui lòng thử lại sau.',
                default => 'Chưa nhận được kết quả tra cứu từ nhà cung cấp. Vui lòng thử lại sau.',
            };
            $httpStatus = match ($response->status()) {
                400, 422 => 422,
                429 => 503,
                default => 502,
            };
            throw new RuntimeException($message, $httpStatus);
        }
        $data = $response->json();
        // Some uncheckable domains return HTTP 200 with an error object.
        // Never interpret such a response as 'available' or 'registered'.
        if (!is_array($data) || !array_key_exists('available', $data) || !is_bool($data['available'])) {
            Log::warning('HEXAGON availability response lacks boolean available');
            throw new RuntimeException('Nhà cung cấp chưa xác định được trạng thái tên miền này.', 502);
        }
        return [
            'available' => $data['available'],
            'is_mock' => false,
            'source' => 'GoDaddy',
            'definitive' => (bool) ($data['definitive'] ?? false),
        ];
    }
}
