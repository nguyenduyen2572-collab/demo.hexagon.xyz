<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

final class HexagonGoDaddyDomainSuggestions
{
    /**
     * GoDaddy v3 returns domain suggestions believed to be available.
     * They are NOT a confirmed live-registration quote and must never be sold
     * as if purchase has completed. Leave checkout's existing behavior alone.
     *
     * @return list<array{domain:string,source:string,status:string}>
     */
    public function suggest(string $query): array
    {
        $token = trim((string) config('hexagon_godaddy.pat', ''));
        if ($token === '') {
            throw new RuntimeException('Chưa cấu hình GoDaddy PAT trong .env.', 503);
        }

        $tlds = (array) config('hexagon_godaddy.tlds', ['com', 'net']);
        $tlds = array_values(array_intersect(['com', 'net'], $tlds));
        if ($tlds === []) {
            throw new RuntimeException('Chưa cấu hình đuôi tên miền hợp lệ.', 503);
        }

        $seconds = max(10, min(180, (int) config('hexagon_godaddy.cache_seconds', 60)));
        $cacheKey = 'hx:godaddy:ideas:v2:' . hash('sha256', mb_strtolower($query, 'UTF-8') . '|' . implode(',', $tlds));

        return Cache::remember($cacheKey, $seconds, function () use ($token, $query, $tlds): array {
            try {
                $request = Http::withToken($token)
                    ->acceptJson()
                    ->connectTimeout(3)
                    ->timeout(9);

                // Optional certificate bundle for WAMP/Apache PHP. TLS verification stays ON.
                $bundle = trim((string) config('hexagon_godaddy.ca_bundle', ''));
                if ($bundle !== '') {
                    if (!is_file($bundle) || !is_readable($bundle)) {
                        Log::warning('HEXAGON GoDaddy CA bundle unavailable', [
                            'category' => 'ca_file_not_readable',
                        ]);
                        throw new RuntimeException('Không thể đọc tệp chứng chỉ CA đã cấu hình.', 503);
                    }
                    $request = $request->withOptions(['verify' => $bundle]);
                }

                $response = $request->get('https://api.godaddy.com/v3/domains/suggestions', [
                    'query' => $query,
                    'tlds' => implode(',', $tlds),
                    'pageSize' => 8,
                ]);
            } catch (RuntimeException $e) {
                throw $e;
            } catch (\Throwable $e) {
                // Do not log the PAT, authorization headers, request URL or full exception.
                $curlCode = null;
                if (preg_match('/cURL error ([0-9]+)/i', $e->getMessage(), $matches)) {
                    $curlCode = (int) $matches[1];
                }
                Log::warning('HEXAGON GoDaddy connection failed', [
                    'exception_type' => get_class($e),
                    'curl_error_code' => $curlCode,
                ]);
                throw new RuntimeException('Không thể kết nối dịch vụ tra cứu lúc này.', 503, $e);
            }

            if (!$response->successful()) {
                Log::warning('HEXAGON GoDaddy provider response error', [
                    'http_status' => $response->status(),
                ]);
                if ($response->status() === 429) {
                    throw new RuntimeException('Dịch vụ tra cứu đang bận. Vui lòng thử lại sau.', 503);
                }
                throw new RuntimeException('Không thể nhận gợi ý tên miền từ nhà cung cấp.', 502);
            }

            $items = $response->json('items');
            if (!is_array($items)) {
                throw new RuntimeException('Dịch vụ tra cứu trả về dữ liệu không hợp lệ.', 502);
            }

            $found = [];
            foreach ($items as $item) {
                if (!is_array($item) || !is_string($item['domain'] ?? null)) {
                    continue;
                }
                $domain = strtolower(trim($item['domain']));
                if (!preg_match('/^(?=.{1,253}$)[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.(?:com|net)$/D', $domain)) {
                    continue;
                }
                $ext = substr(strrchr($domain, '.'), 1);
                if (!in_array($ext, $tlds, true)) {
                    continue;
                }
                $found[$domain] = [
                    'domain' => $domain,
                    'source' => 'GoDaddy',
                    'status' => 'suggested_available',
                ];
                if (count($found) === 8) {
                    break;
                }
            }

            return array_values($found);
        });
    }
}
