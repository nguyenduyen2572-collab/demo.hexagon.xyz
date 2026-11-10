<?php

namespace Tests\Feature\Hexagon;

use App\Http\Middleware\HexagonDomainSearchCooldown;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Tests\Support\HexagonIsolatedTestCase;

class DomainSuggestCooldownTest extends HexagonIsolatedTestCase
{
    public function test_domain_lookup_allows_one_request_per_three_seconds(): void
    {
        Route::get('/__hx-domain-cooldown-fixture', fn () => response()->json(['success' => true]))
            ->middleware(HexagonDomainSearchCooldown::class);

        $ip = '198.51.100.81';
        $ua = 'HEXAGON-Test-Cooldown-1';
        $key = 'hx-domain-lookup-v1:' . hash('sha256', $ip . '|' . $ua);
        RateLimiter::clear($key);

        try {
            $this->withServerVariables(['REMOTE_ADDR' => $ip, 'HTTP_USER_AGENT' => $ua]);
            $this->getJson('/__hx-domain-cooldown-fixture')->assertOk()->assertJsonPath('success', true);
            $this->getJson('/__hx-domain-cooldown-fixture')
                ->assertStatus(429)->assertJsonPath('success', false)->assertHeader('Retry-After');
        } finally {
            RateLimiter::clear($key);
        }
    }

    public function test_home_loads_suggestion_assets_without_changing_domain_check_api(): void
    {
        $blade = file_get_contents(resource_path('views/home.blade.php'));
        $this->assertStringContainsString('hexagon-domain-suggest-v1.js', $blade);
        $this->assertStringContainsString('hexagon-domain-suggest-v1.css', $blade);
        $this->assertStringContainsString('id="domainInput"', $blade);
    }
}
