<?php


namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class LokasiSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();


        // Data utama
        DB::table('lokasis')->insert([
            [
                'nama_lokasi' => 'Gudang Utama',
                'deskripsi' => 'Pusat penyimpanan utama',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_lokasi' => 'Rak A',
                'deskripsi' => 'Rak di sisi kiri',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_lokasi' => 'Rak B',
                'deskripsi' => 'Rak di sisi kanan',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nama_lokasi' => 'Lantai 2',
                'deskripsi' => 'Area penyimpanan atas',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);


        // Data dummy (opsional)
        for ($i = 1; $i <= 15; $i++) {
            DB::table('lokasis')->insert([
                'nama_lokasi' => 'Dummy ' . $i,
                'deskripsi' => 'Lokasi dummy ke-' . $i,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
