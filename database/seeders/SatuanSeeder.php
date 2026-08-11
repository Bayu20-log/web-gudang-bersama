<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Satuan;

class SatuanSeeder extends Seeder
{
    protected int $userId = 2;

    public function run(): void
    {
        $satuans = ['Gram', 'Ml'];

        foreach ($satuans as $nama) {
            Satuan::create([
                'user_id'     => $this->userId,
                'nama_satuan' => $nama,
            ]);
        }
    }
}