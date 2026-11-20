<?php

namespace Tests\Feature;

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FriendControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_search_and_add_an_existing_user_as_a_friend(): void
    {
        $user = User::factory()->create(['name' => 'Main User']);
        $friend = User::factory()->create([
            'name' => 'Alex Collector',
            'email' => 'alex@example.com',
        ]);

        $this->actingAs($user)
            ->get(route('friends.index', ['search' => 'Alex']))
            ->assertOk()
            ->assertSee('Alex Collector');

        $this->actingAs($user)
            ->post(route('friends.store', $friend))
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseHas('friendships', [
            'user_id' => $user->id,
            'friend_id' => $friend->id,
        ]);
        $this->assertDatabaseHas('friendships', [
            'user_id' => $friend->id,
            'friend_id' => $user->id,
        ]);
    }

    public function test_friends_can_trade_owned_nfts(): void
    {
        $user = User::factory()->create();
        $friend = User::factory()->create();
        $user->friends()->sync([$friend->id]);
        $friend->friends()->sync([$user->id]);

        $offeredArtwork = $this->createOwnedArtwork($user, 'offered-nft', 'Offered NFT');
        $requestedArtwork = $this->createOwnedArtwork($friend, 'requested-nft', 'Requested NFT');

        $this->actingAs($user)
            ->post(route('friends.trade', $friend), [
                'offered_artwork_id' => $offeredArtwork->id,
                'requested_artwork_id' => $requestedArtwork->id,
            ])
            ->assertRedirect()
            ->assertSessionHas('status');

        $this->assertDatabaseHas('artworks', [
            'id' => $offeredArtwork->id,
            'owner_id' => $friend->id,
        ]);
        $this->assertDatabaseHas('artworks', [
            'id' => $requestedArtwork->id,
            'owner_id' => $user->id,
        ]);
    }

    public function test_non_friend_cannot_trade_nfts(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $offeredArtwork = $this->createOwnedArtwork($user, 'offered-nft', 'Offered NFT');
        $requestedArtwork = $this->createOwnedArtwork($otherUser, 'requested-nft', 'Requested NFT');

        $this->actingAs($user)
            ->post(route('friends.trade', $otherUser), [
                'offered_artwork_id' => $offeredArtwork->id,
                'requested_artwork_id' => $requestedArtwork->id,
            ])
            ->assertForbidden();

        $this->assertDatabaseHas('artworks', [
            'id' => $offeredArtwork->id,
            'owner_id' => $user->id,
        ]);
        $this->assertDatabaseHas('artworks', [
            'id' => $requestedArtwork->id,
            'owner_id' => $otherUser->id,
        ]);
    }

    private function createOwnedArtwork(User $owner, string $slug, string $title): Artwork
    {
        $artistUser = User::factory()->create();
        $artist = Artist::query()->create([
            'user_id' => $artistUser->id,
            'display_name' => 'Test Artist',
        ]);

        return Artwork::query()->create([
            'artist_id' => $artist->id,
            'owner_id' => $owner->id,
            'slug' => $slug,
            'title' => $title,
            'description' => 'Test NFT.',
            'category' => '2D',
            'price_amount' => 10000,
            'currency' => 'php',
            'is_test_listing' => false,
            'is_published' => false,
        ]);
    }
}
