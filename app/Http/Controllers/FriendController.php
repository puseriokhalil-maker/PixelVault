<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FriendController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $user = $request->user();

        $searchResults = $search === ''
            ? collect()
            : User::query()
                ->whereKeyNot($user->id)
                ->where(function ($query) use ($search): void {
                    $query->where('name', 'like', '%'.$search.'%')
                        ->orWhere('email', 'like', '%'.$search.'%');
                })
                ->limit(10)
                ->get();

        return view('friends.index', [
            'friends' => $user->friends()->get(),
            'searchResults' => $searchResults,
            'search' => $search,
        ]);
    }

    public function store(Request $request, User $user): RedirectResponse
    {
        $currentUser = $request->user();
        abort_if($currentUser->is($user), 422);

        DB::transaction(function () use ($currentUser, $user): void {
            $currentUser->friends()->syncWithoutDetaching([$user->id]);
            $user->friends()->syncWithoutDetaching([$currentUser->id]);
        });

        return back()->with('status', $user->name.' was added to your friends.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $currentUser = $request->user();

        DB::transaction(function () use ($currentUser, $user): void {
            $currentUser->friends()->detach($user->id);
            $user->friends()->detach($currentUser->id);
        });

        return back()->with('status', $user->name.' was removed from your friends.');
    }

    public function trade(Request $request, User $friend): RedirectResponse
    {
        $currentUser = $request->user();
        abort_unless($currentUser->friends()->whereKey($friend->id)->exists(), 403);

        $validated = $request->validate([
            'offered_artwork_id' => [
                'required',
                'integer',
                Rule::exists('artworks', 'id')->where(fn ($query) => $query->where('owner_id', $currentUser->id)),
            ],
            'requested_artwork_id' => [
                'required',
                'integer',
                Rule::exists('artworks', 'id')->where(fn ($query) => $query->where('owner_id', $friend->id)),
            ],
        ]);

        DB::transaction(function () use ($validated, $currentUser, $friend): void {
            $offeredArtwork = Artwork::query()->whereKey($validated['offered_artwork_id'])->lockForUpdate()->firstOrFail();
            $requestedArtwork = Artwork::query()->whereKey($validated['requested_artwork_id'])->lockForUpdate()->firstOrFail();

            $offeredArtwork->update([
                'owner_id' => $friend->id,
                'is_published' => false,
            ]);
            $requestedArtwork->update([
                'owner_id' => $currentUser->id,
                'is_published' => false,
            ]);
        });

        return back()->with('status', 'NFTs were traded with '.$friend->name.'.');
    }
}
