<?php

namespace App\Services;

use App\Exports\LaporanAsetExport;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Pemantauan tanggal kadaluarsa barang (notifikasi kadaluarsa).
 *
 * Aturan status kadaluarsa memakai fungsi yang SAMA dengan Laporan Aset
 * (LaporanAsetExport::statusKadaluarsa, batas 30 hari), supaya angka di
 * Notifikasi dan Laporan Aset konsisten.
 *
 * Sisa stok tiap batch dihitung dengan cara yang sama seperti Laporan Aset:
 * barang keluar di lokasi yang sama memotong batch yang paling cepat
 * kadaluarsa lebih dulu (FEFO). Batch yang sisanya 0 tidak diingatkan.
 */
class ExpiryMonitoringService
{
    /**
     * Cek kadaluarsa semua barang milik satu user, lalu perbarui tabel
     * expiry_notifications: buat notifikasi baru untuk batch yang baru masuk
     * status Segera Kadaluarsa / Kadaluarsa, dan tandai selesai (is_resolved)
     * notifikasi yang batch-nya sudah habis atau statusnya berpindah.
     *
     * @return array{baru: int, aktif: int}
     */
    public function cekKadaluarsa(int $userId): array
    {
        $items = Item::with(['barangMasuk.lokasi', 'barangKeluar.lokasi'])
            ->where('user_id', $userId)
            ->get();

        $aktif = [];

        foreach ($items as $item) {
            $masukPerLokasi = $item->barangMasuk->groupBy(fn ($bm) => (string) ($bm->lokasi->nama_lokasi ?? '-'));

            foreach ($masukPerLokasi as $lokasi => $batches) {
                $lokasi = (string) $lokasi;
                $sisaKeluar = (int) $item->barangKeluar
                    ->filter(fn ($bk) => (string) ($bk->lokasi->nama_lokasi ?? '-') === $lokasi)
                    ->sum('jumlah_keluar');

                // FEFO: batch yang paling cepat kadaluarsa dipakai lebih dulu
                $urut = $batches->sortBy(fn ($bm) => [(string) $bm->tanggal_kadaluarsa, (string) $bm->tanggal_masuk]);

                foreach ($urut as $bm) {
                    $jumlah = (int) $bm->jumlah;
                    $diambil = min($jumlah, $sisaKeluar);
                    $sisaKeluar -= $diambil;
                    $sisa = $jumlah - $diambil;

                    if ($sisa <= 0 || !$bm->tanggal_kadaluarsa) {
                        continue;
                    }

                    $status = match (LaporanAsetExport::statusKadaluarsa($bm->tanggal_kadaluarsa)) {
                        'Kadaluarsa'        => 'kadaluarsa',
                        'Segera Kadaluarsa' => 'segera',
                        default             => null,
                    };

                    if ($status === null) {
                        continue;
                    }

                    $aktif[$bm->id . '|' . $status] = [
                        'item_id'            => $item->kode_barang,
                        'barang_masuk_id'    => $bm->id,
                        'status'             => $status,
                        'tanggal_kadaluarsa' => $bm->tanggal_kadaluarsa,
                        'lokasi'             => $lokasi,
                        'jumlah_sisa'        => $sisa,
                    ];
                }
            }
        }

        $baru = 0;
        $now = now();
        $idMasihAktif = [];

        foreach ($aktif as $data) {
            $existing = DB::table('expiry_notifications')
                ->where('barang_masuk_id', $data['barang_masuk_id'])
                ->where('status', $data['status'])
                ->first();

            if ($existing) {
                DB::table('expiry_notifications')->where('id', $existing->id)->update([
                    'jumlah_sisa' => $data['jumlah_sisa'],
                    'lokasi'      => $data['lokasi'],
                    'is_resolved' => false,
                    'updated_at'  => $now,
                ]);
                $idMasihAktif[] = $existing->id;
            } else {
                $idMasihAktif[] = DB::table('expiry_notifications')->insertGetId($data + [
                    'is_read'     => false,
                    'is_resolved' => false,
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
                $baru++;
            }
        }

        // Notifikasi yang tidak berlaku lagi (batch habis terpakai, atau status
        // pindah dari "segera" ke "kadaluarsa") ditandai selesai.
        DB::table('expiry_notifications')
            ->whereIn('item_id', $items->pluck('kode_barang')->all())
            ->where('is_resolved', false)
            ->whereNotIn('id', $idMasihAktif)
            ->update(['is_resolved' => true, 'updated_at' => $now]);

        return ['baru' => $baru, 'aktif' => count($aktif)];
    }

    /**
     * Sama dengan cekKadaluarsa(), tapi paling banyak SEKALI per hari per user.
     * Dipakai di navbar (lonceng) sebagai pengganti scheduler di server yang
     * tidak mendukung cron, supaya notifikasi kadaluarsa tetap muncul saat hari berganti.
     */
    public function cekHarian(int $userId): void
    {
        $kunci = 'cek_kadaluarsa_' . $userId . '_' . Carbon::today()->toDateString();

        if (Cache::add($kunci, true, Carbon::tomorrow())) {
            $this->cekKadaluarsa($userId);
        }
    }

    /**
     * Daftar notifikasi kadaluarsa yang masih berlaku, dikelompokkan PER BARANG
     * (satu kartu per barang), diurutkan: belum dibaca, sudah kadaluarsa,
     * lalu tanggal terdekat.
     */
    public function daftarAktifPerBarang(int $userId)
    {
        $rows = DB::table('expiry_notifications')
            ->join('items', 'items.kode_barang', '=', 'expiry_notifications.item_id')
            ->where('items.user_id', $userId)
            ->where('expiry_notifications.is_resolved', false)
            ->select('expiry_notifications.*', 'items.nama_barang')
            ->get();

        return $rows->groupBy('item_id')->map(function ($grup) {
            $kadaluarsa = $grup->where('status', 'kadaluarsa');
            $segera     = $grup->where('status', 'segera');
            $utama      = $kadaluarsa->isNotEmpty() ? 'kadaluarsa' : 'segera';
            $terdekat   = ($utama === 'kadaluarsa' ? $kadaluarsa : $segera)->sortBy('tanggal_kadaluarsa')->first();

            return (object) [
                'item_id'            => $terdekat->item_id,
                'nama_barang'        => $terdekat->nama_barang,
                'status'             => $utama,
                'jumlah_kadaluarsa'  => (int) $kadaluarsa->sum('jumlah_sisa'),
                'jumlah_segera'      => (int) $segera->sum('jumlah_sisa'),
                'tanggal_kadaluarsa' => $terdekat->tanggal_kadaluarsa,
                'lokasi'             => $terdekat->lokasi,
                'is_read'            => $grup->every(fn ($r) => (bool) $r->is_read),
                'created_at'         => $grup->max('created_at'),
            ];
        })->sort(function ($a, $b) {
            return [$a->is_read, $a->status === 'kadaluarsa' ? 0 : 1, $a->tanggal_kadaluarsa]
               <=> [$b->is_read, $b->status === 'kadaluarsa' ? 0 : 1, $b->tanggal_kadaluarsa];
        })->values();
    }

    /** Jumlah barang yang punya notifikasi kadaluarsa aktif & belum dibaca (untuk lonceng). */
    public function jumlahBelumDibaca(int $userId): int
    {
        return DB::table('expiry_notifications')
            ->join('items', 'items.kode_barang', '=', 'expiry_notifications.item_id')
            ->where('items.user_id', $userId)
            ->where('expiry_notifications.is_resolved', false)
            ->where('expiry_notifications.is_read', false)
            ->distinct()
            ->count('expiry_notifications.item_id');
    }
}
