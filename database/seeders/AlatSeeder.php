<?php

namespace Database\Seeders;

use App\Models\Alat;
use Illuminate\Database\Seeder;

class AlatSeeder extends Seeder
{

    public function run(): void
    {
        $alat = [
            [
                'kategori_id' => 1,
                'nama_alat' => 'Router Mikrotik RB941-2nD',
                'stok' => 15,
                'status_kondisi' => 'Baik',
                'deskripsi' => 'Router nirkabel rumahan yang cocok untuk praktik jaringan dasar.',
                'gambar' => 'router_mikrotik_rb941-2nd.jpg'
            ],
            [
                'kategori_id' => 2,
                'nama_alat' => 'Kamera DSLR Canon EOS 1500D',
                'stok' => 10,
                'status_kondisi' => 'Baik',
                'deskripsi' => 'Kamera DSLR entry-level yang cocok untuk pemula dalam fotografi dan videografi.',
                'gambar' => 'kamera_dslr_canon_eos_1500d.jpg'
            ],
            [
                'kategori_id' => 3,
                'nama_alat' => 'Laptop Dell Inspiron 15',
                'stok' => 20,
                'status_kondisi' => 'Baik',
                'deskripsi' => 'Laptop serbaguna untuk pekerjaan sehari-hari dan tugas-tugas ringan.',
                'gambar' => 'laptop_dell_inspiron_15.jpg'
            ],
            [
                'kategori_id' => 4,
                'nama_alat' => 'Hard Disk Eksternal Seagate 1TB',
                'stok' => 25,
                'status_kondisi' => 'Baik',
                'deskripsi' => 'Hard disk eksternal dengan kapasitas besar untuk penyimpanan data tambahan.',
                'gambar' => 'hard_disk_eksternal_seagate_1tb.jpg'
            ],
            [
                'kategori_id' => 5,
                'nama_alat' => 'Keyboard Mechanical Razer BlackWidow',
                'stok' => 30,
                'status_kondisi' => 'Baik',
                'deskripsi' => 'Keyboard mekanis dengan pencahayaan RGB dan respons cepat untuk gaming.',
                'gambar' => 'keyboard_mechanical_razer_blackwidow.jpg'
            ]
        ];

        foreach ($alat as $al) {
            Alat::create($al);
        }
    }
}
