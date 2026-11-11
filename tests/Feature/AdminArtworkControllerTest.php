<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminArtworkControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login_from_the_add_artwork_page(): void
    {
        $this->get(route('admin.artworks.create'))
            ->assertRedirect(route('login'));
    }

    public function test_regular_users_cannot_add_artworks(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('admin.artworks.store'))
            ->assertForbidden();

        $this->assertDatabaseCount('artworks', 0);
    }

    public function test_admin_can_create_a_published_gallery_listing_with_an_uploaded_image(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create();
        $admin->is_admin = true;
        $admin->save();
        $artistUser = User::factory()->create();
        $artist = Artist::query()->create([
            'user_id' => $artistUser->id,
            'display_name' => 'Gallery Artist',
        ]);
        $image = $this->pngImage();

        $response = $this->actingAs($admin)
            ->post(route('admin.artworks.store'), [
                'artist_id' => $artist->id,
                'title' => 'New Digital Art',
                'description' => 'A newly uploaded piece for the gallery.',
                'category' => 'Vector',
                'price' => '100.25',
                'is_test_listing' => '1',
                'image' => $image,
            ]);

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('status', 'Artwork added to the gallery.');

        $artwork = Artwork::query()->where('slug', 'new-digital-art')->firstOrFail();
        $this->assertModelExists($artwork);
        $this->assertSame($artist->id, $artwork->artist_id);
        $this->assertSame(10025, $artwork->price_amount);
        $this->assertTrue($artwork->is_test_listing);
        $this->assertTrue($artwork->is_published);

        $storedImagePath = str_replace('storage/', '', $artwork->image_path);
        Storage::disk('public')->assertExists($storedImagePath);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('New Digital Art')
            ->assertSee('Gallery Artist')
            ->assertSee('src="'.$artwork->image_url.'"', false);

        $this->get(route('artworks.show', $artwork))
            ->assertOk()
            ->assertSee('A newly uploaded piece for the gallery.')
            ->assertSee('TEST LISTING')
            ->assertSee('src="'.$artwork->image_url.'"', false);
    }

    public function test_admin_form_lists_existing_artists_and_gallery_fields(): void
    {
        $admin = User::factory()->create();
        $admin->is_admin = true;
        $admin->save();
        $artistUser = User::factory()->create();
        Artist::query()->create([
            'user_id' => $artistUser->id,
            'display_name' => 'Existing Artist',
        ]);

        $this->actingAs($admin)
            ->get(route('admin.artworks.create'))
            ->assertOk()
            ->assertSee('Add artwork to the gallery')
            ->assertSee('Existing Artist')
            ->assertSee('Artwork image')
            ->assertSee('Price (PHP)')
            ->assertSee('Add to gallery');
    }

    public function test_admin_cannot_create_a_listing_with_an_invalid_image_or_price(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create();
        $admin->is_admin = true;
        $admin->save();
        $artistUser = User::factory()->create();
        $artist = Artist::query()->create([
            'user_id' => $artistUser->id,
            'display_name' => 'Gallery Artist',
        ]);

        $this->actingAs($admin)
            ->from(route('admin.artworks.create'))
            ->post(route('admin.artworks.store'), [
                'artist_id' => $artist->id,
                'title' => 'Invalid Image',
                'description' => 'This listing must not be stored.',
                'category' => '2D',
                'price' => '12.345',
                'image' => UploadedFile::fake()->createWithContent('not-an-image.txt', 'not an image'),
            ])
            ->assertRedirect(route('admin.artworks.create'))
            ->assertSessionHasErrors(['price', 'image']);

        $this->assertDatabaseCount('artworks', 0);
        Storage::disk('public')->assertEmpty();
    }

    private function pngImage(): UploadedFile
    {
        $contents = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR4nGNgYAAAAAMAASsJTYQAAAAASUVORK5CYII=',
            true,
        );

        $this->assertNotFalse($contents);

        return UploadedFile::fake()->createWithContent('artwork.png', $contents);
    }
}
