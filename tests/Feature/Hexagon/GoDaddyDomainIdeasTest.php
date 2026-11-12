<?php

namespace Tests\Feature\Hexagon;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class GoDaddyDomainIdeasTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config()->set('hexagon_godaddy.pat', 'FAKE_TEST_PAT');
        config()->set('hexagon_godaddy.cache_seconds', 60);
        $this->withServerVariables([
            'REMOTE_ADDR' => '127.0.0.18',
            'HTTP_USER_AGENT' => 'HexagonGoDaddyIdeasTestAgent',
        ]);
        $key = 'hx-domain-lookup-v1:' . hash('sha256', '127.0.0.18|HexagonGoDaddyIdeasTestAgent');
        RateLimiter::clear($key);
        foreach (['he', 'hex', 'test'] as $query) {
            Cache::forget('hx:godaddy:ideas:v1:' . hash('sha256', $query . '|com,net'));
        }
    }

    public function test_returns_real_provider_ideas_without_claiming_final_availability(): void
    {
        Http::fake(['api.godaddy.com/*' => Http::response([
            'items' => [
                ['domain' => 'helio.com'],
                ['domain' => 'heplus.net'],
                ['domain' => 'he.vn'], // unsupported extension, filtered
                ['domain' => 'helio.com'], // duplicate, filtered
            ],
        ], 200)]);

        $response = $this->getJson('/api/v1/domains/ideas?q=he');
        $response->assertOk()
            ->assertJsonPath('source', 'GoDaddy')
            ->assertJsonPath('availability', 'provider_suggested_not_final')
            ->assertJsonCount(2, 'items')
            ->assertJsonPath('items.0.domain', 'helio.com');
        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => str_contains($request->url(), '/v3/domains/suggestions')
            && str_contains($request->url(), 'query=he'));
    }

    public function test_requests_with_less_than_two_characters_are_rejected(): void
    {
        Http::fake();
        $this->getJson('/api/v1/domains/ideas?q=h')->assertStatus(422);
        Http::assertNothingSent();
    }

    public function test_repeated_requests_are_limited_to_one_every_three_seconds(): void
    {
        Http::fake(['api.godaddy.com/*' => Http::response(['items' => [['domain' => 'helio.com']]])]);
        $this->getJson('/api/v1/domains/ideas?q=he')->assertOk();
        $this->getJson('/api/v1/domains/ideas?q=hex')->assertStatus(429);
        Http::assertSentCount(1);
    }

    public function test_missing_token_does_not_contact_provider_or_leak_credentials(): void
    {
        config()->set('hexagon_godaddy.pat', '');
        Http::fake();
        $this->getJson('/api/v1/domains/ideas?q=he')->assertStatus(503)
            ->assertDontSee('FAKE_TEST_PAT');
        Http::assertNothingSent();
    }

    public function test_provider_failure_returns_service_error_instead_of_fake_results(): void
    {
        Http::fake(['api.godaddy.com/*' => Http::response(['error' => 'upstream'], 502)]);
        $this->getJson('/api/v1/domains/ideas?q=he')->assertStatus(502)
            ->assertJsonPath('success', false)
            ->assertJsonPath('items', []);
    }
}
