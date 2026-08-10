<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardGudangController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $today = Carbon::today();

        // 1. Stok kritis (di bawah/sama dengan minimum) — dipakai utk Prioritas Tindakan
        $stokMinimum = DB::table('items')
            ->leftJoin('barang_masuks', function ($join) use ($userId) {
                $join->on('items.kode_barang', '=', 'barang_masuks.kode_barang')
                     ->where('barang_masuks.user_id', $userId);
            })
            ->leftJoin('barang_keluars', function ($join) use ($userId) {
                $join->on('items.kode_barang', '=', 'barang_keluars.kode_barang')
                     ->where('barang_keluars.user_id', $userId);
            })
            ->select(
                'items.nama_barang',
                'items.stok_minimum',
                DB::raw('COALESCE(SUM(barang_masuks.jumlah), 0) - COALESCE(SUM(barang_keluars.jumlah_keluar), 0) AS total_stok')
            )
            ->groupBy('items.kode_barang', 'items.nama_barang', 'items.stok_minimum')
            ->havingRaw('total_stok <= stok_minimum')
            ->whereNotNull('barang_masuks.id')
            ->get();

        // 2. Segera Kadaluarsa (dalam 30 hari) — tambah jumlah & id utk ditampilkan di card
        $kadaluarsa = DB::table('barang_masuks')
            ->join('items', 'barang_masuks.kode_barang', '=', 'items.kode_barang')
            ->where('barang_masuks.user_id', $userId)
            ->whereBetween('barang_masuks.tanggal_kadaluarsa', [$today, $today->copy()->addDays(30)])
            ->select('items.nama_barang', 'barang_masuks.tanggal_kadaluarsa', 'barang_masuks.jumlah', 'barang_masuks.id')
            ->orderBy('barang_masuks.tanggal_kadaluarsa')
            ->limit(5)
            ->get();

        // 3. 5 Barang Stok Terendah — SEKARANG termasuk stok_minimum, dipakai utk badge status
        $stokTerendah = DB::table('items')
            ->leftJoin('barang_masuks', function ($join) use ($userId) {
                $join->on('items.kode_barang', '=', 'barang_masuks.kode_barang')
                     ->where('barang_masuks.user_id', $userId);
            })
            ->leftJoin('barang_keluars', function ($join) use ($userId) {
                $join->on('items.kode_barang', '=', 'barang_keluars.kode_barang')
                     ->where('barang_keluars.user_id', $userId);
            })
            ->select(
                'items.nama_barang',
                'items.stok_minimum',
                DB::raw('COALESCE(SUM(barang_masuks.jumlah), 0) - COALESCE(SUM(barang_keluars.jumlah_keluar), 0) AS stok')
            )
            ->groupBy('items.kode_barang', 'items.nama_barang', 'items.stok_minimum')
            ->whereNotNull('barang_masuks.id')
            ->orderBy('stok', 'asc')
            ->limit(5)
            ->get();

        // 4. Idle Stock (barang lama tidak bergerak)
        $idleStock = DB::table('barang_masuks')
            ->join('items', 'barang_masuks.kode_barang', '=', 'items.kode_barang')
            ->join('lokasis', 'barang_masuks.id_lokasi', '=', 'lokasis.id')
            ->where('barang_masuks.user_id', $userId)
            ->select('items.nama_barang', 'lokasis.nama_lokasi', 'barang_masuks.jumlah', 'barang_masuks.tanggal_masuk')
            ->orderBy('barang_masuks.tanggal_masuk')
            ->limit(5)
            ->get();

        // 5. Top 5 Barang Paling Banyak Masuk (dipertahankan, fitur tambahan di luar mockup)
        $topMasuk = DB::table('barang_masuks')
            ->join('items', 'barang_masuks.kode_barang', '=', 'items.kode_barang')
            ->where('barang_masuks.user_id', $userId)
            ->select('items.nama_barang', DB::raw('SUM(jumlah) as total'), DB::raw('COUNT(barang_masuks.id) as frekuensi'))
            ->groupBy('barang_masuks.kode_barang', 'items.nama_barang')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // 6. Top 5 Barang Keluar — dipakai utk bar chart (metrik: frekuensi transaksi, sesuai mockup)
        $topKeluar = DB::table('barang_keluars')
            ->join('items', 'barang_keluars.kode_barang', '=', 'items.kode_barang')
            ->where('barang_keluars.user_id', $userId)
            ->select('items.nama_barang', DB::raw('SUM(jumlah_keluar) as total'), DB::raw('COUNT(barang_keluars.id) as frekuensi'))
            ->groupBy('barang_keluars.kode_barang', 'items.nama_barang')
            ->orderByDesc('frekuensi')
            ->limit(5)
            ->get();

        // 7. Tren transaksi 7 hari terakhir (line chart) + total periode (dipakai utk KPI card)
        $trenTransaksi = collect(range(6, 0))->map(function ($i) use ($userId, $today) {
            $tanggal = $today->copy()->subDays($i);

            return [
                'label' => $tanggal->translatedFormat('D'),
                'masuk' => DB::table('barang_masuks')->where('user_id', $userId)->whereDate('created_at', $tanggal)->count(),
                'keluar' => DB::table('barang_keluars')->where('user_id', $userId)->whereDate('created_at', $tanggal)->count(),
            ];
        });

        $kpi = [
            'total_masuk' => $trenTransaksi->sum('masuk'),
            'total_keluar' => $trenTransaksi->sum('keluar'),
            'periode' => '7 Hari Terakhir',
        ];

        // 8. Prioritas Tindakan — gabungan Kritis + Segera Kadaluarsa + Idle, diurutkan urgensi
        $prioritasTindakan = collect();

        foreach ($stokMinimum as $item) {
            $prioritasTindakan->push([
                'nama' => $item->nama_barang,
                'tag' => 'Kritis',
                'keterangan' => "Stok {$item->total_stok} / Minimum {$item->stok_minimum}",
                'urgensi' => 1,
            ]);
        }

        foreach ($kadaluarsa as $item) {
            $sisaHari = max($today->diffInDays(Carbon::parse($item->tanggal_kadaluarsa), false), 0);
            $prioritasTindakan->push([
                'nama' => $item->nama_barang,
                'tag' => 'Segera Kadaluarsa',
                'keterangan' => "{$sisaHari} hari lagi · Qty {$item->jumlah}",
                'urgensi' => 2,
            ]);
        }

        foreach ($idleStock as $item) {
            $hariIdle = Carbon::parse($item->tanggal_masuk)->diffInDays($today);
            $prioritasTindakan->push([
                'nama' => $item->nama_barang,
                'tag' => 'Idle',
                'keterangan' => "{$hariIdle} hari tidak bergerak",
                'urgensi' => 3,
            ]);
        }

        $prioritasTindakan = $prioritasTindakan->sortBy('urgensi')->take(6)->values();

        return view('dashboard.gudang', compact(
            'kpi', 'trenTransaksi', 'topKeluar', 'topMasuk',
            'stokTerendah', 'kadaluarsa', 'prioritasTindakan', 'idleStock'
        ));
    }
}