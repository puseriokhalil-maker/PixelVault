<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_users_can_log_out_and_session_data_is_cleared(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withSession(['logout-test' => 'session data'])
            ->post(route('logout'));

        $response->assertRedirect(route('home'))
            ->assertSessionMissing('logout-test');
        $this->assertGuest();
    }

    public function test_guests_are_redirected_to_login_when_posting_to_logout(): void
    {
        $this->post(route('logout'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_navigation_shows_a_post_logout_form(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('logout'))
            ->assertSee('Logout')
            ->assertSee('method="POST"', false)
            ->assertSee('name="_token"', false);
    }
}
