<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200)
            ->assertSee('action="/register"', false)
            ->assertSee('name="_token"', false);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'phone' => '+358 40 000 0000',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSeeText('Welcome back, Test')
            ->assertSeeText('test@example.com');
    }

    public function test_registration_normalizes_a_finnish_phone_number(): void
    {
        $this->post('/register', [
            'name' => 'Aino Korhonen',
            'phone' => '040 123 4567',
            'email' => 'aino@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertSame('+358401234567', User::query()->where('email', 'aino@example.com')->value('phone'));
    }

    public function test_registration_rejects_an_invalid_phone_number(): void
    {
        $this->from('/register')->post('/register', [
            'name' => 'Aino Korhonen',
            'phone' => 'not a phone number',
            'email' => 'aino@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('phone');

        $this->assertDatabaseMissing('users', ['email' => 'aino@example.com']);
    }

    public function test_registration_validates_non_string_phone_input_without_throwing(): void
    {
        $this->from('/register')->post('/register', [
            'name' => 'Aino Korhonen',
            'phone' => ['040', '1234567'],
            'email' => 'aino@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('phone');

        $this->assertDatabaseMissing('users', ['email' => 'aino@example.com']);
    }
}
