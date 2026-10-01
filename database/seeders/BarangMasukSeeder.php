<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BarangMasuk;
use App\Models\Item;
use App\Models\Lokasi;
use App\Models\Kondisi;
use App\Models\Pemasok;
use Carbon\Carbon;

/**
 * Seed barang_masuks dari kolom "Masuk" di sheet Log Input (Log_Stok_RAKSAKTI.xlsx).
 * Data historis 1-7 Februari 2026 (7 hari), hanya baris dengan jumlah masuk > 0.
 *
 * Wajib dijalankan SETELAH ItemSeeder, LokasiSeeder, KondisiSeeder, PemasokSeeder.
 *
 * CATATAN: id_pemasok TIDAK ADA di Excel -> dipilih random dari 4 pemasok dummy.
 */
class BarangMasukSeeder extends Seeder
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

        $lokasi = Lokasi::where('nama_lokasi', 'Stok Bar')->where('user_id', $this->userId)->first();
        $kondisi = Kondisi::where('nama_kondisi', 'Baik')->where('user_id', $this->userId)->first();
        $pemasokIds = Pemasok::where('user_id', $this->userId)->pluck('id')->all();

        if (!$lokasi || !$kondisi || empty($pemasokIds)) {
            $this->command->error('Lokasi/Kondisi/Pemasok belum ada. Jalankan LokasiSeeder, KondisiSeeder, PemasokSeeder dulu.');
            return;
        }

        foreach ($data['logs'] as $log) {
            if ($log['masuk'] <= 0) {
                continue;
            }

            $item = Item::where('nama_barang', $log['nama_barang'])->where('user_id', $this->userId)->first();
            if (!$item) {
                continue; // item tidak ditemukan, lewati
            }

            $tanggal = Carbon::parse($log['tanggal'])->setTime(9, 0);
            $hargaSatuan = $item->harga_dasar;

            BarangMasuk::create([
                'kode_barang'        => $item->kode_barang,
                'jumlah'             => $log['masuk'],
                'harga_satuan'       => $hargaSatuan,
                'total_harga'        => $hargaSatuan * $log['masuk'],
                'tanggal_masuk'      => $tanggal,
                'tanggal_kadaluarsa' => $tanggal->copy()->addDays(90),
                'id_pemasok'         => $pemasokIds[array_rand($pemasokIds)],
                'id_lokasi'          => $lokasi->id,
                'id_kondisi'         => $kondisi->id,
                'user_id'            => $this->userId,
                'catatan'            => 'Import otomatis dari Log Stok RAKSAKTI',
            ]);
        }
    }
}