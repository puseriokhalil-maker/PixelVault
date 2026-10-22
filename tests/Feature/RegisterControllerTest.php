<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_renders_an_account_creation_form(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Create your account')
            ->assertSee('Confirm password')
            ->assertSee(route('register.store'));
    }

    public function test_valid_registration_creates_and_authenticates_a_user(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Avery Artist',
            'email' => 'avery@example.com',
            'password' => 'long-enough-password',
            'password_confirmation' => 'long-enough-password',
        ]);

        $user = User::query()->where('email', 'avery@example.com')->firstOrFail();

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('long-enough-password', $user->password));
    }

    public function test_registration_rejects_duplicate_email_and_mismatched_passwords(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $this->from(route('register'))
            ->post(route('register.store'), [
                'name' => 'Avery Artist',
                'email' => 'existing@example.com',
                'password' => 'long-enough-password',
                'password_confirmation' => 'different-password',
            ])
            ->assertRedirect(route('register'))
            ->assertSessionHasErrors(['email', 'password']);

        $this->assertGuest();
        $this->assertDatabaseCount('users', 1);
    }

    public function test_authenticated_users_are_redirected_from_registration(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('register'))
            ->assertRedirect(route('dashboard'));
    }
}
