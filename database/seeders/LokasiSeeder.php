<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lokasi;

class LokasiSeeder extends Seeder
{
    protected int $userId = 2;

    public function run(): void
    {
        Lokasi::create([
            'user_id'     => $this->userId,
            'nama_lokasi' => 'Stok Bar',
            'deskripsi'   => 'Lokasi penyimpanan stok bar utama',
        ]);
    }
}