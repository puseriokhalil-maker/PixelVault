<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartCheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_add_and_remove_artwork_and_see_php_totals(): void
    {
        $artwork = $this->createArtwork();

        $this->post(route('cart.items.store', $artwork))
            ->assertRedirect(route('cart.index'));

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('PHP 125.00')
            ->assertSee('Order summary')
            ->assertSee('TEST LISTING')
            ->assertSee('no money is collected or transferred');

        $this->delete(route('cart.items.destroy', $artwork))
            ->assertRedirect(route('cart.index'));

        $this->get(route('cart.index'))
            ->assertSee('Your cart is empty');
    }

    public function test_cart_can_contain_artworks_from_multiple_artists(): void
    {
        $firstArtwork = $this->createArtwork();
        $secondArtwork = $this->createArtwork(
            artist: $this->createArtist(),
            slug: 'other-artist-work',
            title: 'Other sample artwork',
            priceAmount: 20000,
        );

        $this->post(route('cart.items.store', $firstArtwork));
        $this->post(route('cart.items.store', $secondArtwork));

        $this->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Sample Artwork')
            ->assertSee('Other sample artwork')
            ->assertSee('PHP 325.00');
    }

    public function test_guest_checkout_requires_an_authenticated_buyer(): void
    {
        $artwork = $this->createArtwork();
        $this->post(route('cart.items.store', $artwork));

        $this->post(route('checkout.store'))
            ->assertRedirect(route('login'));
    }

    public function test_checkout_completes_a_simulated_order_without_collecting_money(): void
    {
        $buyer = User::factory()->create();
        $firstArtwork = $this->createArtwork(priceAmount: 12500);
        $secondArtwork = $this->createArtwork(
            artist: $this->createArtist(),
            slug: 'other-artist-work',
            title: 'Other sample artwork',
            priceAmount: 20000,
        );
        $this->actingAs($buyer)->post(route('cart.items.store', $firstArtwork));
        $this->post(route('cart.items.store', $secondArtwork));

        $response = $this->post(route('checkout.store'));
        $order = Order::query()->firstOrFail();

        $response->assertRedirect(route('orders.show', $order))
            ->assertSessionMissing('pixelvault.cart');
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'user_id' => $buyer->id,
            'artist_id' => null,
            'status' => 'simulated',
            'subtotal_amount' => 32500,
            'platform_fee_amount' => 0,
            'total_amount' => 32500,
            'currency' => 'php',
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'title' => 'Sample Artwork',
            'unit_amount' => 12500,
            'quantity' => 1,
        ]);
        $this->assertDatabaseHas('order_items', [
            'order_id' => $order->id,
            'title' => 'Other sample artwork',
            'unit_amount' => 20000,
            'quantity' => 1,
        ]);

        $this->get(route('orders.show', $order))
            ->assertOk()
            ->assertSee('Simulated purchase complete')
            ->assertSee('no real payment was taken and no money was transferred');
    }

    public function test_checkout_rejects_an_empty_cart_without_creating_an_order(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('checkout.store'))
            ->assertRedirect(route('cart.index'))
            ->assertSessionHasErrors('cart');

        $this->assertDatabaseCount('orders', 0);
    }

    private function createArtwork(
        ?Artist $artist = null,
        string $slug = 'sample-artwork',
        string $title = 'Sample Artwork',
        int $priceAmount = 12500,
    ): Artwork {
        $artist ??= $this->createArtist();

        return Artwork::query()->create([
            'artist_id' => $artist->id,
            'slug' => $slug,
            'title' => $title,
            'description' => 'Sample listing for cart tests.',
            'category' => '2D',
            'price_amount' => $priceAmount,
            'currency' => 'php',
            'is_test_listing' => true,
            'is_published' => true,
        ]);
    }

    private function createArtist(): Artist
    {
        $user = User::factory()->create();

        return Artist::query()->create([
            'user_id' => $user->id,
            'display_name' => $user->name,
        ]);
    }
}
