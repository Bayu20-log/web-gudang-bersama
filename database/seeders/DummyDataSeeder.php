<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('barang_keluars')->truncate();
        DB::table('barang_masuks')->truncate();
        DB::table('items')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $kategoriList = ['Alat Tulis', 'Elektronik', 'ATK', 'Listrik'];
        $lokasiList = ['Rak A1', 'Rak B2', 'Gudang Utama'];
        $satuanList = ['pcs', 'unit', 'pak'];
        $kondisiList = ['baik', 'rusak ringan', 'rusak berat'];

        foreach (range(1, 100) as $i) {
            $item = Item::create([
                'kode_barang' => 'BRG' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'nama_barang' => ucfirst($faker->unique()->word()),
                'kategori' => $faker->randomElement($kategoriList),
                'satuan' => $faker->randomElement($satuanList),
                'harga_per_unit' => $faker->numberBetween(1000, 50000),
                'lokasi' => $faker->randomElement($lokasiList),
                'kondisi' => $faker->randomElement($kondisiList),
                'deskripsi' => $faker->sentence(),
                'foto' => 'default.jpg',
            ]);

            foreach (range(1, 5) as $_) {
                BarangMasuk::create([
                    'item_id' => $item->id,
                    'jumlah' => $jumlah = $faker->numberBetween(10, 100),
                    'tanggal_masuk' => $faker->dateTimeBetween('2024-01-01', '2025-12-31'),
                    'pemasok' => $faker->company(),
                    'total_harga' => $jumlah * $item->harga_per_unit,
                    'lokasi' => $item->lokasi,
                    'kondisi' => $faker->randomElement($kondisiList),
                    'catatan' => $faker->sentence(),
                    'qr_code' => Str::uuid(),
                    'user_id' => 1
                ]);
            }

            foreach (range(1, 5) as $_) {
                BarangKeluar::create([
                    'item_id' => $item->id,
                    'jumlah_keluar' => $jumlah = $faker->numberBetween(1, 60),
                    'tanggal_keluar' => $faker->dateTimeBetween('2024-01-01', '2025-12-31'),
                    'tujuan_pengeluaran' => $faker->randomElement(['Pengadaan', 'Operasional', 'Peminjaman']),
                    'penerima' => $faker->name(),
                    'lokasi_tujuan' => $item->lokasi,
                    'kondisi' => $faker->randomElement($kondisiList),
                    'catatan' => $faker->sentence(),
                    'user_id' => 1
                ]);
            }
        }
    }
}