<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArtworkControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_gallery_search_and_category_filters_query_artworks(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('home', ['q' => 'AMIGO']))
            ->assertOk()
            ->assertSee('AMIGO')
            ->assertSee('Krimpaps')
            ->assertDontSee('Chromatic Drift');

        $this->get(route('home', ['category' => 'Pixel Art']))
            ->assertOk()
            ->assertSee('Midnight City')
            ->assertDontSee('AMIGO');
    }

    public function test_gallery_cards_link_to_the_database_backed_detail_pages(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('artworks.show', 'amigo'))
            ->assertSee('View Details')
            ->assertSee('AMIGO')
            ->assertSee('Krimpaps')
            ->assertSee('src="'.asset('artworks/amigo.png').'"', false);
    }

    public function test_amigo_detail_page_shows_its_image_details_and_no_test_listing_notice(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('artworks.show', 'amigo'))
            ->assertOk()
            ->assertSee('AMIGO')
            ->assertSee('Krimpaps')
            ->assertSee('2D')
            ->assertSee('Meme Amigo')
            ->assertSee('src="'.asset('artworks/amigo.png').'"', false)
            ->assertDontSee('TEST LISTING');
    }

    public function test_artwork_detail_page_shows_the_selected_placeholder_listing(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('artworks.show', 'sample-digital-bloom'))
            ->assertOk()
            ->assertSee('PLACEHOLDER')
            ->assertSee('Digital Bloom')
            ->assertSee('PixelVault Test Artist')
            ->assertSee('PHP 220.00')
            ->assertSee('TEST LISTING — SAMPLE ARTWORK AND TEST PRICE')
            ->assertSee('Back to gallery');
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
}
