<?php

namespace Tests\Feature\Hexagon;

use App\Models\User;
use Tests\Support\HexagonIsolatedTestCase;

class BuyerJourneyUiV6Test extends HexagonIsolatedTestCase
{
    private function cart(): array
    {
        return [
            'thuonghieu.com' => [
                'domain' => 'thuonghieu.com',
                'extension' => '.com',
                'registration_price' => 250000,
            ],
        ];
    }

    public function test_homepage_keeps_original_search_layout_after_v6_1(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('id="domainForm"', false)
            ->assertSee('class="hx-search-surface"', false)
            ->assertSee('id="searchButton"', false)
            ->assertSee('class="hx-hero-art"', false)
            ->assertSee('NỀN TẢNG DỊCH VỤ SỐ HEXAGON')
            ->assertSee('id="pricing"', false)
            ->assertDontSee('hx-extension-shortcuts')
            ->assertDontSee('id="how-it-works"', false);
    }

    public function test_empty_cart_has_clear_link_back_to_domain_search(): void
    {
        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Chưa có tên miền trong giỏ')
            ->assertSee('/#domain-search');
    }

    public function test_guest_sees_cart_summary_and_sign_in_requirement(): void
    {
        $this->withSession(['hexagon_cart' => $this->cart()])
            ->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Tên miền đã chọn')
            ->assertSee('250.000đ')
            ->assertSee('Đăng nhập để đặt hàng')
            ->assertSee('hx-buy-progress');
    }

    public function test_authenticated_customer_sees_contact_form_and_order_summary(): void
    {
        $user = User::factory()->create(['is_admin' => false, 'email_verified_at' => now()]);

        $this->actingAs($user)
            ->withSession(['hexagon_cart' => $this->cart()])
            ->get(route('checkout.create'))
            ->assertOk()
            ->assertSee('Thông tin liên hệ')
            ->assertSee('Tóm tắt đơn hàng')
            ->assertSee('Gửi yêu cầu đặt hàng')
            ->assertSee('name="customer_name"', false);
    }
}
