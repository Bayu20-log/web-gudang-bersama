<?php




namespace App\Http\Controllers;




use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Exports\LaporanAsetExport;
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




        // --- Hitung aset. Kondisi (Baik / Segera Kadaluarsa / Kadaluarsa) dihitung dari
        //     tanggal kadaluarsa saat barang masuk. Logika yang sama dipakai untuk PDF & Excel.
        $grouped = LaporanAsetExport::hitungAset($userId, $start, $end);

        // Opsi dropdown filter
        $listLokasi  = $grouped->pluck('lokasi')->unique()->sort()->values();
        $listKondisi = collect(LaporanAsetExport::DAFTAR_KONDISI);

        // Sorting (kolom Kondisi diurutkan menurut tingkat bahaya, bukan abjad)
        $sortBy  = $request->sort_by ?? 'nama_barang';
        $sortDir = $request->sort_dir === 'desc' ? 'desc' : 'asc';
        $urutanKondisi = array_flip(LaporanAsetExport::DAFTAR_KONDISI);

        $grouped = $grouped->sortBy(function ($item) use ($sortBy, $urutanKondisi) {
            if ($sortBy === 'kondisi') {
                return $urutanKondisi[$item['kondisi']] ?? 99;
            }
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
