<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Destination;
use App\Models\Package;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil data prasyarat
        try {
            $partner = User::findOrFail(2);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            $this->command->warn('Partner dengan ID 2 tidak ditemukan. Seeder Paket dibatalkan.');
            return;
        }

        $destinations = Destination::whereNotNull('parent_id')
                                   ->whereBetween('id', [5, 9])
                                   ->get();
        
        $categories = Category::all();

        // 2. Pastikan ada data untuk dihubungkan
        if ($destinations->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('Tidak dapat membuat seeder Paket: Pastikan ada destinasi anak (ID 5-9) dan kategori yang tersedia.');
            return;
        }

        // 3. Definisikan data paket wisata
        $packagesData = [
            [
                'name' => 'Jelajah Alam Tersembunyi di Lembah Harau',
                'description' => 'Paket petualangan 3 hari 2 malam menjelajahi air terjun dan perbukitan hijau yang jarang terjamah di Lembah Harau.',
                'duration_days' => 3,
                'price' => 1500000,
            ],
            [
                'name' => 'Snorkeling Seru di Pulau Pahawang',
                'description' => 'Nikmati akhir pekan yang santai dengan paket snorkeling 2 hari 1 malam di surga bawah laut Pahawang.',
                'duration_days' => 2,
                'price' => 1250000,
            ],
            [
                'name' => 'Petualangan Gajah di Way Kambas',
                'description' => 'Pengalaman tak terlupakan berinteraksi langsung dengan gajah Sumatera di habitat aslinya.',
                'duration_days' => 2,
                'price' => 1800000,
            ],
        ];

        // 4. Buat entri paket di database sesuai struktur yang Anda berikan
        foreach ($packagesData as $data) {
            Package::create([
                // 'id' -> Dibuat otomatis oleh database
                'partner_id' => $partner->id, // Menggunakan ID 3
                'destination_id' => $destinations->random()->id, // Acak dari ID 5-9
                'category_id' => $categories->random()->id, // Acak
                'name' => $data['name'],
                'slug' => Str::slug($data['name']), // Dibuat otomatis dari nama
                'description' => $data['description'],
                'duration_days' => $data['duration_days'],
                'price' => $data['price'],
                'status' => 'pending', // Status default saat dibuat
                // 'created_at' & 'updated_at' -> Dibuat otomatis oleh Eloquent
            ]);
        }
    }
}
