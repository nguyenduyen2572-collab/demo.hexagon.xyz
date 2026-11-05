<?php

namespace Tests\Feature\Hexagon;

use Tests\TestCase;

class AboutPageTest extends TestCase
{
    public function test_guest_can_read_about_page(): void
    {
        $this->get(route('hexagon.about'))
            ->assertOk()
            ->assertSee('Tư duy kết nối.')
            ->assertSee('Giới thiệu HEXAGON');
    }

    public function test_about_page_contains_real_navigation(): void
    {
        $this->get(route('hexagon.about'))
            ->assertOk()
            ->assertSee(url('/').'#domain-search', false)
            ->assertSee(route('cart.index'), false);
    }
}
