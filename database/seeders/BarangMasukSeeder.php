<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BarangMasukSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 300; $i++) {
            $jumlah = rand(1, 100);
            $harga_satuan = rand(100000, 1000000);
            $total_harga = $jumlah * $harga_satuan;

            DB::table('barang_masuks')->insert([
                'item_id' => rand(1, 100),
                'jumlah' => $jumlah,
                'harga_satuan' => $harga_satuan,
                'tanggal_masuk' => now()->subDays(rand(0, 365)),
                'tanggal_kadaluarsa' => now()->subDays(rand(0, 365)),
                'id_pemasok' => rand(1, 3),
                'total_harga' => $total_harga,
                'id_lokasi' => rand(1, 4),
                'id_kondisi' => rand(1, 3),
                'catatan' => 'Masuk ke sistem.',
                'qr_code' => Str::uuid(),
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
