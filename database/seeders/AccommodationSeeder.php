<?php

namespace Database\Seeders;

use App\Models\Accommodation;
use App\Models\Category;
use App\Models\Destination;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AccommodationSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil data prasyarat
        $partnerIds = User::where('role', 'partner')->pluck('id');
        $destinationIds = Destination::whereNotNull('parent_id')->pluck('id'); 
        // Ambil ID kategori yang relevan (misal, yang baru ditambahkan)
        // Untuk simpelnya, ambil semua ID dulu

        // 2. Pastikan ada data untuk dihubungkan
        if ($partnerIds->isEmpty() || $destinationIds->isEmpty()) {
            $this->command->warn('Tidak dapat membuat seeder Akomodasi: Pastikan ada user Partner, destinasi anak, dan kategori yang tersedia.');
            return;
        }

        // 3. Tipe dan Status yang Valid
        $types = ['hotel', 'villa', 'homestay'];
        $statuses = ['pending', 'publish', 'rejected', 'draft']; // Tambahkan draft jika perlu

        // 4. Definisikan data akomodasi (lebih banyak)
        $accommodationsData = [
            // ... (Copy array $accommodationsData dari jawaban seeder sebelumnya, yang berisi 15 contoh) ...
             ['name' => 'Villa Toba Indah', 'type' => 'villa', 'address' => 'Jl. Lingkar Tuktuk Siadong, Samosir', 'description' => 'Villa pribadi dengan pemandangan langsung ke Danau Toba, cocok untuk keluarga.'],
             ['name' => 'Samosir Cottages Resort', 'type' => 'hotel', 'address' => 'Tuktuk Siadong, Simanindo, Samosir', 'description' => 'Resort tepi danau dengan fasilitas lengkap dan akses mudah ke atraksi lokal.'],
             ['name' => 'Homestay Ompungboru', 'type' => 'homestay', 'address' => 'Desa Tomok, Simanindo, Samosir', 'description' => 'Pengalaman menginap autentik di rumah tradisional Batak Toba.'],
             ['name' => 'Toba Villa Adventure', 'type' => 'villa', 'address' => 'Kawasan Bukit Holbung, Samosir', 'description' => 'Berkemah mewah dengan pemandangan perbukitan hijau dan Danau Toba.'],
             ['name' => 'Grand Rocky Hotel Bukittinggi', 'type' => 'hotel', 'address' => 'Jl. Yos Sudarso No.29, Bukittinggi', 'description' => 'Hotel bintang 4 di pusat kota dengan pemandangan Jam Gadang.'],
             ['name' => 'Villa Ngarai Sianok', 'type' => 'villa', 'address' => 'Jl. Ngarai Sianok, Koto Gadang', 'description' => 'Villa tenang di tepi Ngarai Sianok yang menakjubkan.'],
             ['name' => 'Homestay Uni Eli', 'type' => 'homestay', 'address' => 'Jl. Panorama Baru, Bukittinggi', 'description' => 'Penginapan sederhana dan bersih dekat Lobang Jepang.'],
             ['name' => 'Harau Valley Homestay', 'type' => 'homestay', 'address' => 'Desa Harau, Kec. Harau, Lima Puluh Kota', 'description' => 'Menginap di tengah sawah dengan pemandangan tebing Lembah Harau.'],
             ['name' => 'Abdi Homestay & Bungalow', 'type' => 'homestay', 'address' => 'Lembah Harau, Tarantang', 'description' => 'Bungalow kayu yang nyaman dengan suasana pedesaan.'],
             ['name' => 'Andreas Resort Pahawang', 'type' => 'hotel', 'address' => 'Pulau Pahawang Besar, Punduh Pidada', 'description' => 'Resort pinggir pantai dengan fasilitas snorkeling dan diving.'],
             ['name' => 'Villa Andreas Pahawang', 'type' => 'villa', 'address' => 'Pulau Pahawang Besar, Punduh Pidada', 'description' => 'Villa eksklusif di atas air dengan akses langsung ke laut.'],
             ['name' => 'Homestay Dolphin Pahawang', 'type' => 'homestay', 'address' => 'Pulau Pahawang Kecil, Punduh Pidada', 'description' => 'Penginapan sederhana milik nelayan lokal, sering melihat lumba-lumba.'],
             ['name' => 'Freddies Santai Sumurtiga', 'type' => 'homestay', 'address' => 'Jl. Sumur Tiga, Sabang', 'description' => 'Homestay populer dengan suasana santai dan pemandangan laut yang indah.'],
             ['name' => 'Casanemo Resort Sabang', 'type' => 'hotel', 'address' => 'Pantai Iboih, Sabang', 'description' => 'Resort nyaman dekat titik snorkeling dan diving terbaik di Iboih.'],
             ['name' => 'Iboih Inn Resort & Restaurant', 'type' => 'hotel', 'address' => 'Pantai Iboih, Sabang', 'description' => 'Akomodasi tepi pantai dengan restoran yang menyajikan hidangan laut segar.'],
        ];

        // 5. Buat entri di database SESUAI SKEMA BARU
        foreach ($accommodationsData as $data) {
            Accommodation::create([
                'partner_id' => $partnerIds->random(),
                'destination_id' => $destinationIds->random(),
                'name' => $data['name'],
                'slug' => Str::slug($data['name']) . '-' . Str::random(3), 
                'type' => $data['type'] ?? $types[array_rand($types)], 
                'address' => $data['address'],
                'description' => $data['description'],
                'status' => $statuses[rand(0, count($statuses) - 1)], // Pilih status acak
                'is_verified' => rand(0, 1) == 1, 
            ]);
        }
        
        $this->command->info(count($accommodationsData) . ' data akomodasi berhasil ditambahkan.');
    }
}
