<?php

namespace Database\Seeders;

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
