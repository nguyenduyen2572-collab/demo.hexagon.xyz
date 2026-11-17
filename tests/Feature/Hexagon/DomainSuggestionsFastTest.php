<?php

namespace Tests\Feature\Hexagon;

use App\Http\Middleware\HexagonDomainSearchCooldown;
use App\Services\HexagonGoDaddyDomainSuggestions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class DomainSuggestionsFastTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config()->set('hexagon_godaddy.pat', 'FAKE_TEST_PAT');
        config()->set('hexagon_godaddy.ca_bundle', '');
        config()->set('hexagon_godaddy.cache_seconds', 300);
        $this->withServerVariables([
            'REMOTE_ADDR' => '198.51.100.92',
            'HTTP_USER_AGENT' => 'HexagonSuggestionsFastTest',
        ]);
        $fp = hash('sha256', '198.51.100.92|HexagonSuggestionsFastTest');
        RateLimiter::clear('hx-domain-ideas-v1:' . $fp);
        RateLimiter::clear('hx-domain-lookup-v1:' . $fp);
        Cache::forget('hx:godaddy:ideas:v2:' . hash('sha256', 'quick|' . 'com,net'));
    }

    public function test_suggestions_no_longer_consume_the_three_second_check_cooldown(): void
    {
        Http::fake(['api.godaddy.com/*' => Http::response(['items' => [
            ['domain' => 'quick.com'],
        ]], 200)]);

        $this->getJson('/api/v1/domains/ideas?q=quick')
            ->assertOk()->assertJsonPath('items.0.domain', 'quick.com');

        Route::get('/__hx-fast-check-fixture', fn () => response()->json(['success' => true]))
            ->middleware(HexagonDomainSearchCooldown::class);

        $this->getJson('/__hx-fast-check-fixture')
            ->assertOk()->assertJsonPath('success', true);
        $this->getJson('/__hx-fast-check-fixture')->assertStatus(429);
        $this->getJson('/api/v1/domains/ideas?q=quick')->assertStatus(429);
        Http::assertSentCount(1);
    }

    public function test_provider_ideas_are_cached_for_repeated_queries(): void
    {
        Http::fake(['api.godaddy.com/*' => Http::response(['items' => [
            ['domain' => 'quick.com'],
        ]], 200)]);

        $service = app(HexagonGoDaddyDomainSuggestions::class);
        $this->assertCount(1, $service->suggest('quick'));
        $this->assertCount(1, $service->suggest('quick'));
        Http::assertSentCount(1);
    }
}
