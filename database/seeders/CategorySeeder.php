<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Petualangan Alam',
            'Wisata Budaya',
            'Kuliner Lokal',
            'Relaksasi & Pantai',
            'Wisata Sejarah',
            'Hotel Bintang 5', 'Hotel Bintang 4', 'Hotel Bintang 3',
            'Villa Pribadi', 'Villa Tepi Pantai',
            'Homestay Budget', 'Homestay Keluarga',
            'Glamping Unik', 'Resort Eksklusif', 'Penginapan Transit'
        ];

        foreach ($categories as $categoryName) {
            Category::firstOrCreate(
                ['slug' => Str::slug($categoryName)], 
                ['name' => $categoryName]           
            );
        }
    }
}
