<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BarangKeluar;
use App\Models\Item;
use App\Models\Lokasi;
use App\Models\Kondisi;
use Carbon\Carbon;

/**
 * Seed barang_keluars dari kolom "Keluar" di sheet Log Input (Log_Stok_RAKSAKTI.xlsx).
 * Data historis 1-7 Februari 2026 (7 hari), hanya baris dengan jumlah keluar > 0.
 *
 * Wajib dijalankan SETELAH ItemSeeder, LokasiSeeder, KondisiSeeder.
 *
 * CATATAN: harga_jual, penerima, lokasi_tujuan TIDAK ADA di Excel -> harga_jual
 * dihitung dari harga_dasar item + markup 30%, penerima/lokasi_tujuan digeneralisasi
 * sebagai konsumsi langsung di bar (karena ini konteks bar minuman, bukan distribusi antar gudang).
 */
class BarangKeluarSeeder extends Seeder
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

        if (!$lokasi || !$kondisi) {
            $this->command->error('Lokasi/Kondisi belum ada. Jalankan LokasiSeeder & KondisiSeeder dulu.');
            return;
        }

        foreach ($data['logs'] as $log) {
            if ($log['keluar'] <= 0) {
                continue;
            }

            $item = Item::where('nama_barang', $log['nama_barang'])->where('user_id', $this->userId)->first();
            if (!$item) {
                continue; // item tidak ditemukan, lewati
            }

            $tanggal = Carbon::parse($log['tanggal'])->setTime(17, 0);
            $hargaJual = (int) round($item->harga_dasar * 1.3); // markup 30%

            BarangKeluar::create([
                'user_id'          => $this->userId,
                'kode_barang'      => $item->kode_barang,
                'id_lokasi'        => $lokasi->id,
                'tanggal_keluar'   => $tanggal,
                'jumlah_keluar'    => $log['keluar'],
                'harga_jual'       => $hargaJual,
                'total_harga_jual' => $hargaJual * $log['keluar'],
                'penerima'         => 'Konsumen Bar',
                'lokasi_tujuan'    => 'Konsumsi Langsung',
                'id_kondisi'       => $kondisi->id,
                'catatan'          => 'Import otomatis dari Log Stok RAKSAKTI',
            ]);
        }
    }
}