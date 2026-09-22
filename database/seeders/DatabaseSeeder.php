<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'SuperAdmin',
            'email' => 'admin@engixcare.com',
            'is_admin' => 1,
            'password' => Hash::make('Engixcare@2026'),
            'email_verified_at' => now(),
        ]);

        $this->call([
            PromotionOfferSeeder::class,
            ReviewSeeder::class,
        ]);
    }
}
