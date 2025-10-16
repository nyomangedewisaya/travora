<?php

namespace Database\Seeders;

use App\Models\Destination;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DestinationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Destinasi Induk (Provinsi/Wilayah) dengan parent_id NULL
        // =================================================================

        $sumateraUtara = Destination::create([
            'parent_id' => null,
            'name' => 'Sumatera Utara',
            'slug' => 'sumatera-utara',
            'description' => 'Provinsi yang terkenal dengan Danau Toba, danau vulkanik terbesar di dunia, serta budaya Batak yang kaya.',
        ]);

        $sumateraBarat = Destination::create([
            'parent_id' => null,
            'name' => 'Sumatera Barat',
            'slug' => 'sumatera-barat',
            'description' => 'Tanah Minangkabau yang mempesona dengan kuliner legendaris, arsitektur Rumah Gadang, dan keindahan alamnya.',
        ]);

        $lampung = Destination::create([
            'parent_id' => null,
            'name' => 'Lampung',
            'slug' => 'lampung',
            'description' => 'Gerbang Pulau Sumatera yang menawarkan keindahan bahari, teluk yang menawan, dan konservasi gajah di Way Kambas.',
        ]);

        $aceh = Destination::create([
            'parent_id' => null,
            'name' => 'Aceh',
            'slug' => 'aceh',
            'description' => 'Dikenal sebagai Serambi Mekkah, Aceh memiliki keindahan alam bawah laut di Sabang dan sejarah yang kaya.',
        ]);


        // 2. Buat Destinasi Anak (Tempat Wisata Spesifik) yang mengarah ke Induknya
        // =========================================================================

        // Anak dari Sumatera Utara
        Destination::create([
            'parent_id' => $sumateraUtara->id,
            'name' => 'Danau Toba',
            'slug' => 'danau-toba',
            'description' => 'Danau vulkanik ikonik dengan Pulau Samosir di tengahnya, menawarkan pemandangan spektakuler dan budaya Batak yang kental.',
            'latitude' => 2.6107,
            'longitude' => 98.8222,
        ]);

        // Anak dari Sumatera Barat
        Destination::create([
            'parent_id' => $sumateraBarat->id,
            'name' => 'Jam Gadang',
            'slug' => 'jam-gadang',
            'description' => 'Menara jam yang menjadi ikon dan pusat Kota Bukittinggi, dikelilingi oleh pasar tradisional dan kuliner khas.',
            'latitude' => -0.3055,
            'longitude' => 100.3706,
        ]);

        Destination::create([
            'parent_id' => $sumateraBarat->id,
            'name' => 'Lembah Harau',
            'slug' => 'lembah-harau',
            'description' => 'Cagar alam dengan tebing-tebing granit yang menjulang tinggi, sawah yang hijau, dan beberapa air terjun yang indah.',
            'latitude' => -0.0983,
            'longitude' => 100.6732,
        ]);

        // Anak dari Lampung
        Destination::create([
            'parent_id' => $lampung->id,
            'name' => 'Pulau Pahawang',
            'slug' => 'pulau-pahawang',
            'description' => 'Destinasi populer untuk snorkeling dan island hopping dengan pemandangan bawah laut yang jernih dan pantai pasir putih.',
            'latitude' => -5.6738,
            'longitude' => 105.2195,
        ]);

        Destination::create([
            'parent_id' => $lampung->id,
            'name' => 'Taman Nasional Way Kambas',
            'slug' => 'taman-nasional-way-kambas',
            'description' => 'Pusat konservasi gajah sumatera, di mana pengunjung dapat melihat gajah di habitat aslinya dan belajar tentang upaya pelestarian.',
            'latitude' => -4.8943,
            'longitude' => 105.7744,
        ]);
        
        // Anak dari Aceh
        Destination::create([
            'parent_id' => $aceh->id,
            'name' => 'Pantai Iboih',
            'slug' => 'pantai-iboih',
            'description' => 'Terletak di Pulau Weh, Sabang, pantai ini terkenal dengan airnya yang sebening kristal dan menjadi surga bagi para penyelam.',
            'latitude' => 5.8814,
            'longitude' => 95.2573,
        ]);
    }
}
