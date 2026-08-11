<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kondisi;

class KondisiSeeder extends Seeder
{
    protected int $userId = 2;

    public function run(): void
    {
        Kondisi::create([
            'user_id'      => $this->userId,
            'nama_kondisi' => 'Baik',
            'deskripsi'    => 'Kondisi barang layak pakai',
        ]);
    }
}