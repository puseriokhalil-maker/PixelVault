<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ArtworkController extends Controller
{
    private const CATEGORIES = ['2D', '3D', 'Vector', 'Pixel Art'];

    public function index(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'category' => ['nullable', Rule::in(self::CATEGORIES)],
        ]);

        $artworks = Artwork::query()
            ->with('artist')
            ->where('is_published', true)
            ->when($filters['q'] ?? null, function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('title', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%')
                        ->orWhereHas('artist', fn ($artistQuery) => $artistQuery->where('display_name', 'like', '%'.$search.'%'));
                });
            })
            ->when($filters['category'] ?? null, fn ($query, string $category) => $query->where('category', $category))
            ->orderBy('id')
            ->get();

        return view('welcome', [
            'artworks' => $artworks,
            'categories' => self::CATEGORIES,
        ]);
    }

    public function show(Artwork $artwork): View
    {
        abort_unless($artwork->is_published, 404);

        return view('artworks.show', [
            'artwork' => $artwork->load('artist'),
        ]);
    }
}
