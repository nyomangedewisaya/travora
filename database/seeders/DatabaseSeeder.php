<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            DestinationSeeder::class,
            CategorySeeder::class,
            PackageSeeder::class,
            AccommodationSeeder::class

            // php artisan db:seed --class=NamaSeeder
        ]);
    }
}
