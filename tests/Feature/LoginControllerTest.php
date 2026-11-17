<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders_the_sign_in_form(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk()
            ->assertSee('Welcome back')
            ->assertSee('Email address')
            ->assertSee('Password')
            ->assertDontSee('AMIGO')
            ->assertDontSee('Krimpaps');
    }

    public function test_valid_credentials_sign_in_and_redirect_to_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
            'remember' => true,
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_return_to_login_with_an_error(): void
    {
        $user = User::factory()->create();

        $response = $this->from(route('login'))->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'incorrect-password',
        ]);

        $response->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_five_failed_attempts_lock_login_and_show_the_cooldown(): void
    {
        $user = User::factory()->create();
        $this->freezeTime();

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $response = $this->from(route('login'))->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'incorrect-password',
            ]);
        }

        $response->assertRedirect(route('login'))
            ->assertSessionHasErrors([
                'email' => 'Too many failed login attempts. Please try again in 60 seconds.',
            ]);

        $this->followingRedirects()
            ->from(route('login'))
            ->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'password',
            ])
            ->assertOk()
            ->assertSee('Too many failed login attempts. Please try again in 60 seconds.');

        $this->assertGuest();
    }

    public function test_login_is_allowed_again_after_the_cooldown_expires(): void
    {
        $user = User::factory()->create();

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->from(route('login'))->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'incorrect-password',
            ]);
        }

        $this->travel(61)->seconds();

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_successful_login_clears_previous_failed_attempts(): void
    {
        $user = User::factory()->create();

        for ($attempt = 0; $attempt < 4; $attempt++) {
            $this->from(route('login'))->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'incorrect-password',
            ]);
        }

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->post(route('logout'));

        for ($attempt = 0; $attempt < 4; $attempt++) {
            $response = $this->from(route('login'))->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'incorrect-password',
            ]);
        }

        $response->assertSessionHasErrors(['email' => trans('auth.failed')]);

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'incorrect-password',
            ])
            ->assertSessionHasErrors([
                'email' => 'Too many failed login attempts. Please try again in 60 seconds.',
            ]);
    }

    public function test_login_lockout_is_scoped_to_the_email_address(): void
    {
        $lockedUser = User::factory()->create();
        $otherUser = User::factory()->create();

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->from(route('login'))->post(route('login.store'), [
                'email' => $lockedUser->email,
                'password' => 'incorrect-password',
            ]);
        }

        $this->post(route('login.store'), [
            'email' => $otherUser->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($otherUser);
    }

    public function test_missing_credentials_are_rejected(): void
    {
        $response = $this->post(route('login.store'), []);

        $response->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }
}
