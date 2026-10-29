<?php

namespace Database\Seeders;

use App\Models\Artist;
use App\Models\Artwork;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->seedAdmin();

        $user = User::query()->firstOrCreate(['email' => 'test@example.com'], [
            'name' => 'Test User',
            'password' => 'password',
        ]);

        $artist = Artist::query()->firstOrCreate(
            ['user_id' => $user->id],
            ['display_name' => 'PixelVault Test Artist'],
        );

        $artworks = [
            ['slug' => 'sample-neon-solitude', 'title' => 'Neon Solitude', 'category' => '2D', 'price_amount' => 10000],
            ['slug' => 'sample-chromatic-drift', 'title' => 'Chromatic Drift', 'category' => '3D', 'price_amount' => 15000],
            ['slug' => 'sample-quiet-geometry', 'title' => 'Quiet Geometry', 'category' => 'Vector', 'price_amount' => 18000],
            ['slug' => 'sample-digital-bloom', 'title' => 'Digital Bloom', 'category' => '2D', 'price_amount' => 22000],
            ['slug' => 'sample-midnight-city', 'title' => 'Midnight City', 'category' => 'Pixel Art', 'price_amount' => 25000],
            ['slug' => 'sample-soft-machines', 'title' => 'Soft Machines', 'category' => '3D', 'price_amount' => 30000],
            ['slug' => 'sample-paper-garden', 'title' => 'Paper Garden', 'category' => 'Vector', 'price_amount' => 35000],
            ['slug' => 'sample-afterglow', 'title' => 'Afterglow Study', 'category' => '2D', 'price_amount' => 40000],
            ['slug' => 'sample-archive-01', 'title' => 'Archive No. 01', 'category' => 'Pixel Art', 'price_amount' => 100000],
        ];

        foreach ($artworks as $artwork) {
            Artwork::query()->updateOrCreate(
                ['slug' => $artwork['slug']],
                [
                    ...$artwork,
                    'artist_id' => $artist->id,
                    'description' => 'A sample digital artwork listing for testing the PixelVault marketplace. The final artwork and product details will replace this preview.',
                    'currency' => 'php',
                    'is_test_listing' => true,
                    'is_published' => true,
                ],
            );
        }
    }

    private function seedAdmin(): void
    {
        $email = config('marketplace.admin.email');
        $password = config('marketplace.admin.password');

        if (blank($email) && blank($password)) {
            return;
        }

        if (
            ! is_string($email)
            || filter_var($email, FILTER_VALIDATE_EMAIL) === false
            || ! is_string($password)
            || mb_strlen($password) < 12
        ) {
            throw new InvalidArgumentException(
                'Set MARKETPLACE_ADMIN_EMAIL and a MARKETPLACE_ADMIN_PASSWORD of at least 12 characters.',
            );
        }

        $admin = User::query()->firstOrNew(['email' => $email]);

        if (! $admin->exists) {
            $admin->name = 'PixelVault Admin';
            $admin->password = $password;
        } elseif (! Hash::check($password, $admin->password)) {
            throw new InvalidArgumentException(
                'The configured marketplace admin email already exists with a different password.',
            );
        }

        $admin->is_admin = true;
        $admin->save();
    }
}
