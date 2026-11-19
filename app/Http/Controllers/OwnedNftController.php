<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OwnedNftController extends Controller
{
    public function index(Request $request): View
    {
        return view('owned-nfts.index', [
            'artworks' => $request->user()
                ->ownedArtworks()
                ->with('artist')
                ->latest('id')
                ->paginate(12),
        ]);
    }

    public function resell(Request $request, Artwork $artwork): RedirectResponse
    {
        abort_unless($artwork->owner_id === $request->user()->id, 403);

        $validated = $request->validate([
            'price' => ['required', 'numeric', 'regex:/^\d{1,8}(?:\.\d{1,2})?$/', 'min:0.01'],
        ], [
            'price.regex' => 'Enter a PHP price with no more than two decimal places.',
        ]);

        [$wholePrice, $fractionalPrice] = array_pad(explode('.', $validated['price'], 2), 2, '');

        $artwork->update([
            'price_amount' => ((int) $wholePrice * 100) + (int) str_pad($fractionalPrice, 2, '0'),
            'is_published' => true,
        ]);

        return redirect()
            ->route('owned-nfts.index')
            ->with('status', 'Your NFT has been relisted in the gallery.');
    }

    public function unlist(Request $request, Artwork $artwork): RedirectResponse
    {
        abort_unless($artwork->owner_id === $request->user()->id, 403);

        $artwork->update(['is_published' => false]);

        return redirect()
            ->route('owned-nfts.index')
            ->with('status', 'Your NFT has been removed from the gallery.');
    }
}
