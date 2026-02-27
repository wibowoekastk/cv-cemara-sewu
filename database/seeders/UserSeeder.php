<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Hanya membuat 1 akun Owner awal untuk akses pertama kali
        // Sisanya (Admin/Mandor) akan dibuat lewat menu "Tambah User" di dashboard Owner
        if (!User::where('email', 'owner@cemarasewu.com')->exists()) {
            User::create([
                'name' => 'Bapak Owner',
                'email' => 'owner@cemarasewu.com',
                'password' => Hash::make('password123'), // Password default
                'role' => 'owner',
                'status' => 'active',
            ]);
        }
    }
}