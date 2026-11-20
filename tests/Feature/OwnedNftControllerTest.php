<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnedNftControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_sees_purchased_artwork_details_and_can_resell_it(): void
    {
        $owner = User::factory()->create();
        $artwork = $this->createOwnedArtwork($owner);

        $this->actingAs($owner)
            ->get(route('owned-nfts.index'))
            ->assertOk()
            ->assertSee('Owned NFTs')
            ->assertSee('Purchased NFT')
            ->assertSee('Pixel artwork description.')
            ->assertSee('PHP 150.00');

        $this->actingAs($owner)
            ->post(route('owned-nfts.resell', $artwork), ['price' => '175.50'])
            ->assertRedirect(route('owned-nfts.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'owner_id' => $owner->id,
            'price_amount' => 17550,
            'is_published' => true,
        ]);
    }

    public function test_owner_can_unlist_a_resold_artwork(): void
    {
        $owner = User::factory()->create();
        $artwork = $this->createOwnedArtwork($owner);
        $artwork->update(['is_published' => true]);

        $this->actingAs($owner)
            ->post(route('owned-nfts.unlist', $artwork))
            ->assertRedirect(route('owned-nfts.index'))
            ->assertSessionHas('status');

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'owner_id' => $owner->id,
            'is_published' => false,
        ]);
    }

    public function test_user_cannot_relist_an_artwork_owned_by_another_user(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $artwork = $this->createOwnedArtwork($owner);

        $this->actingAs($otherUser)
            ->post(route('owned-nfts.resell', $artwork), ['price' => '200.00'])
            ->assertForbidden();

        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'owner_id' => $owner->id,
            'is_published' => false,
        ]);
    }

    private function createOwnedArtwork(User $owner): Artwork
    {
        $artistUser = User::factory()->create();
        $artist = Artist::query()->create([
            'user_id' => $artistUser->id,
            'display_name' => 'Original Artist',
        ]);

        return Artwork::query()->create([
            'artist_id' => $artist->id,
            'owner_id' => $owner->id,
            'slug' => 'purchased-nft',
            'title' => 'Purchased NFT',
            'description' => 'Pixel artwork description.',
            'category' => 'Pixel Art',
            'price_amount' => 15000,
            'currency' => 'php',
            'is_test_listing' => false,
            'is_published' => false,
        ]);
    }
}
