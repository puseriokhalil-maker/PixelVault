<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_when_opening_the_dashboard(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'))
            ->assertSessionHas('url.intended', route('dashboard'));
    }

    public function test_authenticated_users_can_view_the_artwork_dashboard(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Artwork Management')
            ->assertSee('Total Sales')
            ->assertSee('Neon Solitude')
            ->assertSee('Add Artwork');
    }

    public function test_homepage_navigation_includes_a_dashboard_link(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('dashboard'))
            ->assertSee('Dashboard');
    }
}
