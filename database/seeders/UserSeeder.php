<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pengguna Admin
        User::create([
            'name' => 'Admin Travora',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'phone_number' => '081234567890',
            'partner_status' => null, // Admin tidak memiliki status partner
        ]);

        // 2. Pengguna Partner (sudah diverifikasi agar bisa langsung login)
        User::create([
            'name' => 'Budi Partner',
            'email' => 'partner@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'partner',
            'phone_number' => '089876543210',
            'partner_status' => 'verified',
        ]);

        // 3. Pengguna Customer
        User::create([
            'name' => 'Citra Pelanggan',
            'email' => 'customer@gmail.com',
            'password' => Hash::make('password123'),
            'role' => 'customer',
            'phone_number' => '085555555555',
            'partner_status' => null,
        ]);
    }
}
