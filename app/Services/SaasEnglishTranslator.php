<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\PendingRequest;
use RuntimeException;

/**
 * Dịch nội dung công khai từ tiếng Việt sang tiếng Anh.
 * MyMemory: dịch từng đoạn <= 450 byte (giới hạn API 500 byte).
 * Google Cloud Translation Basic: dùng API key của chủ hệ thống nếu cấu hình.
 */
final class SaasEnglishTranslator
{
    public function translate(?string $text): ?string
    {
        $text = trim((string) $text);
        if ($text === '') {
            return null;
        }

        $provider = (string) config('services.hexagon_saas_translation.provider', 'mymemory');
        if (! in_array($provider, ['mymemory', 'google'], true)) {
            throw new RuntimeException('Chưa cấu hình dịch tự động SaaS hợp lệ.');
        }

        return Cache::remember('hexagon:saas:translation:'.hash('sha256', $provider.'|vi|en|'.$text), now()->addDays(30), function () use ($provider, $text) {
            if ($provider === 'google') {
                return $this->googleTranslate($text);
            }

            // Giữ dấu xuống dòng và dịch từng đoạn nhỏ, tránh vượt giới hạn API.
            $lines = preg_split('/\R/u', $text);
            if ($lines === false) {
                throw new RuntimeException('Không đọc được văn bản cần dịch.');
            }
            $translated = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if ($line === '') {
                    $translated[] = '';
                    continue;
                }
                $pieces = [];
                foreach ($this->segments($line, 440) as $segment) {
                    $response = $this->saasHttpsRequest(10)->get('https://api.mymemory.translated.net/get', [
                        'q' => $segment,
                        'langpair' => 'vi|en',
                    ]);
                    if (! $response->successful() || (int) $response->json('responseStatus') !== 200) {
                        throw new RuntimeException('Dịch vụ MyMemory đang giới hạn lượt dịch hoặc không phản hồi.');
                    }
                    $value = html_entity_decode((string) $response->json('responseData.translatedText', ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    if (trim($value) === '' || preg_match('/^\s*(?:MYMEMORY WARNING|YOU USED ALL AVAILABLE FREE TRANSLATIONS)/i', $value)) {
                        throw new RuntimeException('Không nhận được bản dịch hợp lệ. Có thể đã hết hạn mức dịch miễn phí.');
                    }
                    $pieces[] = trim($value);
                }
                $translated[] = implode(' ', $pieces);
            }
            return implode("\n", $translated);
        });
    }

    /** Reuse existing CA certificate (same as GoDaddy); TLS verification remains enabled. */
    private function saasHttpsRequest(int $seconds): PendingRequest
    {
        $request = Http::acceptJson()->timeout($seconds);
        // Optional SaaS-specific config takes priority if added in the future.
        $caBundle = trim((string) (config('services.hexagon_saas_translation.ca_bundle') ?: config('hexagon_godaddy.ca_bundle', '')));
        if ($caBundle === '') {
            return $request;
        }
        if (!is_file($caBundle) || !is_readable($caBundle)) {
            throw new RuntimeException('Không đọc được chứng chỉ CA đã cấu hình. Vui lòng kiểm tra GODADDY_CA_BUNDLE trong .env.');
        }
        return $request->withOptions(['verify' => $caBundle]);
    }
    /** @return list<string> */
    private function segments(string $line, int $maxBytes): array
    {
        // Dịch câu trọn vẹn khi có thể; câu dài được tách theo từ, không cắt đôi UTF-8.
        $sentences = preg_split('/(?<=[.!?;:])\s+/u', $line, -1, PREG_SPLIT_NO_EMPTY) ?: [$line];
        $result = [];
        $current = '';
        foreach ($sentences as $sentence) {
            $words = preg_split('/\s+/u', trim($sentence), -1, PREG_SPLIT_NO_EMPTY) ?: [];
            foreach ($words as $word) {
                if (strlen($word) > $maxBytes) {
                    throw new RuntimeException('Có một từ quá dài để dịch. Vui lòng chia nhỏ nội dung.');
                }
                $next = $current === '' ? $word : $current.' '.$word;
                if (strlen($next) > $maxBytes) {
                    if ($current !== '') $result[] = $current;
                    $current = $word;
                } else {
                    $current = $next;
                }
            }
        }
        if ($current !== '') $result[] = $current;
        return $result;
    }

    private function googleTranslate(string $text): string
    {
        $key = (string) config('services.hexagon_saas_translation.google_api_key', '');
        if ($key === '') {
            throw new RuntimeException('Chưa cấu hình GOOGLE_TRANSLATE_API_KEY trong .env.');
        }
        $response = $this->saasHttpsRequest(15)
            ->post('https://translation.googleapis.com/language/translate/v2?key='.rawurlencode($key), [
                'q' => $text,
                'source' => 'vi',
                'target' => 'en',
                'format' => 'text',
            ]);
        if (! $response->successful()) {
            throw new RuntimeException('Google Cloud Translation hiện không phản hồi. Kiểm tra API key và hạn mức.');
        }
        $value = html_entity_decode((string) $response->json('data.translations.0.translatedText', ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (trim($value) === '') {
            throw new RuntimeException('Google Cloud Translation trả về nội dung trống.');
        }
        return trim($value);
    }
}
