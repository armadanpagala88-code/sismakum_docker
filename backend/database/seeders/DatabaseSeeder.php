<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seed Kategori Dokumen
        $this->call([
            KategoriDokumenSeeder::class,
        ]);

        // Create sample users (using updateOrCreate to avoid duplicate errors)
        User::updateOrCreate(
            ['email' => 'admin@sismakum.local'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'unit_kerja' => 'Administrator'
            ]
        );

        User::updateOrCreate(
            ['email' => 'dinas@sismakum.local'],
            [
                'name' => 'Dinas Pendidikan',
                'password' => Hash::make('password'),
                'role' => 'dinas',
                'unit_kerja' => 'Dinas Pendidikan'
            ]
        );

        User::updateOrCreate(
            ['email' => 'hukum@sismakum.local'],
            [
                'name' => 'Bagian Hukum',
                'password' => Hash::make('password'),
                'role' => 'bagian_hukum',
                'unit_kerja' => 'Bagian Hukum'
            ]
        );

        User::updateOrCreate(
            ['email' => 'verifikator@sismakum.local'],
            [
                'name' => 'Verifikator',
                'password' => Hash::make('password'),
                'role' => 'verifikator',
                'unit_kerja' => 'Tim Verifikasi'
            ]
        );
    }
}

