<?php

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\Support\HexagonIsolatedTestCase;

class GoDaddyDomainCheckLiveTest extends HexagonIsolatedTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config()->set('hexagon_godaddy.availability_source', 'godaddy');
        config()->set('hexagon_godaddy.pat', 'test-token-not-real');
        config()->set('hexagon_godaddy.ca_bundle', '');
        DB::table('domain_prices')->insert([
            'extension' => '.com', 'registration_price' => 260000,
            'renewal_price' => 350000, 'is_active' => 1,
            'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function test_unlisted_domain_can_be_checked_through_godaddy(): void
    {
        Http::fake(['https://api.godaddy.com/v3/domains/check-availability*' => Http::response([
            'domain' => 'newbrandhexagon.com', 'available' => true,
            'definitive' => false, 'prices' => [],
        ], 200)]);
        $this->getJson('/api/v1/domains/check?domain=newbrandhexagon.com')
            ->assertOk()->assertJsonPath('data.available', true)
            ->assertJsonPath('data.is_mock', false)
            ->assertJsonPath('data.source', 'GoDaddy')
            ->assertJsonPath('data.registration_price', 260000);
        Http::assertSent(fn ($request) => str_contains($request->url(), '/v3/domains/check-availability')
            && str_contains($request->url(), 'domain=newbrandhexagon.com'));
    }

    public function test_provider_unknown_response_does_not_claim_domain_is_available(): void
    {
        Http::fake(['https://api.godaddy.com/v3/domains/check-availability*' => Http::response([
            'error' => ['message' => 'cannot determine availability'],
        ], 200)]);
        $this->getJson('/api/v1/domains/check?domain=unknownhexagon.com')
            ->assertStatus(502)->assertJsonPath('success', false);
    }

    public function test_mock_mode_still_works_without_live_credential(): void
    {
        config()->set('hexagon_godaddy.availability_source', 'mock');
        DB::table('mock_domain_statuses')->insert([
            'domain' => 'mocktesthexagon.com', 'is_available' => true,
            'created_at' => now(), 'updated_at' => now(),
        ]);
        $this->getJson('/api/v1/domains/check?domain=mocktesthexagon.com')
            ->assertOk()->assertJsonPath('data.is_mock', true);
    }

    public function test_cart_and_checkout_verify_live_availability_without_mock_row(): void
    {
        Http::fake(['https://api.godaddy.com/v3/domains/check-availability*' => Http::response([
            'domain' => 'newbrandhexagon.com', 'available' => true, 'definitive' => false,
        ], 200)]);
        $this->postJson(route('cart.store'), ['domain' => 'newbrandhexagon.com'])
            ->assertOk()->assertJsonPath('success', true);
        $this->actingAs(User::factory()->create(['is_admin' => false]));
        $this->post(route('checkout.store'), [
            'customer_name' => 'Khach thu nghiem',
            'customer_email' => 'newcustomer@example.com',
            'customer_phone' => '0900000000',
        ])->assertRedirect();
        $this->assertDatabaseHas('order_items', ['domain_name' => 'newbrandhexagon.com']);
        $this->assertDatabaseHas('orders', ['is_mock' => 1]);
    }

    public function test_provider_outage_does_not_add_unverified_domain_to_cart(): void
    {
        Http::fake(['https://api.godaddy.com/v3/domains/check-availability*' => Http::response([], 503)]);
        $this->postJson(route('cart.store'), ['domain' => 'newbrandhexagon.com'])
            ->assertStatus(502)->assertJsonPath('success', false);
        $this->assertDatabaseCount('orders', 0);
    }
}
