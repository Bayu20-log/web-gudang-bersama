<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class DummySeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        foreach (range(1, 20) as $i) {
            $item = Item::create([
                'kode_barang' => 'BRG' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'nama_barang' => ucfirst($faker->unique()->word()),
                'kategori' => $faker->randomElement(['Alat Tulis', 'Elektronik', 'ATK', 'Listrik']),
                'satuan' => $faker->randomElement(['pcs', 'unit', 'pak']),
                'lokasi' => $faker->randomElement(['Rak A1', 'Rak B2', 'Gudang Utama']),
            ]);

            foreach (range(1, rand(3, 6)) as $_) {
                BarangMasuk::create([
                    'item_id' => $item->id,
                    'jumlah' => rand(10, 100),
                    'tanggal_masuk' => $faker->dateTimeBetween('-2 years', 'now'),
                    'pemasok' => $faker->company(),
                    'lokasi' => $item->lokasi,
                    'user_id' => 1 // pastikan user_id 1 ada untuk testing
                ]);
            }

            foreach (range(1, rand(2, 5)) as $_) {
                BarangKeluar::create([
                    'item_id' => $item->id,
                    'jumlah_keluar' => rand(1, 60),
                    'tanggal_keluar' => $faker->dateTimeBetween('-2 years', 'now'),
                    'penerima' => $faker->name(),
                    'lokasi_tujuan' => $item->lokasi,
                    'user_id' => 1 // pastikan user_id 1 ada untuk testing
                ]);
            }
        }
    }
}