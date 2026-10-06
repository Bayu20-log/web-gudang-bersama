<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pemasok;
use Carbon\Carbon;

/**
 * Data pemasok TIDAK ADA di Excel Log Stok RAKSAKTI, jadi ini dummy realistis
 * untuk kebutuhan bar minuman (bahan baku, kemasan, dessert).
 */
class PemasokSeeder extends Seeder
{
    protected int $userId = 2;

    public function run(): void
    {
        $pemasoks = [
            [
                'nama_pemasok' => 'CV Sumber Rasa Nusantara',
                'jenis'        => 'Bahan Baku',
                'email'        => 'sumberrasa@example.com',
                'nama_pic'     => 'Budi Santoso',
                'alamat'       => 'Jl. Industri No. 12, Balikpapan',
                'no_telepon'   => '081234500001',
                'bulan_lalu'   => 18,
            ],
            [
                'nama_pemasok' => 'Toko Kemasan Jaya',
                'jenis'        => 'Kemasan',
                'email'        => 'kemasanjaya@example.com',
                'nama_pic'     => 'Sri Wahyuni',
                'alamat'       => 'Jl. Sudirman No. 45, Balikpapan',
                'no_telepon'   => '081234500002',
                'bulan_lalu'   => 12,
            ],
            [
                'nama_pemasok' => 'UD Sumber Kopi Nusantara',
                'jenis'        => 'Bahan Baku',
                'email'        => 'sumberkopi@example.com',
                'nama_pic'     => 'Andi Prasetyo',
                'alamat'       => 'Jl. MT Haryono No. 8, Balikpapan',
                'no_telepon'   => '081234500003',
                'bulan_lalu'   => 24,
            ],
            [
                'nama_pemasok' => 'PT Boga Cita Rasa',
                'jenis'        => 'Dessert & Bahan Kue',
                'email'        => 'bogacitarasa@example.com',
                'nama_pic'     => 'Dewi Lestari',
                'alamat'       => 'Jl. Ahmad Yani No. 21, Balikpapan',
                'no_telepon'   => '081234500004',
                'bulan_lalu'   => 6,
            ],
        ];

        foreach ($pemasoks as $p) {
            Pemasok::create([
                'user_id'         => $this->userId,
                'nama_pemasok'    => $p['nama_pemasok'],
                'email'           => $p['email'],
                'jenis'           => $p['jenis'],
                'nama_pic'        => $p['nama_pic'],
                'alamat'          => $p['alamat'],
                'no_telepon'      => $p['no_telepon'],
                'bergabung_sejak' => Carbon::now()->subMonths($p['bulan_lalu'])->format('Y-m-d'),
            ]);
        }
    }
}