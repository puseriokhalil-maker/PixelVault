<?php

namespace App\Http\Controllers;

use App\Models\Artist;
use App\Models\Artwork;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminArtworkController extends Controller
{
    private const CATEGORIES = ['2D', '3D', 'Vector', 'Pixel Art'];

    public function index(): View
    {
        return view('admin.artworks.index', [
            'artworks' => Artwork::query()
                ->with('artist')
                ->latest('id')
                ->paginate(15),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.artworks.create', [
            'artists' => Artist::query()->orderBy('display_name')->get(['id', 'display_name']),
            'defaultArtistId' => $request->user()->artist()->value('id'),
            'categories' => self::CATEGORIES,
            'artwork' => new Artwork,
        ]);
    }

    public function edit(Artwork $artwork): View
    {
        Gate::authorize('update', $artwork);

        return view('admin.artworks.create', [
            'artists' => Artist::query()->orderBy('display_name')->get(['id', 'display_name']),
            'defaultArtistId' => $artwork->artist_id,
            'categories' => self::CATEGORIES,
            'artwork' => $artwork,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Artwork::class);

        $validated = $request->validate([
            'artist_id' => ['nullable', 'integer', 'exists:artists,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'category' => ['required', Rule::in(self::CATEGORIES)],
            'price' => ['required', 'numeric', 'regex:/^\d{1,8}(?:\.\d{1,2})?$/', 'min:0.01'],
            'image' => ['required', 'image', 'mimes:jpeg,png,webp', 'max:5120'],
        ], [
            'price.regex' => 'Enter a PHP price with no more than two decimal places.',
        ]);

        $image = $request->file('image');

        if (! $image instanceof UploadedFile) {
            throw ValidationException::withMessages([
                'image' => 'Please upload a valid artwork image.',
            ]);
        }

        $storedImagePath = $image->storePublicly('artworks', 'public');

        if ($storedImagePath === false) {
            throw ValidationException::withMessages([
                'image' => 'The artwork image could not be uploaded. Please try again.',
            ]);
        }

        [$wholePrice, $fractionalPrice] = array_pad(explode('.', $validated['price'], 2), 2, '');
        $priceAmount = ((int) $wholePrice * 100) + (int) str_pad($fractionalPrice, 2, '0');
        $artist = isset($validated['artist_id'])
            ? Artist::query()->findOrFail($validated['artist_id'])
            : $request->user()->artist()->firstOrCreate([], ['display_name' => $request->user()->name]);

        Artwork::query()->create([
            'artist_id' => $artist->id,
            'slug' => $this->uniqueSlug($validated['title']),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'image_path' => 'storage/'.$storedImagePath,
            'category' => $validated['category'],
            'price_amount' => $priceAmount,
            'currency' => 'php',
            'is_test_listing' => false,
            'is_published' => true,
        ]);

        return redirect()
            ->route('admin.artworks.index')
            ->with('status', 'Artwork added to the gallery.');
    }

    public function update(Request $request, Artwork $artwork): RedirectResponse
    {
        Gate::authorize('update', $artwork);

        $validated = $request->validate([
            'artist_id' => ['nullable', 'integer', 'exists:artists,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'category' => ['required', Rule::in(self::CATEGORIES)],
            'price' => ['required', 'numeric', 'regex:/^\d{1,8}(?:\.\d{1,2})?$/', 'min:0.01'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:5120'],
        ], [
            'price.regex' => 'Enter a PHP price with no more than two decimal places.',
        ]);

        $oldImagePath = $artwork->image_path;
        $attributes = [
            'artist_id' => $validated['artist_id'] ?? $artwork->artist_id,
            'slug' => $this->uniqueSlug($validated['title'], $artwork),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'price_amount' => $this->priceToAmount($validated['price']),
        ];

        if ($request->hasFile('image')) {
            $storedImagePath = $request->file('image')->storePublicly('artworks', 'public');

            if ($storedImagePath === false) {
                throw ValidationException::withMessages([
                    'image' => 'The artwork image could not be uploaded. Please try again.',
                ]);
            }

            $attributes['image_path'] = 'storage/'.$storedImagePath;
        }

        $artwork->update($attributes);

        if ($artwork->image_path !== $oldImagePath) {
            $this->deleteStoredImage($oldImagePath);
        }

        return redirect()
            ->route('admin.artworks.index')
            ->with('status', 'Artwork updated.');
    }

    public function destroy(Artwork $artwork): RedirectResponse
    {
        Gate::authorize('delete', $artwork);

        $imagePath = $artwork->image_path;
        $artwork->delete();

        $this->deleteStoredImage($imagePath);

        return redirect()
            ->route('admin.artworks.index')
            ->with('status', 'Artwork removed from the gallery.');
    }

    private function uniqueSlug(string $title, ?Artwork $artwork = null): string
    {
        $baseSlug = Str::slug($title) ?: 'artwork';
        $slug = $baseSlug;
        $suffix = 2;

        while (Artwork::query()
            ->where('slug', $slug)
            ->when($artwork, fn ($query) => $query->where('id', '!=', $artwork->id))
            ->exists()) {
            $slug = $baseSlug.'-'.$suffix;
            $suffix++;
        }

        return $slug;
    }

    private function priceToAmount(string $price): int
    {
        [$wholePrice, $fractionalPrice] = array_pad(explode('.', $price, 2), 2, '');

        return ((int) $wholePrice * 100) + (int) str_pad($fractionalPrice, 2, '0');
    }

    private function deleteStoredImage(?string $imagePath): void
    {
        if ($imagePath !== null && Str::startsWith($imagePath, 'storage/')) {
            Storage::disk('public')->delete(Str::after($imagePath, 'storage/'));
        }
    }
}
