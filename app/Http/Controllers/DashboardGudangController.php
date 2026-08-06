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


        // 1. Jumlah stok hampir habis
        $stokMinimum = DB::table('items')
            ->leftJoin('barang_masuks', function($join) use ($userId) {
                $join->on('items.kode_barang', '=', 'barang_masuks.kode_barang')
                     ->where('barang_masuks.user_id', $userId);
            })
            ->leftJoin('barang_keluars', function($join) use ($userId) {
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


        // 2. Barang kadaluarsa dalam 30 hari
        $kadaluarsa = DB::table('barang_masuks')
            ->join('items', 'barang_masuks.kode_barang', '=', 'items.kode_barang')
            ->where('barang_masuks.user_id', $userId)
            ->whereBetween('barang_masuks.tanggal_kadaluarsa', [$today, $today->copy()->addDays(30)])
            ->select('items.nama_barang', 'barang_masuks.tanggal_kadaluarsa')
            ->get();


        // 3. Transaksi Hari Ini
        $masukHariIni = DB::table('barang_masuks')
            ->where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->count();


        $keluarHariIni = DB::table('barang_keluars')
            ->where('user_id', $userId)
            ->whereDate('created_at', $today)
            ->count();


        // 4. 5 Barang Stok Terendah
        $stokTerendah = DB::table('barang_masuks')
            ->join('items', 'items.kode_barang', '=', 'barang_masuks.kode_barang')
            ->where('barang_masuks.user_id', $userId)
            ->select('items.nama_barang', DB::raw('SUM(barang_masuks.jumlah) as stok'))
            ->groupBy('items.kode_barang', 'items.nama_barang')
            ->orderBy('stok', 'asc')
            ->limit(5)
            ->get();


        // 5. Idle Stock (barang lama)
        $idleStock = DB::table('barang_masuks')
            ->join('items', 'barang_masuks.kode_barang', '=', 'items.kode_barang')
            ->join('lokasis', 'barang_masuks.id_lokasi', '=', 'lokasis.id')
            ->where('barang_masuks.user_id', $userId)
            ->select('items.nama_barang', 'lokasis.nama_lokasi', 'barang_masuks.jumlah', 'barang_masuks.tanggal_masuk')
            ->orderBy('barang_masuks.tanggal_masuk')
            ->limit(5)
            ->get();


        // 6. 5 Barang paling banyak masuk
        $topMasuk = DB::table('barang_masuks')
            ->join('items', 'barang_masuks.kode_barang', '=', 'items.kode_barang')
            ->where('barang_masuks.user_id', $userId)
            ->select(
                'items.nama_barang',
                DB::raw('SUM(jumlah) as total'),
                DB::raw('COUNT(barang_masuks.id) as frekuensi')
            )
            ->groupBy('barang_masuks.kode_barang', 'items.nama_barang')
            ->orderByDesc('total')
            ->limit(5)
            ->get();


        // 7. 5 Barang paling banyak keluar
        $topKeluar = DB::table('barang_keluars')
            ->join('items', 'barang_keluars.kode_barang', '=', 'items.kode_barang')
            ->where('barang_keluars.user_id', $userId)
            ->select(
                'items.nama_barang',
                DB::raw('SUM(jumlah_keluar) as total'),
                DB::raw('COUNT(barang_keluars.id) as frekuensi')
            )
            ->groupBy('barang_keluars.kode_barang', 'items.nama_barang')
            ->orderByDesc('total')
            ->limit(5)
            ->get();


        return view('dashboard.gudang', compact(
            'stokMinimum', 'kadaluarsa', 'masukHariIni', 'keluarHariIni',
            'stokTerendah', 'idleStock', 'topMasuk', 'topKeluar'
        ));
    }
}
