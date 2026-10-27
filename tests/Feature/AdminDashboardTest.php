<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_from_the_admin_dashboard(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_regular_users_are_forbidden_from_the_admin_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_registration_cannot_grant_admin_access(): void
    {
        $this->post(route('register.store'), [
            'name' => 'New User',
            'email' => 'new-user@example.com',
            'password' => 'password-123',
            'password_confirmation' => 'password-123',
            'is_admin' => true,
        ])->assertRedirect(route('dashboard'));

        $user = User::query()->where('email', 'new-user@example.com')->firstOrFail();

        $this->assertFalse($user->is_admin);
        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }

    public function test_configured_seed_credentials_create_the_admin_account(): void
    {
        config([
            'marketplace.admin.email' => 'pixelvault-admin@example.com',
            'marketplace.admin.password' => 'a-unique-admin-password-123',
        ]);

        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'pixelvault-admin@example.com')->firstOrFail();

        $this->assertTrue($admin->is_admin);
        $this->assertTrue(Hash::check('a-unique-admin-password-123', $admin->password));
        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk();
    }

    public function test_admin_sees_only_simulated_sales_and_orders(): void
    {
        $admin = User::factory()->create();
        $admin->is_admin = true;
        $admin->save();

        $buyer = User::factory()->create([
            'name' => 'Demo Buyer',
            'email' => 'buyer@example.com',
        ]);
        $artistUser = User::factory()->create();
        $artist = Artist::query()->create([
            'user_id' => $artistUser->id,
            'display_name' => 'Sample Artist',
        ]);
        $firstOrder = $this->createOrder($buyer, $artist, 'simulated', 12500);
        $secondOrder = $this->createOrder($buyer, $artist, 'simulated', 25000);
        $unconfirmedOrder = $this->createOrder($buyer, $artist, 'pending', 99000);
        OrderItem::query()->create([
            'order_id' => $firstOrder->id,
            'title' => 'Demo artwork one',
            'unit_amount' => 12500,
            'quantity' => 1,
        ]);
        OrderItem::query()->create([
            'order_id' => $secondOrder->id,
            'title' => 'Demo artwork two',
            'unit_amount' => 25000,
            'quantity' => 1,
        ]);
        OrderItem::query()->create([
            'order_id' => $unconfirmedOrder->id,
            'title' => 'Unconfirmed artwork',
            'unit_amount' => 99000,
            'quantity' => 1,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('simulatedOrderCount', 2)
            ->assertSee('Theoretical sales total')
            ->assertSee('PHP 375.00')
            ->assertSee('Simulated orders')
            ->assertSee('buyer@example.com')
            ->assertSee('Sample Artist')
            ->assertSee('Demo artwork one')
            ->assertSee('Demo artwork two')
            ->assertDontSee('Unconfirmed artwork')
            ->assertSee('No payment processor or bank is connected.');
    }

    private function createOrder(User $buyer, Artist $artist, string $status, int $amount): Order
    {
        return Order::query()->create([
            'user_id' => $buyer->id,
            'artist_id' => $artist->id,
            'status' => $status,
            'subtotal_amount' => $amount,
            'platform_fee_amount' => 0,
            'total_amount' => $amount,
            'currency' => 'php',
        ]);
    }
}
