<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Services\CartService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(CartService $cart): View
    {
        $items = $cart->items();
        $subtotal = (int) $items->sum('price_amount');

        return view('cart.index', [
            'items' => $items,
            'subtotal' => $subtotal,
        ]);
    }

    public function store(Artwork $artwork, CartService $cart): RedirectResponse
    {
        abort_unless($artwork->is_published, 404);

        $cart->add($artwork);

        return redirect()->route('cart.index')->with('status', 'Artwork added to your cart.');
    }

    public function destroy(Artwork $artwork, CartService $cart): RedirectResponse
    {
        $cart->remove($artwork);

        return redirect()->route('cart.index')->with('status', 'Artwork removed from your cart.');
    }
}
