<?php




namespace App\Http\Controllers;




use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;




class LaporanAsetController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();




        // --- Parse tanggal fleksibel: terima 'Y-m-d' atau 'd/m/Y'
        $start = $this->parseDate($request->start_date, true);   // startOfDay
        $end   = $this->parseDate($request->end_date, false);    // endOfDay




        // --- Ambil data dengan eager load lengkap untuk kurangi N+1
        $items = Item::with([
                'barangMasuk.kondisi',
                'barangMasuk.lokasi',
                'barangKeluar.kondisi',
                'barangKeluar.lokasi',
            ])
            ->whereHas('barangMasuk', function ($q) use ($userId, $start, $end) {
                $q->where('user_id', $userId);
                if ($start && $end) {
                    $q->whereBetween('tanggal_masuk', [$start, $end]);
                }
            })
            ->get();




        $data        = collect();
        $listLokasi  = collect();
        $listKondisi = collect();




        foreach ($items as $item) {
            // Filter barang masuk berdasarkan tanggal (jika ada rentang)
            $filteredBM = $item->barangMasuk;
            if ($start && $end) {
                $filteredBM = $filteredBM->filter(function ($bm) use ($start, $end) {
                    return Carbon::parse($bm->tanggal_masuk)->between($start, $end);
                });
            }




            // Kumpulkan opsi lokasi & kondisi yang memang dipakai
            foreach ($filteredBM as $bm) {
                if ($bm->lokasi)   $listLokasi->push($bm->lokasi->nama_lokasi);
                if ($bm->kondisi)  $listKondisi->push($bm->kondisi->nama_kondisi);
            }




            // Grup per nama|lokasi|kondisi
            $grup = $filteredBM->groupBy(function ($bm) {
                return $bm->item->nama_barang . '|' .
                       ($bm->lokasi->nama_lokasi ?? '-') . '|' .
                       ($bm->kondisi->nama_kondisi ?? '-');
            });




            foreach ($grup as $key => $groupedBM) {
                [$namaBarang, $lokasi, $kondisi] = explode('|', $key);




                $totalMasuk = $groupedBM->sum('jumlah');




                // Hitung keluar untuk lokasi & kondisi yg sama (dan tanggal bila ada)
                $totalKeluar = $item->barangKeluar
                    ->filter(function ($bk) use ($lokasi, $kondisi, $start, $end) {
                        $validTanggal = true;
                        if ($start && $end) {
                            $validTanggal = Carbon::parse($bk->tanggal_keluar)->between($start, $end);
                        }
                        return $validTanggal
                            && (($bk->lokasi->nama_lokasi ?? '-') === $lokasi)
                            && (($bk->kondisi->nama_kondisi ?? '-') === $kondisi);
                    })
                    ->sum('jumlah_keluar');




                $stokAkhir = $totalMasuk - $totalKeluar;
                $hargaBeli = $groupedBM->first()?->harga_satuan ?? 0;




                $data->push([
                    'nama_barang'  => $namaBarang,
                    'lokasi'       => $lokasi,
                    'kondisi'      => $kondisi,
                    'stok_akhir'   => $stokAkhir,
                    'harga_beli'   => $hargaBeli,
                    'jumlah_aset'  => $stokAkhir * $hargaBeli,
                ]);
            }
        }




        // Gabungkan lagi jika ada duplikat kunci nama|lokasi|kondisi
        $grouped = $data->groupBy(function ($row) {
            return $row['nama_barang'].'|'.$row['lokasi'].'|'.$row['kondisi'];
        })->map(function ($group) {
            $first = $group->first();
            $stok  = $group->sum('stok_akhir');
            return [
                'nama_barang'  => $first['nama_barang'],
                'lokasi'       => $first['lokasi'],
                'kondisi'      => $first['kondisi'],
                'stok_akhir'   => $stok,
                'harga_beli'   => $first['harga_beli'],
                'jumlah_aset'  => $stok * $first['harga_beli'],
            ];
        })->values();




        // Sorting
        $sortBy  = $request->sort_by ?? 'nama_barang';
        $sortDir = $request->sort_dir === 'desc' ? 'desc' : 'asc';




        $grouped = $grouped->sortBy(function ($item) use ($sortBy) {
            $value = $item[$sortBy] ?? null;
            return is_numeric($value) ? (float) $value : (is_string($value) ? mb_strtolower($value) : $value);
        }, SORT_REGULAR, $sortDir === 'desc')->values();




        // Filtering
        $filtered = $grouped->filter(function ($row) use ($request) {
            return $row['stok_akhir'] > 0
                && (!$request->lokasi       || $row['lokasi']  == $request->lokasi)
                && (!$request->kondisi      || $row['kondisi'] == $request->kondisi)
                && (!$request->nama_barang  || stripos($row['nama_barang'], $request->nama_barang) !== false);
        })->values();




        $totalAset = $filtered->sum('jumlah_aset');




        // Opsi dropdown (unik & terurut)
        $listLokasi  = $listLokasi->unique()->sort()->values();
        $listKondisi = $listKondisi->unique()->sort()->values();




        // --- Pagination manual untuk Collection hasil filter
        $perPage = (int) $request->get('per_page', 10);
        $page    = LengthAwarePaginator::resolveCurrentPage();




        $paginated = new LengthAwarePaginator(
            $filtered->forPage($page, $perPage)->values(),
            $filtered->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );


        $filters = collect([
            'Nama Barang'    => $request->nama_barang,
            'Lokasi'         => $request->lokasi,
            'Kondisi'        => $request->kondisi,
        ]);


        if ($request->start_date) {
            $filters->put('Tanggal Mulai', Carbon::createFromFormat('Y-m-d', $request->start_date)->format('d/m/Y'));
        }
        if ($request->end_date) {
            $filters->put('Tanggal Selesai', Carbon::createFromFormat('Y-m-d', $request->end_date)->format('d/m/Y'));
        }




        return view('aset.index', [
            'grouped'     => $paginated,   // <- sekarang paginator
            'totalAset'   => $totalAset,
            'listLokasi'  => $listLokasi,
            'listKondisi' => $listKondisi,
            'start'       => $request->start_date,
            'end'         => $request->end_date,
            'filters'     => $filters,
        ]);
    }




    /**
     * Parse string tanggal 'Y-m-d' atau 'd/m/Y' menjadi Carbon mulai/habis hari.
     * @param  string|null $value
     * @param  bool $startOfDay  true: startOfDay, false: endOfDay
     * @return \Carbon\Carbon|null
     */
    private function parseDate(?string $value, bool $startOfDay = true): ?Carbon
    {
        if (!$value) return null;




        $formats = ['Y-m-d', 'd/m/Y'];
        foreach ($formats as $fmt) {
            try {
                $c = Carbon::createFromFormat($fmt, $value);
                return $startOfDay ? $c->startOfDay() : $c->endOfDay();
            } catch (\Throwable $e) { /* coba format lain */ }
        }
        // fallback: biarkan null jika format tak cocok
        return null;
    }
}
