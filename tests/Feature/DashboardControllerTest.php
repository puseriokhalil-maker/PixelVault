<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\Order;
use App\Models\OrderItem;
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

    public function test_authenticated_users_can_view_their_buyer_account(): void
    {
        $user = User::factory()->create([
            'name' => 'Alex Collector',
            'email' => 'alex@example.com',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('My account')
            ->assertSee('Alex Collector')
            ->assertSee('alex@example.com')
            ->assertSee('Order history')
            ->assertSee('No orders yet')
            ->assertSee(route('home').'#collection')
            ->assertDontSee('Total Sales')
            ->assertDontSee('Add Artwork');
    }

    public function test_dashboard_library_shows_only_completed_demo_items_belonging_to_the_user(): void
    {
        $user = User::factory()->create();
        $artist = Artist::query()->create([
            'user_id' => $user->id,
            'display_name' => 'Sample Artist',
        ]);
        $paidOrder = Order::query()->create([
            'user_id' => $user->id,
            'artist_id' => $artist->id,
            'status' => 'simulated',
            'subtotal_amount' => 10000,
            'platform_fee_amount' => 0,
            'total_amount' => 10000,
            'currency' => 'php',
        ]);
        $pendingOrder = Order::query()->create([
            'user_id' => $user->id,
            'artist_id' => $artist->id,
            'status' => 'pending',
            'subtotal_amount' => 20000,
            'platform_fee_amount' => 0,
            'total_amount' => 20000,
            'currency' => 'php',
        ]);
        OrderItem::query()->create([
            'order_id' => $paidOrder->id,
            'title' => 'Paid sample artwork',
            'unit_amount' => 10000,
            'quantity' => 1,
        ]);
        OrderItem::query()->create([
            'order_id' => $pendingOrder->id,
            'title' => 'Pending sample artwork',
            'unit_amount' => 20000,
            'quantity' => 1,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Paid sample artwork')
            ->assertDontSee('Pending sample artwork')
            ->assertSee('1 item');
    }

    public function test_homepage_navigation_includes_a_dashboard_link(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('dashboard'))
            ->assertSee('Dashboard');
    }
}
