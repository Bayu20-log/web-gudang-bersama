<?php


namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class SatuanSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();


        DB::table('satuans')->insert([
            ['nama_satuan' => 'pcs', 'created_at' => $now, 'updated_at' => $now],
            ['nama_satuan' => 'box', 'created_at' => $now, 'updated_at' => $now],
            ['nama_satuan' => 'liter', 'created_at' => $now, 'updated_at' => $now],
            ['nama_satuan' => 'kg', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}
