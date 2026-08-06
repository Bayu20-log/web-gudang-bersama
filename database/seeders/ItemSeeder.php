<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 100; $i++) {
            DB::table('items')->insert([
                'kode_barang' => 'BRG' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'nama_barang' => 'Item ' . $i,
                'id_kategori' => rand(1, 4),
                'id_satuan' => rand(1, 4),
                'id_lokasi' => rand(1, 4),
                'deskripsi' => 'Deskripsi barang ke-' . $i,
                'foto' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}