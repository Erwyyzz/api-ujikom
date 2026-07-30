<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{

    public function run(): void
    {
        $kategori = [
            ['nama_kategori' => 'Jaringan & Konektivitas'],
            ['nama_kategori' => 'Multimedia & Audio Visual'],
            ['nama_kategori' => 'Perangkat Pemerosesan'],
            ['nama_kategori' => 'Perangkat Penyimpanan'],
            ['nama_kategori' => 'Perangkat Input']
        ];

        foreach ($kategori as $kat) {
            Kategori::create($kat);
        }
    }
}
