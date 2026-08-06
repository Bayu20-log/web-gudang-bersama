<?php


namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class KondisiSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();


        // ✅ Data utama
        DB::table('kondisis')->insert([
            [
                'nama_kondisi' => 'Baik',
                'deskripsi' => 'Barang masih sangat bagus',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_kondisi' => 'Rusak Ringan',
                'deskripsi' => 'Hanya rusak kecil, masih bisa dipakai',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_kondisi' => 'Rusak Berat',
                'deskripsi' => 'Sudah tidak dapat digunakan lagi',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);


        // ✅ Data dummy tambahan
        for ($i = 1; $i <= 15; $i++) {
            DB::table('kondisis')->insert([
                'nama_kondisi' => 'Dummy ' . $i,
                'deskripsi' => 'Deskripsi kondisi dummy ke-' . $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
