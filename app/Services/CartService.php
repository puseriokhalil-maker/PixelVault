<?php

namespace App\Services;

use App\Models\Artwork;
use Illuminate\Database\Eloquent\Collection;

class CartService
{
    private const SESSION_KEY = 'pixelvault.cart';

    /**
     * @return Collection<int, Artwork>
     */
    public function items(): Collection
    {
        $cart = session()->get(self::SESSION_KEY, []);
        $artworkIds = array_map('intval', array_keys($cart));
        $artworks = Artwork::query()
            ->with('artist')
            ->where('is_published', true)
            ->whereIn('id', $artworkIds)
            ->get();

        if ($artworks->count() !== count($cart)) {
            session()->put(self::SESSION_KEY, $artworks->mapWithKeys(
                fn (Artwork $artwork): array => [(string) $artwork->id => 1],
            )->all());
        }

        return $artworks;
    }

    public function add(Artwork $artwork): void
    {
        $cart = session()->get(self::SESSION_KEY, []);
        $cart[(string) $artwork->id] = 1;

        session()->put(self::SESSION_KEY, $cart);
    }

    public function remove(Artwork $artwork): void
    {
        $cart = session()->get(self::SESSION_KEY, []);
        unset($cart[(string) $artwork->id]);

        session()->put(self::SESSION_KEY, $cart);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function subtotal(): int
    {
        return (int) $this->items()->sum('price_amount');
    }
}
