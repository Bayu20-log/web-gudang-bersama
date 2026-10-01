<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Kategori;
use App\Models\Satuan;

/**
 * Seed 42 item dari sheet "Master Item" di Log_Stok_RAKSAKTI.xlsx.
 * Wajib dijalankan SETELAH KategoriSeeder & SatuanSeeder (butuh id_kategori & id_satuan).
 *
 * harga_dasar TIDAK ADA di Excel -> di-generate dummy tapi realistis per kategori/satuan
 * (sudah dihitung sebelumnya & disimpan di data/raksakti_seed_data.json).
 */
class ItemSeeder extends Seeder
{
    protected int $userId = 2;

    public function run(): void
    {
        $jsonPath = database_path('seeders/data/raksakti_seed_data.json');

        if (!file_exists($jsonPath)) {
            $this->command->error("File data tidak ditemukan: {$jsonPath}");
            return;
        }

        $data = json_decode(file_get_contents($jsonPath), true);

        foreach ($data['items'] as $it) {
            $kategori = Kategori::where('kategori', $it['kategori'])->where('user_id', $this->userId)->first();
            $satuan   = Satuan::where('nama_satuan', $it['satuan'])->where('user_id', $this->userId)->first();

            if (!$kategori || !$satuan) {
                $this->command->warn("Lewati item '{$it['nama_barang']}': kategori/satuan tidak ditemukan. Jalankan KategoriSeeder & SatuanSeeder dulu.");
                continue;
            }

            Item::firstOrCreate(
                ['nama_barang' => $it['nama_barang']], // nama_barang unique secara global di tabel items
                [
                    'user_id'      => $this->userId,
                    'harga_dasar'  => $it['harga_dasar'],
                    'id_kategori'  => $kategori->id,
                    'id_satuan'    => $satuan->id,
                    'stok_minimum' => $it['stok_minimum'],
                    'deskripsi'    => null,
                    'foto'         => null,
                ]
            );
        }
    }
}