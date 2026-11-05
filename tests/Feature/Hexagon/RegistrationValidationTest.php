<?php

namespace Tests\Feature\Hexagon;

use Illuminate\Support\Facades\DB;
use Tests\Support\HexagonIsolatedTestCase;

class RegistrationValidationTest extends HexagonIsolatedTestCase
{
    private function data(array $extra = []): array
    {
        return array_replace([
            'name' => 'Nguyễn Văn An',
            'email' => 'anhexagon@gmail.com',
            'password' => 'Ab1!cdef', // exactly 8; upper, lower, number, special
            'password_confirmation' => 'Ab1!cdef',
            'ui_language' => 'vi',
        ], $extra);
    }

    public function test_name_rejects_numbers_and_accepts_vietnamese_letters(): void
    {
        $this->post(route('customer.register.store'), $this->data(['name' => 'Nguyễn 123']))
            ->assertSessionHasErrors('name');

        $this->assertSame(0, DB::table('users')->count());
    }

    public function test_only_valid_gmail_email_is_accepted(): void
    {
        $this->post(route('customer.register.store'), $this->data(['email' => 'an@yahoo.com']))
            ->assertSessionHasErrors('email');

        $this->post(route('customer.register.store'), $this->data(['email' => 'an@@gmail.com']))
            ->assertSessionHasErrors('email');
    }

    public function test_password_requires_eight_characters_and_complexity(): void
    {
        $this->post(route('customer.register.store'), $this->data(['password' => 'abcdefgh', 'password_confirmation' => 'abcdefgh']))
            ->assertSessionHasErrors('password');

        $this->post(route('customer.register.store'), $this->data(['password' => 'Ab1!cdefgh', 'password_confirmation' => 'Ab1!cdefgh']))
            ->assertSessionHasErrors('password');
    }

    public function test_confirmation_error_appears_on_confirmation_field(): void
    {
        $this->post(route('customer.register.store'), $this->data(['password_confirmation' => 'Ab1!xxxx']))
            ->assertSessionHasErrors('password_confirmation');
    }

    public function test_success_creates_account_and_shows_success_message(): void
    {
        $this->post(route('customer.register.store'), $this->data())
            ->assertRedirect(route('customer.register'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'name' => 'Nguyễn Văn An',
            'email' => 'anhexagon@gmail.com',
            'is_admin' => 0,
        ]);
        $this->get(route('customer.register'))
            ->assertOk()
            ->assertSee('Tới trang đăng nhập ngay');
    }
}
