<?php

namespace App\Exports;

use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LaporanAsetExport implements FromCollection, WithHeadings
{
    /**
     * Berapa hari sebelum tanggal kadaluarsa sebuah batch sudah dianggap "Segera Kadaluarsa".
     * Ubah angka ini kalau ingin batas yang berbeda.
     */
    public const BATAS_SEGERA_HARI = 30;

    /** Tiga indikator kondisi di Laporan Aset (urut dari paling aman). */
    public const DAFTAR_KONDISI = ['Baik', 'Segera Kadaluarsa', 'Kadaluarsa'];

    protected $request;

    public function __construct($request = null)
    {
        $this->request = $request;
    }

    private function parseDate($date, $startOfDay = true)
    {
        if (!$date) return null;

        try {
            $parsed = Carbon::parse($date);
            return $startOfDay ? $parsed->startOfDay() : $parsed->endOfDay();
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * RUMUS KONDISI ASET (dihitung dari tanggal kadaluarsa yang diisi saat barang masuk):
     *  - Kadaluarsa        : tanggal kadaluarsa sudah lewat (sebelum hari ini)
     *  - Segera Kadaluarsa : kadaluarsa hari ini sampai BATAS_SEGERA_HARI hari ke depan
     *  - Baik              : selebihnya
     */
    public static function statusKadaluarsa($tanggalKadaluarsa): string
    {
        if (!$tanggalKadaluarsa) {
            return 'Baik';
        }

        try {
            $kadaluarsa = Carbon::parse($tanggalKadaluarsa)->startOfDay();
        } catch (\Exception $e) {
            return 'Baik';
        }

        $hariIni = Carbon::today();

        if ($kadaluarsa->lt($hariIni)) {
            return 'Kadaluarsa';
        }

        if ($kadaluarsa->lte($hariIni->copy()->addDays(self::BATAS_SEGERA_HARI))) {
            return 'Segera Kadaluarsa';
        }

        return 'Baik';
    }

    /**
     * Hitung aset per (nama barang + lokasi + kondisi kadaluarsa).
     *
     * Cara hitung:
     *  1. Setiap barang masuk dianggap satu "batch" dengan tanggal kadaluarsanya sendiri.
     *  2. Barang keluar di lokasi yang sama memotong stok batch yang PALING CEPAT kadaluarsa lebih dulu.
     *  3. Sisa tiap batch dikelompokkan sesuai status kadaluarsanya (Baik / Segera Kadaluarsa / Kadaluarsa).
     *  4. Nilai aset = sisa stok x harga beli batch tersebut.
     *
     * Hanya baris dengan stok > 0 yang dikembalikan.
     */
    public static function hitungAset($userId, $startDate = null, $endDate = null): Collection
    {
        $items = Item::with(['barangMasuk.lokasi', 'barangKeluar.lokasi'])
            ->whereHas('barangMasuk', function ($q) use ($userId, $startDate, $endDate) {
                $q->where('user_id', $userId);

                if ($startDate && $endDate) {
                    $q->whereBetween('tanggal_masuk', [$startDate, $endDate]);
                }
            })
            ->get();

        $baris = [];

        foreach ($items as $item) {
            $masuk = $item->barangMasuk;
            $keluar = $item->barangKeluar;

            if ($startDate && $endDate) {
                $masuk = $masuk->filter(function ($bm) use ($startDate, $endDate) {
                    return Carbon::parse($bm->tanggal_masuk)->between($startDate, $endDate);
                });
                $keluar = $keluar->filter(function ($bk) use ($startDate, $endDate) {
                    return Carbon::parse($bk->tanggal_keluar)->between($startDate, $endDate);
                });
            }

            $masukPerLokasi = $masuk->groupBy(function ($bm) {
                return $bm->lokasi->nama_lokasi ?? '-';
            });

            foreach ($masukPerLokasi as $lokasi => $batches) {
                $lokasi = (string) $lokasi; // jaga-jaga kalau nama lokasi berupa angka
                $sisaKeluar = (int) $keluar
                    ->filter(function ($bk) use ($lokasi) {
                        return ($bk->lokasi->nama_lokasi ?? '-') === $lokasi;
                    })
                    ->sum('jumlah_keluar');

                // Batch yang paling cepat kadaluarsa dipakai lebih dulu
                $urut = $batches->sortBy(function ($bm) {
                    return [(string) $bm->tanggal_kadaluarsa, (string) $bm->tanggal_masuk];
                });

                foreach ($urut as $bm) {
                    $jumlah = (int) $bm->jumlah;
                    $diambil = min($jumlah, $sisaKeluar);
                    $sisaKeluar -= $diambil;
                    $sisa = $jumlah - $diambil;

                    if ($sisa <= 0) {
                        continue;
                    }

                    $status = self::statusKadaluarsa($bm->tanggal_kadaluarsa);
                    $kunci = $item->nama_barang . '|' . $lokasi . '|' . $status;

                    if (!isset($baris[$kunci])) {
                        $baris[$kunci] = [
                            'nama_barang' => $item->nama_barang,
                            'lokasi'      => $lokasi,
                            'kondisi'     => $status,
                            'stok_akhir'  => 0,
                            'nilai'       => 0,
                        ];
                    }

                    $baris[$kunci]['stok_akhir'] += $sisa;
                    $baris[$kunci]['nilai'] += $sisa * (float) $bm->harga_satuan;
                }
            }
        }

        return collect($baris)->map(function ($row) {
            return [
                'nama_barang' => $row['nama_barang'],
                'lokasi'      => $row['lokasi'],
                'kondisi'     => $row['kondisi'],
                'stok_akhir'  => $row['stok_akhir'],
                'harga_beli'  => round($row['nilai'] / $row['stok_akhir'], 2),
                'jumlah_aset' => $row['nilai'],
            ];
        })->values();
    }

    public function collection()
    {
        $userId = auth()->id();

        $startDate = $this->parseDate($this->request->tanggal_mulai, true);
        $endDate   = $this->parseDate($this->request->tanggal_selesai, false);

        $grouped = self::hitungAset($userId, $startDate, $endDate);

        // Terapkan filter dari request
        $filtered = $grouped->filter(function ($item) {
            $req = $this->request;
            return $item['stok_akhir'] > 0 &&
                (!$req->lokasi || $item['lokasi'] == $req->lokasi) &&
                (!$req->kondisi || $item['kondisi'] == $req->kondisi) &&
                (!$req->nama_barang || stripos($item['nama_barang'], $req->nama_barang) !== false);
        })->values();

        // Tambah baris total
        $total = $filtered->sum('jumlah_aset');
        $filtered->push([
            'nama_barang' => '',
            'lokasi' => '',
            'kondisi' => '',
            'stok_akhir' => '',
            'harga_beli' => 'Total Aset',
            'jumlah_aset' => $total,
        ]);

        return $filtered;
    }

    public function headings(): array
    {
        return [
            'Nama Barang',
            'Lokasi',
            'Kondisi',
            'Stok Akhir',
            'Harga Beli',
            'Jumlah Aset',
        ];
    }
}
