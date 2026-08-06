<?php
namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class PemasokSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'nama_pemasok' => 'PT Alat Kantor',
                'nama_pic' => 'Agus Riyanto',
                'email' => 'alatkantor@mail.com',
                'alamat' => 'Jakarta',
                'no_telepon' => '021999888',
                'jenis' => 'ATK',
                'bergabung_sejak' => '2022-01-01',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pemasok' => 'CV Elektronik',
                'nama_pic' => 'Sari Lestari',
                'email' => 'elektronik@mail.com',
                'alamat' => 'Bandung',
                'no_telepon' => '022555999',
                'jenis' => 'Elektronik',
                'bergabung_sejak' => '2023-03-15',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama_pemasok' => 'PT Kebersihan Sehat',
                'nama_pic' => 'Rahmat Hidayat',
                'email' => 'kebersihan@mail.com',
                'alamat' => 'Surabaya',
                'no_telepon' => '031888111',
                'jenis' => 'Kebersihan',
                'bergabung_sejak' => '2021-11-20',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];


        // Tambahkan dummy data dari 4 sampai 20
        for ($i = 4; $i <= 20; $i++) {
            $data[] = [
                'nama_pemasok' => "Pemasok Dummy $i",
                'nama_pic' => "PIC Dummy $i",
                'email' => "dummy$i@mail.com",
                'alamat' => "Kota Dummy $i",
                'no_telepon' => "08$i$i$i$i$i",
                'jenis' => 'Umum',
                'bergabung_sejak' => now()->subDays($i)->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }


        DB::table('pemasoks')->insert($data);
    }
}
