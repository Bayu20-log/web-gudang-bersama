<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangKeluarSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 100; $i++) {
            DB::table('barang_keluars')->insert([
                'item_id' => rand(1, 100),
                'tanggal_keluar' => now()->subDays(rand(0, 180)),
                'jumlah_keluar' => rand(1, 50),
                'tujuan_pengeluaran' => 'Divisi ' . rand(1, 5),
                'penerima' => 'Penerima ' . $i,
                'lokasi_tujuan' => 'Lokasi Tujuan ' . rand(1, 5),
                'id_kondisi' => rand(1, 3),
                'catatan' => 'Digunakan untuk kegiatan operasional.',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
