<?php




namespace App\Http\Controllers;




use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use App\Models\Kategori;
use App\Models\Lokasi;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Carbon\Carbon;




class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $nama   = $request->nama_barang;
        $kode   = $request->kode_barang;
        $lokasi = $request->lokasi;
        $sortBy = $request->sort_by ?? 'nama_barang';
        $sortDir = $request->sort_dir === 'desc' ? 'desc' : 'asc';
        $userId = auth()->id(); // Ambil ID user yang login




        // Barang Masuk milik user login
        $barangMasuk = BarangMasuk::with(['item', 'lokasi', 'user'])
            ->where('user_id', $userId)
            ->get()
            ->filter(function ($bm) use ($nama, $kode, $lokasi) {
                return (!$nama || str_contains(strtolower($bm->item->nama_barang), strtolower($nama)))
                    && (!$kode || str_contains(strtolower($bm->item->kode_barang), strtolower($kode)))
                    && (!$lokasi || $bm->lokasi?->id == $lokasi);
            })
            ->map(function ($bm) {
                return collect([
                    'kode_barang' => $bm->item->kode_barang,
                    'nama_barang' => $bm->item->nama_barang,
                    'harga_dasar' => $bm->item->harga_dasar ?? 0,
                    'lokasi'      => [
                        'id' => $bm->lokasi->id ?? null,
                        'nama_lokasi' => $bm->lokasi->nama_lokasi ?? '-',
                    ],
                    'kondisi'     => $bm->kondisi ?? 'Baik',
                    'username'    => $bm->user->username ?? '-',
                    'jenis'       => 'Masuk',
                    'jumlah'      => $bm->jumlah,
                ]);
            });




        // Barang Keluar milik user login
        $barangKeluar = BarangKeluar::with(['item', 'lokasi', 'user'])
            ->where('user_id', $userId)
            ->get()
            ->filter(function ($bk) use ($nama, $kode, $lokasi) {
                return (!$nama || str_contains(strtolower($bk->item->nama_barang), strtolower($nama)))
                    && (!$kode || str_contains(strtolower($bk->item->kode_barang), strtolower($kode)))
                    && (!$lokasi || $bk->lokasi?->id == $lokasi);
            })
            ->map(function ($bk) {
                return collect([
                    'kode_barang' => $bk->item->kode_barang,
                    'nama_barang' => $bk->item->nama_barang,
                    'harga_dasar' => $bk->item->harga_dasar ?? 0,
                    'lokasi'      => [
                        'id' => $bk->lokasi->id ?? null,
                        'nama_lokasi' => $bk->lokasi->nama_lokasi ?? '-',
                    ],
                    'kondisi'     => $bk->kondisi ?? 'Baik',
                    'username'    => $bk->user->username ?? '-',
                    'jenis'       => 'Keluar',
                    'jumlah'      => $bk->jumlah_keluar,
                ]);
            });




        // Gabungkan dan kelompokkan
        $merged = $barangMasuk->merge($barangKeluar);




        $grouped = $merged->groupBy(function ($row) {
            return $row['kode_barang'] . '|' . ($row['lokasi']['nama_lokasi'] ?? '-') . '|' . $row['kondisi'] . '|' . $row['username'];
        });




        // Hitung total stok
        $collection = $grouped->map(function ($rows) {
            $rows = collect($rows);
            return [
                'kode_barang'  => $rows->first()['kode_barang'],
                'nama_barang'  => $rows->first()['nama_barang'],
                'harga_dasar'  => $rows->first()['harga_dasar'],
                'total_masuk'  => $rows->where('jenis', 'Masuk')->sum('jumlah'),
                'total_keluar' => $rows->where('jenis', 'Keluar')->sum('jumlah'),
                'stok_akhir'   => $rows->where('jenis', 'Masuk')->sum('jumlah') - $rows->where('jenis', 'Keluar')->sum('jumlah'),
                'lokasi'       => $rows->first()['lokasi'],
                'kondisi'      => $rows->first()['kondisi'],
                'username'     => $rows->first()['username'],
            ];
        });




        // Sorting
        $collection = $collection->sortBy(function ($item) use ($sortBy) {
            $value = data_get($item, $sortBy);
            return is_string($value) ? strtolower($value) : $value;
        }, SORT_REGULAR, $sortDir === 'desc')->values();




        // Lokasi unik untuk dropdown filter
        $lokasiList = $collection->pluck('lokasi')
            ->unique(fn($lok) => is_array($lok) ? $lok['nama_lokasi'] : $lok)
            ->values()
            ->all();




        // Pagination (stok)
        $perPage = 10;
        $page = LengthAwarePaginator::resolveCurrentPage('page');
        $currentItems = $collection->forPage($page, $perPage);




        $paginator = new LengthAwarePaginator(
            $currentItems,
            $collection->count(),
            $perPage,
            $page,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'query' => $request->query(),
            ]
        );




        return view('laporan.index', [
            'data' => $paginator,
            'lokasis' => $lokasiList,
        ]);
    }




    public function arus(Request $request)
    {
        $start = $request->start_date;
        $end = $request->end_date;
        $kategori = $request->kategori;
        $lokasi = $request->lokasi;
        $search = $request->search;
        $userId = auth()->id();




        // Barang Masuk
        $arusMasuk = BarangMasuk::with(['item.kategori', 'lokasi'])
            ->where('user_id', $userId)
            ->when($start && $end, fn($q) => $q->whereBetween('tanggal_masuk', [$start, $end]))
            ->get()
            ->filter(function ($masuk) use ($kategori, $lokasi, $search) {
                return (!$kategori || $masuk->item->id_kategori == $kategori)
                    && (!$lokasi || $masuk->id_lokasi == $lokasi)
                    && (!$search || str_contains(strtolower($masuk->item->nama_barang), strtolower($search))
                        || str_contains(strtolower($masuk->item->kode_barang), strtolower($search))
                        || str_contains(strtolower($masuk->pemasok ?? ''), strtolower($search)));
            })
            ->map(function ($masuk) {
                return [
                    'tanggal' => $masuk->tanggal_masuk,
                    'jenis' => 'Masuk',
                    'kode_barang' => $masuk->item->kode_barang,
                    'nama_barang' => $masuk->item->nama_barang,
                    'harga_dasar' => $masuk->item->harga_dasar ?? 0,
                    'jumlah' => $masuk->jumlah,
                    'lokasi' => $masuk->lokasi->nama_lokasi ?? '-',
                    'pihak' => $masuk->pemasok ?? '-',
                ];
            });




        // Barang Keluar
        $arusKeluar = BarangKeluar::with(['item.kategori', 'lokasi'])
            ->where('user_id', $userId)
            ->when($start && $end, fn($q) => $q->whereBetween('tanggal_keluar', [$start, $end]))
            ->get()
            ->filter(function ($keluar) use ($kategori, $lokasi, $search) {
                return (!$kategori || $keluar->item->id_kategori == $kategori)
                    && (!$lokasi || $keluar->id_lokasi == $lokasi)
                    && (!$search || str_contains(strtolower($keluar->item->nama_barang), strtolower($search))
                        || str_contains(strtolower($keluar->item->kode_barang), strtolower($search))
                        || str_contains(strtolower($keluar->penerima ?? ''), strtolower($search)));
            })
            ->map(function ($keluar) {
                return [
                    'tanggal' => $keluar->tanggal_keluar,
                    'jenis' => 'Keluar',
                    'kode_barang' => $keluar->item->kode_barang,
                    'nama_barang' => $keluar->item->nama_barang,
                    'harga_dasar' => $keluar->item->harga_dasar ?? 0,
                    'jumlah' => $keluar->jumlah_keluar,
                    'lokasi' => $keluar->lokasi->nama_lokasi ?? '-',
                    'pihak' => $keluar->penerima ?? '-',
                ];
            });




        // Gabungkan dan urutkan
        $combined = $arusMasuk->merge($arusKeluar)->sortBy('tanggal')->values();




        // Hitung stok berjalan
        $stokPerBarang = [];
        $enhanced = collect();




        foreach ($combined as $row) {
            $kode = $row['kode_barang'];
            if (!isset($stokPerBarang[$kode])) $stokPerBarang[$kode] = 0;




            $jumlahMasuk  = $row['jenis'] === 'Masuk'  ? $row['jumlah'] : 0;
            $jumlahKeluar = $row['jenis'] === 'Keluar' ? $row['jumlah'] : 0;




            $stokPerBarang[$kode] += $jumlahMasuk - $jumlahKeluar;




            $enhanced->push(array_merge($row, [
                'jumlah_masuk'  => $jumlahMasuk,
                'jumlah_keluar' => $jumlahKeluar,
                'total_barang'  => $stokPerBarang[$kode],
            ]));
        }




        // Sorting manual
        $sortBy = $request->get('sort_by', 'tanggal');
        $sortDir = $request->get('sort_dir', 'asc');
        $enhanced = $enhanced->sortBy($sortBy, SORT_REGULAR, $sortDir === 'desc')->values();




        // Pagination manual (10 per halaman)
        $perPage = 10; // <-- diset ke 10 agar "Showing 1 to 10 ..."
        $page = LengthAwarePaginator::resolveCurrentPage();
        $currentPageItems = $enhanced->slice(($page - 1) * $perPage, $perPage)->values()->all();




        $paginated = new LengthAwarePaginator(
            $currentPageItems,
            $enhanced->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );




        // Ambil kategori dan lokasi berdasarkan user login
        $kategoris = Kategori::all();




        $lokasiMasuk = BarangMasuk::where('user_id', $userId)->pluck('id_lokasi')->unique();
        $lokasiKeluar = BarangKeluar::where('user_id', $userId)->pluck('id_lokasi')->unique();
        $lokasiIds = $lokasiMasuk->merge($lokasiKeluar)->unique();
        $lokasis = Lokasi::whereIn('id', $lokasiIds)->get();




        return view('laporan.arus', compact(
            'paginated',
            'start',
            'end',
            'kategoris',
            'lokasis',
            'search',
            'kategori',
            'lokasi'
        ));
    }






public function stokViewer(Request $request)
{
    $query = Item::select('items.*')
        ->addSelect([
            // total stok masuk (stok_akhir)
            'stok_akhir' => function ($q) {
                $q->selectRaw('COALESCE(SUM(jumlah),0)')
                  ->from('barang_masuks')
                  ->whereColumn('barang_masuks.kode_barang', 'items.kode_barang');
            },
            // username terakhir input barang masuk
            'username' => function ($q) {
                $q->selectRaw('COALESCE(users.username, users.name)')
                  ->from('barang_masuks')
                  ->join('users', 'users.id', '=', 'barang_masuks.user_id')
                  ->whereColumn('barang_masuks.kode_barang', 'items.kode_barang')
                  ->orderBy('barang_masuks.created_at', 'desc')
                  ->limit(1);
            },
        ]);


    // Pencarian: kode / nama / username terakhir (via subquery)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('items.kode_barang', 'like', "%{$search}%")
              ->orWhere('items.nama_barang', 'like', "%{$search}%")
              ->orWhereRaw(
                  "(SELECT COALESCE(u.username, u.name)
                      FROM barang_masuks bm
                      JOIN users u ON u.id = bm.user_id
                     WHERE bm.kode_barang = items.kode_barang
                  ORDER BY bm.created_at DESC
                     LIMIT 1) LIKE ?",
                  ["%{$search}%"]
              );
        });
    }


    // Hanya tampilkan item yang pernah ada transaksi masuk (stok_akhir > 0 secara efektif)
    $query->whereExists(function ($q) {
        $q->select(DB::raw(1))
          ->from('barang_masuks')
          ->whereColumn('barang_masuks.kode_barang', 'items.kode_barang');
    });


    // Sorting aman (izinkan hanya kolom berikut)
    $allowedSort = ['kode_barang', 'nama_barang', 'stok_akhir', 'username'];
    $sortBy  = $request->get('sort_by', 'nama_barang');
    $sortDir = strtolower($request->get('sort_dir', 'asc')) === 'desc' ? 'desc' : 'asc';
    if (!in_array($sortBy, $allowedSort, true)) {
        $sortBy = 'nama_barang';
    }


    // ORDER BY termasuk alias (MySQL mendukung)
    $query->orderByRaw("{$sortBy} {$sortDir}");


    // Pagination + keep query string
    $data = $query->paginate(10)->appends($request->query());


    return view('views.index', compact('data', 'sortBy', 'sortDir'));
}


public function stokAdmin(Request $request)
{
    $query = Item::select(
            'items.nama_barang',
            'items.kode_barang',
            DB::raw('COALESCE(SUM(bm.jumlah),0) as stok_akhir'),
            DB::raw('COALESCE(u.username, u.name) as username')
        )
        ->leftJoin('barang_masuks as bm', 'bm.kode_barang', '=', 'items.kode_barang')
        ->leftJoin('users as u', 'u.id', '=', 'bm.user_id')
        ->groupBy('items.nama_barang', 'items.kode_barang', 'u.username', 'u.name');


    // Filter pencarian
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('items.kode_barang', 'like', "%{$search}%")
              ->orWhere('items.nama_barang', 'like', "%{$search}%")
              ->orWhere('u.username', 'like', "%{$search}%")
              ->orWhere('u.name', 'like', "%{$search}%");
        });
    }


    // Filter lokasi jika ada
    if ($request->filled('lokasi')) {
        $query->where('items.lokasi', $request->lokasi);
    }


    // Hanya tampilkan stok > 0
    $query->having('stok_akhir', '>', 0);


    // Sorting
    $sortBy = $request->sort_by ?? 'items.nama_barang';
    $sortDir = $request->sort_dir === 'desc' ? 'desc' : 'asc';
    $query->orderBy($sortBy, $sortDir);


    // Pagination
    $data = $query->paginate(10)->appends($request->query());


    return view('views.admin', compact('data', 'sortBy', 'sortDir'));
}




}
