<?php


namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();


        // ✅ Data utama
        DB::table('kategoris')->insert([
            [
                'kategori' => 'Alat Tulis',
                'deskripsi' => 'Perlengkapan tulis-menulis seperti pulpen, buku, penghapus.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kategori' => 'Elektronik',
                'deskripsi' => 'Barang elektronik seperti laptop, printer, dan kabel.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'kategori' => 'Kebersihan',
                'deskripsi' => 'Alat dan bahan kebersihan kantor seperti pel, sabun, dan tisu.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_kategori' => 'Makanan',
                'deskripsi' => 'Stok makanan ringan dan kebutuhan pantry.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);


        // ✅ Data dummy tambahan
        for ($i = 1; $i <= 15; $i++) {
            DB::table('kategoris')->insert([
                'kategori' => 'Dummy ' . $i,
                'deskripsi' => 'Deskripsi kategori dummy ke-' . $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
