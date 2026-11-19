<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Order;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function store(Request $request, CartService $cart): RedirectResponse
    {
        $items = $cart->items();

        if ($items->isEmpty()) {
            return redirect()->route('cart.index')->withErrors([
                'cart' => 'Your cart is empty.',
            ]);
        }

        $subtotal = (int) $items->sum('price_amount');

        $order = DB::transaction(function () use ($items, $request, $subtotal): Order {
            $artistIds = $items->pluck('artist_id')->unique();
            $order = Order::query()->create([
                'user_id' => $request->user()->id,
                'artist_id' => $artistIds->count() === 1 ? $artistIds->first() : null,
                'status' => 'simulated',
                'subtotal_amount' => $subtotal,
                'platform_fee_amount' => 0,
                'total_amount' => $subtotal,
                'currency' => config('marketplace.currency'),
                'completed_at' => now(),
            ]);

            foreach ($items as $artwork) {
                $lockedArtwork = Artwork::query()
                    ->whereKey($artwork->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if (! $lockedArtwork->is_published || $lockedArtwork->is_test_listing) {
                    throw ValidationException::withMessages([
                        'cart' => 'One of the artworks is no longer available.',
                    ]);
                }

                $order->items()->create([
                    'artwork_id' => $lockedArtwork->id,
                    'title' => $lockedArtwork->title,
                    'unit_amount' => $lockedArtwork->price_amount,
                    'quantity' => 1,
                ]);

                $lockedArtwork->update([
                    'owner_id' => $request->user()->id,
                    'is_published' => false,
                ]);
            }

            return $order;
        });

        $cart->clear();

        return redirect()->route('orders.show', $order);
    }
}
