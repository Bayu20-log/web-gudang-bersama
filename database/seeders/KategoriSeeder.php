<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;

class KategoriSeeder extends Seeder
{
    protected int $userId = 2;

    public function run(): void
    {
        $kategoris = ['12oz', '16oz', 'Syrup & Bahan Lainnya', 'Dessert'];

        foreach ($kategoris as $nama) {
            Kategori::create([
                'user_id'   => $this->userId,
                'kategori'  => $nama,
                'deskripsi' => "Kategori {$nama} (hasil import dari Log Stok RAKSAKTI)",
            ]);
        }
    }
}