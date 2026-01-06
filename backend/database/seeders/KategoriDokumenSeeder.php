<?php

namespace Database\Seeders;

use App\Models\KategoriDokumen;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KategoriDokumenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategori = [
            [
                'kode' => 'PERBUB',
                'nama' => 'Peraturan Bupati',
                'deskripsi' => 'Peraturan yang ditetapkan oleh Bupati untuk melaksanakan peraturan perundang-undangan yang lebih tinggi',
                'order' => 1,
                'is_active' => true,
            ],
            [
                'kode' => 'PERDA',
                'nama' => 'Peraturan Daerah',
                'deskripsi' => 'Peraturan yang ditetapkan oleh DPRD bersama dengan Bupati',
                'order' => 2,
                'is_active' => true,
            ],
            [
                'kode' => 'KEPBUB',
                'nama' => 'Keputusan Bupati',
                'deskripsi' => 'Keputusan yang ditetapkan oleh Bupati untuk melaksanakan kebijakan tertentu',
                'order' => 3,
                'is_active' => true,
            ],
            [
                'kode' => 'INBUB',
                'nama' => 'Instruksi Bupati',
                'deskripsi' => 'Instruksi yang diberikan oleh Bupati untuk pelaksanaan tugas tertentu',
                'order' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($kategori as $item) {
            KategoriDokumen::updateOrCreate(
                ['kode' => $item['kode']],
                $item
            );
        }
    }
}
