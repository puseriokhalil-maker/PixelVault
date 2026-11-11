<?php

namespace App\Http\Controllers;

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

class MyArtworkController extends Controller
{
    private const CATEGORIES = ['2D', '3D', 'Vector', 'Pixel Art'];

    public function index(Request $request): View
    {
        $user = $request->user();

        return view('artworks.mine', [
            'artworks' => Artwork::query()
                ->with('artist')
                ->whereHas('artist', fn ($query) => $query->where('user_id', $user->id))
                ->latest('id')
                ->paginate(12),
        ]);
    }

    public function create(): View
    {
        return view('artworks.form', [
            'artwork' => new Artwork,
            'categories' => self::CATEGORIES,
            'formAction' => route('artworks.store'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Artwork::class);
        $validated = $this->validateArtwork($request, true);
        $storedImagePath = $this->storeImage($request);
        $user = $request->user();
        $artist = $user->artist()->firstOrCreate([], ['display_name' => $user->name]);

        $artist->artworks()->create([
            'slug' => $this->uniqueSlug($validated['title']),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'image_path' => 'storage/'.$storedImagePath,
            'category' => $validated['category'],
            'price_amount' => $this->priceToAmount($validated['price']),
            'currency' => 'php',
            'is_test_listing' => false,
            'is_published' => true,
        ]);

        return redirect()
            ->route('artworks.mine')
            ->with('status', 'Your artwork has been added to the gallery.');
    }

    public function edit(Artwork $artwork): View
    {
        Gate::authorize('update', $artwork);

        return view('artworks.form', [
            'artwork' => $artwork,
            'categories' => self::CATEGORIES,
            'formAction' => route('artworks.update', $artwork),
        ]);
    }

    public function update(Request $request, Artwork $artwork): RedirectResponse
    {
        Gate::authorize('update', $artwork);
        $validated = $this->validateArtwork($request, false);
        $oldImagePath = $artwork->image_path;

        $attributes = [
            'slug' => $this->uniqueSlug($validated['title'], $artwork),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'price_amount' => $this->priceToAmount($validated['price']),
        ];

        if ($request->hasFile('image')) {
            $attributes['image_path'] = 'storage/'.$this->storeImage($request);
        }

        $artwork->update($attributes);

        if ($artwork->image_path !== $oldImagePath) {
            $this->deleteStoredImage($oldImagePath);
        }

        return redirect()
            ->route($request->user()->is_admin ? 'admin.artworks.index' : 'artworks.mine')
            ->with('status', 'Your artwork has been updated.');
    }

    public function destroy(Request $request, Artwork $artwork): RedirectResponse
    {
        Gate::authorize('delete', $artwork);
        $imagePath = $artwork->image_path;
        $artwork->delete();
        $this->deleteStoredImage($imagePath);

        return redirect()
            ->route($request->user()->is_admin ? 'admin.artworks.index' : 'artworks.mine')
            ->with('status', 'Your artwork has been removed.');
    }

    /**
     * @return array{title: string, description: string, category: string, price: string}
     */
    private function validateArtwork(Request $request, bool $requireImage): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'category' => ['required', Rule::in(self::CATEGORIES)],
            'price' => ['required', 'numeric', 'regex:/^\d{1,8}(?:\.\d{1,2})?$/', 'min:0.01'],
            'image' => [$requireImage ? 'required' : 'nullable', 'image', 'mimes:jpeg,png,webp', 'max:5120'],
        ], [
            'price.regex' => 'Enter a PHP price with no more than two decimal places.',
        ]);
    }

    private function storeImage(Request $request): string
    {
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

        return $storedImagePath;
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
