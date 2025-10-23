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
        ];

        foreach ($categories as $categoryName) {
            Category::firstOrCreate(
                ['slug' => Str::slug($categoryName)], 
                ['name' => $categoryName]           
            );
        }
    }
}
