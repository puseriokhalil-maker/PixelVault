<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArtworkControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_stays_empty_until_an_artwork_is_added(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('No artworks have been added yet.')
            ->assertDontSee('AMIGO')
            ->assertDontSee('PLACEHOLDER');

        $this->assertDatabaseCount('artworks', 0);
    }

    public function test_gallery_search_and_category_filters_query_published_artworks(): void
    {
        $artist = $this->createArtist('Gallery Artist');
        $this->createArtwork($artist, 'color-study', 'Color Study', '2D');
        $this->createArtwork($artist, 'city-lights', 'City Lights', 'Pixel Art');

        $this->get(route('home', ['q' => 'Color']))
            ->assertOk()
            ->assertSee('Color Study')
            ->assertDontSee('City Lights');

        $this->get(route('home', ['category' => 'Pixel Art']))
            ->assertOk()
            ->assertSee('City Lights')
            ->assertDontSee('Color Study');
    }

    public function test_gallery_hides_unpublished_and_test_artworks(): void
    {
        $artist = $this->createArtist('Gallery Artist');
        $this->createArtwork($artist, 'private-artwork', 'Private Artwork', '2D', isPublished: false);
        $this->createArtwork($artist, 'test-artwork', 'Test Artwork', '2D', isTestListing: true);

        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('Private Artwork')
            ->assertDontSee('Test Artwork')
            ->assertSee('No artworks have been added yet.');
    }

    public function test_artwork_detail_page_shows_its_saved_image_and_details(): void
    {
        $artist = $this->createArtist('Digital Artist');
        $artwork = $this->createArtwork($artist, 'color-study', 'Color Study', 'Vector', imagePath: 'artworks/color-study.png');

        $this->get(route('artworks.show', $artwork))
            ->assertOk()
            ->assertSee('Color Study')
            ->assertSee('Digital Artist')
            ->assertSee('Vector')
            ->assertSee('Artwork description.')
            ->assertSee('src="'.asset('artworks/color-study.png').'"', false)
            ->assertDontSee('PLACEHOLDER');
    }

    public function test_artwork_with_no_image_does_not_render_a_placeholder(): void
    {
        $artwork = $this->createArtwork($this->createArtist('Digital Artist'), 'old-artwork', 'Old Artwork', '2D', imagePath: null);

        $this->get(route('artworks.show', $artwork))
            ->assertOk()
            ->assertSee('Old Artwork')
            ->assertDontSee('PLACEHOLDER');
    }

    public function test_user_can_add_and_edit_their_own_artwork(): void
    {
        Storage::fake('public');
        $user = User::factory()->create(['name' => 'Artwork Creator']);
        $otherArtist = $this->createArtist('Someone Else');

        $response = $this->actingAs($user)->post(route('artworks.store'), [
            'artist_id' => $otherArtist->id,
            'title' => 'First Artwork',
            'description' => 'The original description.',
            'category' => '2D',
            'price' => '75.25',
            'is_published' => false,
            'is_test_listing' => true,
            'image' => $this->pngImage(),
        ]);

        $response->assertRedirect(route('artworks.mine'))
            ->assertSessionHas('status', 'Your artwork has been added to the gallery.');

        $artwork = Artwork::query()->where('slug', 'first-artwork')->firstOrFail();
        $imagePath = str_replace('storage/', '', $artwork->image_path);
        $this->assertSame($user->id, $artwork->artist->user_id);
        $this->assertSame(7525, $artwork->price_amount);
        $this->assertTrue($artwork->is_published);
        $this->assertFalse($artwork->is_test_listing);
        Storage::disk('public')->assertExists($imagePath);

        $this->actingAs($user)->put(route('artworks.update', $artwork), [
            'title' => 'Updated Artwork',
            'description' => 'The updated description.',
            'category' => 'Vector',
            'price' => '100.00',
            'artist_id' => $otherArtist->id,
            'is_published' => false,
        ])->assertRedirect(route('artworks.mine'));

        $artwork->refresh();
        $this->assertSame('updated-artwork', $artwork->slug);
        $this->assertSame('Updated Artwork', $artwork->title);
        $this->assertSame('The updated description.', $artwork->description);
        $this->assertSame('Vector', $artwork->category);
        $this->assertSame(10000, $artwork->price_amount);
        $this->assertSame($user->artist->id, $artwork->artist_id);
        $this->assertTrue($artwork->is_published);
        $this->assertSame('storage/'.$imagePath, $artwork->image_path);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Updated Artwork');

        $this->delete(route('artworks.destroy', $artwork))
            ->assertRedirect(route('artworks.mine'));

        $this->assertDatabaseMissing('artworks', ['id' => $artwork->id]);
        Storage::disk('public')->assertMissing($imagePath);
    }

    public function test_users_cannot_edit_or_delete_another_users_artwork(): void
    {
        $ownerArtwork = $this->createArtwork($this->createArtist('Owner'), 'owner-artwork', 'Owner Artwork', '2D');
        $otherUser = User::factory()->create();

        $this->actingAs($otherUser)
            ->put(route('artworks.update', $ownerArtwork), [
                'title' => 'Changed by another user',
                'description' => 'This should not be saved.',
                'category' => 'Vector',
                'price' => '9.99',
            ])
            ->assertForbidden();

        $this->delete(route('artworks.destroy', $ownerArtwork))
            ->assertForbidden();

        $this->delete(route('admin.artworks.destroy', $ownerArtwork))
            ->assertForbidden();

        $ownerArtwork->refresh();
        $this->assertModelExists($ownerArtwork);
        $this->assertSame('Owner Artwork', $ownerArtwork->fresh()->title);
    }

    public function test_admin_can_edit_and_delete_any_users_artwork(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['is_admin' => true]);
        $owner = $this->createArtist('Owner');
        $artwork = $this->createArtwork($owner, 'owner-artwork', 'Owner Artwork', '2D', imagePath: 'storage/artworks/owner.png');
        Storage::disk('public')->put('artworks/owner.png', 'image');

        $this->actingAs($admin)
            ->put(route('artworks.update', $artwork), [
                'title' => 'Admin Updated Artwork',
                'description' => 'Updated by an admin.',
                'category' => 'Vector',
                'price' => '55.50',
            ])
            ->assertRedirect(route('admin.artworks.index'));

        $artwork->refresh();
        $this->assertSame('Admin Updated Artwork', $artwork->title);
        $this->assertSame($owner->id, $artwork->artist_id);

        $this->delete(route('admin.artworks.destroy', $artwork))
            ->assertRedirect(route('admin.artworks.index'));

        $this->assertDatabaseMissing('artworks', ['id' => $artwork->id]);
        Storage::disk('public')->assertMissing('artworks/owner.png');
    }

    public function test_guests_are_redirected_to_login_from_artwork_creation(): void
    {
        $this->get(route('artworks.create'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_users_visiting_artworks_path_are_redirected_to_creation_form(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/artworks')
            ->assertRedirect(route('artworks.create'));
    }

    public function test_artwork_creation_rejects_missing_or_invalid_images(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from(route('artworks.create'))
            ->post(route('artworks.store'), [
                'title' => 'Artwork Without Image',
                'description' => 'An image is required.',
                'category' => '2D',
                'price' => '20.00',
            ])
            ->assertRedirect(route('artworks.create'))
            ->assertSessionHasErrors('image');

        $this->assertDatabaseCount('artworks', 0);
        Storage::disk('public')->assertEmpty();
    }

    public function test_unknown_artwork_detail_returns_not_found(): void
    {
        $this->get(route('artworks.show', 'unknown-artwork'))
            ->assertNotFound();
    }

    public function test_search_rejects_unknown_categories(): void
    {
        $this->get(route('home', ['category' => 'unknown']))
            ->assertSessionHasErrors('category');
    }

    private function createArtwork(
        Artist $artist,
        string $slug,
        string $title,
        string $category,
        bool $isTestListing = false,
        bool $isPublished = true,
        ?string $imagePath = 'artworks/artwork.png',
    ): Artwork {
        return Artwork::query()->create([
            'artist_id' => $artist->id,
            'slug' => $slug,
            'title' => $title,
            'description' => 'Artwork description.',
            'image_path' => $imagePath,
            'category' => $category,
            'price_amount' => 12500,
            'currency' => 'php',
            'is_test_listing' => $isTestListing,
            'is_published' => $isPublished,
        ]);
    }

    private function createArtist(string $displayName): Artist
    {
        $user = User::factory()->create(['name' => $displayName]);

        return Artist::query()->create([
            'user_id' => $user->id,
            'display_name' => $displayName,
        ]);
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
