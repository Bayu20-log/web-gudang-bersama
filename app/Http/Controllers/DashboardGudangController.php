<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardGudangController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        if ($request->filled('date_from') && $request->filled('date_to')) {
            $dateFrom = Carbon::parse($request->input('date_from'))->startOfDay();
            $dateTo   = Carbon::parse($request->input('date_to'))->endOfDay();
        } else {
            $lastMasuk = DB::table('barang_masuks')->max('tanggal_masuk');

            if ($lastMasuk) {
                $latestCarbon = Carbon::parse($lastMasuk);
                $dateFrom     = $latestCarbon->copy()->startOfMonth()->startOfDay();
                $dateTo       = $latestCarbon->copy()->endOfMonth()->endOfDay();
            } else {
                $dateFrom = Carbon::now()->startOfMonth()->startOfDay();
                $dateTo   = Carbon::now()->endOfDay();
            }
        }
        
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

        // 2. Segera Kadaluarsa (dalam 30 hari)
        $sortKadaluarsa = strtolower($request->input('sort_kadaluarsa', 'asc')) === 'desc' ? 'desc' : 'asc';

        $kadaluarsa = DB::table('barang_masuks')
            ->leftJoin('items', 'barang_masuks.kode_barang', '=', 'items.kode_barang')
            ->where('barang_masuks.user_id', '=', $userId)
            ->whereNotNull('barang_masuks.tanggal_kadaluarsa')
            ->select(
                'barang_masuks.id',
                'barang_masuks.kode_barang',
                DB::raw('COALESCE(items.nama_barang, barang_masuks.kode_barang) as nama_barang'),
                'barang_masuks.jumlah',
                'barang_masuks.tanggal_kadaluarsa'
            )
            ->orderBy('barang_masuks.tanggal_kadaluarsa', $sortKadaluarsa)
            ->paginate(5, ['*'], 'kadaluarsa_page')
            ->withQueryString();

        // 3. 5 Barang Stok Terendah
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
        $idleStock = $this->getIdleStockData($request);

        // 5. Top 5 Barang Paling Banyak Masuk
        $topMasuk = DB::table('barang_masuks')
            ->join('items', 'barang_masuks.kode_barang', '=', 'items.kode_barang')
            ->where('barang_masuks.user_id', $userId)
            ->select('items.nama_barang', DB::raw('SUM(jumlah) as total'), DB::raw('COUNT(barang_masuks.id) as frekuensi'))
            ->groupBy('barang_masuks.kode_barang', 'items.nama_barang')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // 6. Top 5 Barang Keluar
        $topKeluar = DB::table('barang_keluars')
            ->join('items', 'barang_keluars.kode_barang', '=', 'items.kode_barang')
            ->where('barang_keluars.user_id', $userId)
            ->whereBetween('barang_keluars.tanggal_keluar', [$dateFrom, $dateTo])
            ->select('items.nama_barang', DB::raw('SUM(jumlah_keluar) as total'), DB::raw('COUNT(barang_keluars.id) as frekuensi'))
            ->groupBy('barang_keluars.kode_barang', 'items.nama_barang')
            ->orderByDesc('frekuensi')
            ->limit(5)
            ->get();

        // 7. Tren Transaksi berdasarkan filter tanggal
        $totalMasukFilter = DB::table('barang_masuks')
            ->where('user_id', $userId)
            ->whereBetween('tanggal_masuk', [$dateFrom, $dateTo])
            ->sum('jumlah');

        $totalKeluarFilter = DB::table('barang_keluars')
            ->where('user_id', $userId)
            ->whereBetween('tanggal_keluar', [$dateFrom, $dateTo])
            ->sum('jumlah_keluar');

        $masukHarian = DB::table('barang_masuks')
            ->where('user_id', $userId)
            ->whereBetween('tanggal_masuk', [$dateFrom, $dateTo])
            ->select(DB::raw('DATE(tanggal_masuk) as tgl'), DB::raw('SUM(jumlah) as total'))
            ->groupBy('tgl')
            ->pluck('total', 'tgl');

        $keluarHarian = DB::table('barang_keluars')
            ->where('user_id', $userId)
            ->whereBetween('tanggal_keluar', [$dateFrom, $dateTo])
            ->select(DB::raw('DATE(tanggal_keluar) as tgl'), DB::raw('SUM(jumlah_keluar) as total'))
            ->groupBy('tgl')
            ->pluck('total', 'tgl');

        $period = CarbonPeriod::create($dateFrom, $dateTo);
        $trenTransaksi = collect();

        foreach ($period as $date) {
        $tglStr = $date->format('Y-m-d');
        $trenTransaksi->push([
            'label'  => $date->format('d M'),
            'masuk'  => (int) ($masukHarian[$tglStr] ?? 0),
            'keluar' => (int) ($keluarHarian[$tglStr] ?? 0),
        ]);

        }

        $kpi = [
            'total_masuk'   => $totalMasukFilter,
            'total_keluar'  => $totalKeluarFilter,
            'periode'      => $dateFrom->format('d M Y') . ' - ' . $dateTo->format('d M Y'),        
        ];

        // 8. Prioritas Tindakan
        $dateFrom = $request->input('date_from', date('Y-m-01'));
        $dateTo = $request->input('date_to', date('Y-m-d'));
        $filterStatus = strtolower($request->input('filter_status', 'semua'));

        $allItems = \App\Models\Item::all();

        $barangMasuk = \DB::table('barang_masuks') 
            ->select('kode_barang', \DB::raw('SUM(jumlah) as total_masuk'))
            ->whereBetween('tanggal_masuk', [$dateFrom, $dateTo])
            ->groupBy('kode_barang')
            ->pluck('total_masuk', 'kode_barang');

        $barangKeluar = \DB::table('barang_keluars') 
            ->select('kode_barang', \DB::raw('SUM(jumlah_keluar) as total_keluar'))
            ->whereBetween('tanggal_keluar', [$dateFrom, $dateTo])
            ->groupBy('kode_barang')
            ->pluck('total_keluar', 'kode_barang');

        $dataPrioritas = $allItems->map(function ($item) use ($barangMasuk, $barangKeluar) {
            $kode = $item->kode_barang;
        
            $masuk = $barangMasuk[$kode] ?? 0;
            $keluar = $barangKeluar[$kode] ?? 0;

            $stokAkhir = $masuk - $keluar;
            if ($stokAkhir < 0) {
                $stokAkhir = 0; 
            }

            $minStok = $item->stok_minimum ?? 5;

            // Klasifikasi Status Stok
            if ($stokAkhir == 0) {
                $tag = 'Habis';
                $urgensi = 1;
            } elseif ($stokAkhir <= $minStok) {
                $tag = 'Kritis';
                $urgensi = 2;
            } elseif ($stokAkhir <= ($minStok * 1.5)) {
                $tag = 'Rendah';
                $urgensi = 3;
            } else {
                $tag = 'Aman';
                $urgensi = 4;
            }

        return [
                'kode_barang' => $item->kode_barang,
                'nama'       => $item->nama_barang,
                'tag'        => $tag,
                'keterangan' => "Stok {$stokAkhir} / Minimum {$minStok}",
                'urgensi'    => $urgensi,
            ];
        });

        if ($filterStatus !== 'semua') {
            $dataPrioritas = $dataPrioritas->filter(function ($item) use ($filterStatus) {
                return strtolower($item['tag']) === $filterStatus;
            });
        }

        $dataPrioritas = $dataPrioritas->sortBy('urgensi')->values();

        $pagePrio = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage('prio_page');
        $perPagePrio = 5;

        $prioritasTindakan = new \Illuminate\Pagination\LengthAwarePaginator(
            $dataPrioritas->forPage($pagePrio, $perPagePrio)->values(),
            $dataPrioritas->count(),
            $perPagePrio,
            $pagePrio,
            [
                'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => 'prio_page',
            ]
        );

        $prioritasTindakan->appends($request->all());

        // 9. Top Pemasok
        $topPemasok = DB::table('pemasoks')
            ->join('barang_masuks', 'pemasoks.id', '=', 'barang_masuks.id_pemasok')
            ->where('barang_masuks.user_id', '=', $userId)
            ->whereBetween('barang_masuks.tanggal_masuk', [$dateFrom, $dateTo])
            ->select(
                'pemasoks.nama_pemasok',
                DB::raw('SUM(barang_masuks.jumlah) as total_pasokan')
            )
            ->groupBy('pemasoks.id', 'pemasoks.nama_pemasok')
            ->orderByDesc('total_pasokan')
            ->limit(5)
            ->get();

        if ($topPemasok->isEmpty() && !$request->filled('date_from')) {
            $lastMasukDate = DB::table('barang_masuks')
                ->where('user_id', $userId)
                ->max('tanggal_masuk');

            if ($lastMasukDate) {
                $fallbackCarbon = \Carbon\Carbon::parse($lastMasukDate);
                $fallbackFrom   = $fallbackCarbon->copy()->startOfMonth()->startOfDay();
                $fallbackTo     = $fallbackCarbon->copy()->endOfMonth()->endOfDay();

                $topPemasok = DB::table('pemasoks')
                    ->join('barang_masuks', 'pemasoks.id', '=', 'barang_masuks.id_pemasok')
                    ->where('barang_masuks.user_id', '=', $userId)
                    ->whereBetween('barang_masuks.tanggal_masuk', [$fallbackFrom, $fallbackTo])
                    ->select(
                        'pemasoks.nama_pemasok',
                        DB::raw('SUM(barang_masuks.jumlah) as total_pasokan')
                    )
                    ->groupBy('pemasoks.id', 'pemasoks.nama_pemasok')
                    ->orderByDesc('total_pasokan')
                    ->limit(5)
                    ->get();

                $dateFrom = $fallbackFrom;
                $dateTo   = $fallbackTo;
            }
        }

            return view('dashboard.gudang', compact(
                'dateFrom', 'dateTo', 'kpi', 'trenTransaksi',
                'topKeluar', 'topMasuk', 'stokTerendah', 'kadaluarsa', 'prioritasTindakan',
                'idleStock', 'topPemasok', 'sortKadaluarsa'
            ));
        }

        public function getIdleStockData(Request $request, $isExport = false)
        {
            $userId = Auth::id();

            $sortOrder = $request->input('idle_sort', 'desc');
            $fsnFilter = $request->input('fsn_filter', 'all');
            $today = Carbon::now();

            $barangs = DB::table('items')
                ->where('user_id', $userId)
                ->select('kode_barang', 'nama_barang', 'stok_minimum')
                ->get();

            $filteredCollection = $barangs->map(function ($barang) use ($today, $userId) {
                $kode = $barang->kode_barang;

                $total_masuk = DB::table('barang_masuks')
                    ->where('user_id', $userId)
                    ->where('kode_barang', $kode)
                    ->sum('jumlah');

                $totalKeluar = DB::table('barang_keluars')
                    ->where('user_id', $userId)
                    ->where('kode_barang', $kode)
                    ->sum('jumlah_keluar');

                $stokSaatIni = max(0, $total_masuk - $totalKeluar);

                $lastKeluar = DB::table('barang_keluars')
                    ->where('user_id', $userId)
                    ->where('kode_barang', $kode)
                    ->latest('tanggal_keluar')
                    ->value('tanggal_keluar');

                $lastMasuk = DB::table('barang_masuks')
                    ->where('user_id', $userId)
                    ->where('kode_barang', $kode)
                    ->latest('tanggal_masuk')
                    ->value('tanggal_masuk');

                if ($lastKeluar) {
                    $lastActivityDate = Carbon::parse($lastKeluar);
                    $dslm = (int) $lastActivityDate->diffInDays($today);
                    $tglKeluarStr = $lastActivityDate->format('d M Y');
                } else {
                    $firstMasuk = DB::table('barang_masuks')
                        ->where('user_id', $userId)
                        ->where('kode_barang', $kode)
                        ->oldest('tanggal_masuk')
                        ->value('tanggal_masuk');

                    if ($firstMasuk) {
                        $firstMasukDate = Carbon::parse($firstMasuk);
                        $dslm =(int) $firstMasukDate->diffInDays($today);
                    } else {
                            $dslm = 0;
                    }
                    $tglKeluarStr = 'Belum Ada Transaksi Keluar';
                }


                $keluar365Hari = DB::table('barang_keluars')
                    ->where('user_id', $userId)
                    ->where('kode_barang', $kode)
                    ->where('tanggal_keluar', '>=', $today->copy()->subDays(365))
                    ->sum('jumlah_keluar');

                $tor = $stokSaatIni > 0 ? round($keluar365Hari / $stokSaatIni, 2) : 0;

                # Klasifikasi FSN — berbasis TOR sebagai penentu utama
                if ($keluar365Hari == 0 && $dslm >= 30) {
                    $kategoriFsn = 'Non-Moving (Idle)';
                    $badgeColor  = 'danger';
                }
                # Klasifikasi FSN — berbasis TOR sebagai penentu utama
                elseif ($tor < 1) {
                    $kategoriFsn = 'Non-Moving (Idle)';
                    $badgeColor  = 'danger';
                } elseif ($tor <= 3) {
                    $kategoriFsn = 'Slow-Moving';
                    $badgeColor  = 'warning';
                } else {
                    $kategoriFsn = 'Fast-Moving';
                    $badgeColor  = 'success';
                }

                return [
                    'kode_barang'       => $kode,
                    'nama_barang'       => $barang->nama_barang,
                    'stok'              => $stokSaatIni,
                    'hari_idle'         => $dslm . ' Hari',
                    'hari_idle_num'     => $dslm,
                    'terakhir_bergerak' => $tglKeluarStr,
                    'tor'               => $tor,
                    'kategori_fsn'      => $kategoriFsn,
                    'badge_color'       => $badgeColor,
                ];
            })
            ->filter(function ($row) {
                return $row['stok'] > 0;
               
            });
            
            if ($sortOrder === 'asc') {
                $filteredCollection = $filteredCollection->sortBy('hari_idle_num')->values();
            } else {
                $filteredCollection = $filteredCollection->sortByDesc('hari_idle_num')->values();
            }

            if ($isExport) {
                return $filteredCollection;
            }

            $perPage = 5;
            $currentPage = LengthAwarePaginator::resolveCurrentPage('idle_page');
            $currentPageItems = $filteredCollection->slice(($currentPage - 1) * $perPage, $perPage)->values();

            $paginatedItems = new LengthAwarePaginator(
                $currentPageItems,
                $filteredCollection->count(),
                $perPage,
                $currentPage,
                [
                    'path' => LengthAwarePaginator::resolveCurrentPath(),
                    'pageName' => 'idle_page',
                ]
            );

            return $paginatedItems->appends($request->all());
        }

        public function exportPrioritasPdf(Request $request)
        {
            $dateFrom       = $request->input('date_from', date('Y-m-01'));
            $dateTo         = $request->input('date_to', date('Y-m-d'));
            $filterStatus   = strtolower($request->input('filter_status', 'semua'));

            $allItems   = \App\Models\Item::all();

            $barangMasuk = DB::table('barang_masuks')
                ->select('kode_barang', DB::raw('SUM(jumlah) as total_masuk'))
                ->whereBetween('tanggal_masuk', [$dateFrom, $dateTo])
                ->groupBy('kode_barang')
                ->pluck('total_masuk', 'kode_barang');

            $barangKeluar = DB::table('barang_keluars')
                ->select('kode_barang', DB::raw('SUM(jumlah_keluar) as total_keluar'))
                ->whereBetween('tanggal_keluar', [$dateFrom, $dateTo])
                ->groupBy('kode_barang')
                ->pluck('total_keluar', 'kode_barang');

            $dataPrioritas = $allItems->map(function ($item) use ($barangMasuk, $barangKeluar) {
            $kode = $item->kode_barang;

            $masuk = $barangMasuk[$kode] ?? 0;
            $keluar = $barangKeluar[$kode] ?? 0;

            $stokAkhir = $masuk - $keluar;
            if ($stokAkhir < 0) { $stokAkhir = 0; }

            $minStok = $item->stok_minimum ?? 5;

            if ($stokAkhir == 0) {
                $tag = 'Habis';
                $urgensi = 1;
            } elseif ($stokAkhir <= $minStok) {
                $tag = 'Kritis';
                $urgensi = 2;
            } elseif ($stokAkhir <= ($minStok * 1.5)) {
                $tag = 'Rendah';
                $urgensi = 3;
            } else {
                $tag = 'Aman';
                $urgensi = 4;
            }

            return [
                'kode_barang'  => $item->kode_barang,
                'nama'         => $item->nama_barang,
                'stok_akhir'   => $stokAkhir,
                'stok_minimum' => $minStok,
                'tag'          => $tag,
                'urgensi'      => $urgensi,
            ];
        });

        if ($filterStatus !== 'semua') {
            $dataPrioritas = $dataPrioritas->filter(function ($item) use ($filterStatus) {
                return strtolower($item['tag']) === $filterStatus;
            });
        }

        $prioritasItems = $dataPrioritas->sortBy('urgensi')->values();

        // Memanggil view di folder dashboard/prioritas_pdf.blade.php
        $pdf = Pdf::loadView('dashboard.prioritas_pdf', [
            'items'        => $prioritasItems,
            'dateFrom'     => $dateFrom,
            'dateTo'       => $dateTo,
            'filterStatus' => ucfirst($filterStatus),
            'printedBy'    => Auth::check() ? Auth::user()->name : 'Gudang',
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('laporan_prioritas_tindakan_' . date('Ymd_His') . '.pdf');
        }

        public function exportIdleStockPdf (Request $request)
        {
            $idleItems = $this->getIdleStockData($request, true);

            $pdf = Pdf::loadView('dashboard.idle_stock_pdf', [
                'items'     => $idleItems,
                'sortOrder' => $request->input('idle_sort', 'desc'),
                'printedBy' => Auth::check() ? Auth::user()->name : 'Gudang',
            ])->setPaper('a4', 'portrait');

            return $pdf->stream('laporan_idle_stock_' . date('Ymd_His') . '.pdf');
        }
}