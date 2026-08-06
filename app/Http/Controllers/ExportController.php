<?php




namespace App\Http\Controllers;




use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanStokExport;
use App\Exports\LaporanArusExport;
use App\Exports\OmzetExport;
use App\Exports\LaporanAsetExport;
use Barryvdh\DomPDF\Facade\Pdf; // Perbaikan di sini
use Carbon\Carbon;




class ExportController extends Controller
{
   public function exportPdf(Request $request)
{
    $user = auth()->user();
    $nama = $request->nama_barang;
    $kode = $request->kode_barang;
    $lokasiId = $request->lokasi;








    // Ambil nama lokasi jika ID tersedia
    $lokasiNama = null;
    if ($lokasiId) {
        $lokasiModel = \App\Models\Lokasi::find($lokasiId);
        $lokasiNama = $lokasiModel ? $lokasiModel->nama_lokasi : null;
    }








    $barangMasuk = BarangMasuk::with(['item', 'lokasi', 'user'])
        ->where('user_id', $user->id)
        ->get()
        ->filter(function ($bm) use ($nama, $kode, $lokasiId) {
            return (!$nama || str_contains(strtolower($bm->item->nama_barang), strtolower($nama))) &&
                   (!$kode || str_contains(strtolower($bm->item->kode_barang), strtolower($kode))) &&
                   (!$lokasiId || ($bm->lokasi && $bm->lokasi->id == $lokasiId));
        })
        ->map(function ($bm) {
            return collect([
                'kode_barang' => $bm->item->kode_barang,
                'nama_barang' => $bm->item->nama_barang,
                'harga_dasar' => $bm->item->harga_dasar ?? 0,
                'lokasi'      => $bm->lokasi->nama_lokasi ?? '-',
                'kondisi'     => $bm->kondisi ?? 'Baik',
                'username'    => $bm->user->username ?? '-',
                'jenis'       => 'Masuk',
                'jumlah'      => $bm->jumlah,
                'tanggal'     => $bm->tanggal_masuk,
            ]);
        });








    $barangKeluar = BarangKeluar::with(['item', 'lokasi', 'user'])
        ->where('user_id', $user->id)
        ->get()
        ->filter(function ($bk) use ($nama, $kode, $lokasiId) {
            return (!$nama || str_contains(strtolower($bk->item->nama_barang), strtolower($nama))) &&
                   (!$kode || str_contains(strtolower($bk->item->kode_barang), strtolower($kode))) &&
                   (!$lokasiId || ($bk->lokasi && $bk->lokasi->id == $lokasiId));
        })
        ->map(function ($bk) {
            return collect([
                'kode_barang' => $bk->item->kode_barang,
                'nama_barang' => $bk->item->nama_barang,
                'harga_dasar' => $bk->item->harga_dasar ?? 0,
                'lokasi'      => $bk->lokasi->nama_lokasi ?? '-',
                'kondisi'     => $bk->kondisi ?? 'Baik',
                'username'    => $bk->user->username ?? '-',
                'jenis'       => 'Keluar',
                'jumlah'      => $bk->jumlah_keluar,
                'tanggal'     => $bk->tanggal_keluar,
            ]);
        });








    $merged = $barangMasuk->merge($barangKeluar);








    $grouped = $merged->groupBy(function ($row) {
        return $row['kode_barang'] . '|' . $row['lokasi'] . '|' . $row['kondisi'] . '|' . $row['username'];
    });








    $data = $grouped->map(function ($rows) {
        $rows = collect($rows);
        return [
            'kode_barang'  => $rows->first()['kode_barang'],
            'nama_barang'  => $rows->first()['nama_barang'],
            'harga_dasar' => $rows->first()['harga_dasar'] ?? 0,
            'total_masuk'  => $rows->where('jenis', 'Masuk')->sum('jumlah'),
            'total_keluar' => $rows->where('jenis', 'Keluar')->sum('jumlah'),
            'stok_akhir'   => $rows->where('jenis', 'Masuk')->sum('jumlah') - $rows->where('jenis', 'Keluar')->sum('jumlah'),
            'lokasi'       => $rows->first()['lokasi'],
            'kondisi'      => $rows->first()['kondisi'],
            'username'     => $rows->first()['username'],
        ];
    })->filter(fn($row) => $row['stok_akhir'] > 0)->values();








    // Ambil periode
    $tanggalAwal = $merged->min('tanggal') ?? now();
    $tanggalAkhir = now();
    $tanggalCetak = now()->format('d/m/Y H:i:s');








    // Tambahkan lokasi ke daftar filters
    $filters = [
        'Nama Barang' => $nama,
        'Kode Barang' => $kode,
        'Lokasi'      => $lokasiNama,
    ];








    return Pdf::loadView('laporan.print', [
        'data' => $data,
        'nama' => $user->name,
        'username' => $user->username,
        'tanggalAwal' => Carbon::parse($tanggalAwal)->format('d/m/Y'),
        'tanggalAkhir' => Carbon::parse($tanggalAkhir)->format('d/m/Y'),
        'tanggalCetak' => $tanggalCetak,
        'filters' => $filters,
    ])->stream('laporan_stok_barang.pdf');
}
























public function exportExcel(Request $request)
{
    $userId = auth()->id();
    $nama = $request->nama_barang;
    $kode = $request->kode_barang;
















    $barangMasuk = BarangMasuk::with(['item', 'lokasi', 'user'])
        ->where('user_id', $userId)
        ->get()
        ->filter(function ($bm) use ($nama, $kode) {
            return (!$nama || str_contains(strtolower($bm->item->nama_barang), strtolower($nama))) &&
                   (!$kode || str_contains(strtolower($bm->item->kode_barang), strtolower($kode)));
        })
        ->map(function ($bm) {
            return collect([
                'kode_barang' => $bm->item->kode_barang,
                'nama_barang' => $bm->item->nama_barang,
                'harga_dasar' => $bm->item->harga_dasar ?? 0,
                'lokasi'      => $bm->lokasi->nama_lokasi ?? '-',
                'kondisi'     => $bm->kondisi ?? 'Baik',
                'username'    => $bm->user->username ?? '-',
                'jenis'       => 'Masuk',
                'jumlah'      => $bm->jumlah,
            ]);
        });
















    $barangKeluar = BarangKeluar::with(['item', 'lokasi', 'user'])
        ->where('user_id', $userId)
        ->get()
        ->filter(function ($bk) use ($nama, $kode) {
            return (!$nama || str_contains(strtolower($bk->item->nama_barang), strtolower($nama))) &&
                   (!$kode || str_contains(strtolower($bk->item->kode_barang), strtolower($kode)));
        })
        ->map(function ($bk) {
            return collect([
                'kode_barang' => $bk->item->kode_barang,
                'nama_barang' => $bk->item->nama_barang,
                'harga_dasar' => $bk->item->harga_dasar ?? 0,
                'lokasi'      => $bk->lokasi->nama_lokasi ?? '-',
                'kondisi'     => $bk->kondisi ?? 'Baik',
                'username'    => $bk->user->username ?? '-',
                'jenis'       => 'Keluar',
                'jumlah'      => $bk->jumlah_keluar,
               
            ]);
        });
















    $merged = $barangMasuk->merge($barangKeluar);
















    $grouped = $merged->groupBy(function ($row) {
        return $row['kode_barang'] . '|' . $row['lokasi'] . '|' . $row['kondisi'] . '|' . $row['username'];
    });
















    $data = $grouped->map(function ($rows) {
        $rows = collect($rows);
        return [
            'kode_barang'  => $rows->first()['kode_barang'],
            'nama_barang'  => $rows->first()['nama_barang'],
            'harga_dasar'  => $rows->first()['harga_dasar'], // ✅ harga dari item
            'total_masuk'  => $rows->where('jenis', 'Masuk')->sum('jumlah'),
            'total_keluar' => $rows->where('jenis', 'Keluar')->sum('jumlah'),
            'stok_akhir'   => $rows->where('jenis', 'Masuk')->sum('jumlah') - $rows->where('jenis', 'Keluar')->sum('jumlah'),
            'lokasi'       => $rows->first()['lokasi'],
















            'username'     => $rows->first()['username'],
        ];
    })->values();
















    // Export langsung array ke Excel tanpa export class
    return Excel::download(new \App\Exports\ArrayExport($data->toArray()), 'laporan_stok_barang.xlsx');
}
































    public function exportArusPdf(Request $request)
{
    $user = auth()->user();








    $start = $request->start_date;
    $end = $request->end_date;
    $kategori = $request->kategori;
    $lokasi = $request->lokasi;
    $search = $request->search;








    $userId = $user->id;








    // Ambil data barang masuk
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
                'pihak' => is_object($masuk->pemasok) ? ($masuk->pemasok->nama_pemasok ?? '-') : ($masuk->pemasok ?? '-'),
            ];
        });
















    // Ambil data barang keluar
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








        $jumlahMasuk = $row['jenis'] === 'Masuk' ? $row['jumlah'] : 0;
        $jumlahKeluar = $row['jenis'] === 'Keluar' ? $row['jumlah'] : 0;








        $stokPerBarang[$kode] += $jumlahMasuk - $jumlahKeluar;








        $enhanced->push(array_merge($row, [
            'jumlah_masuk' => $jumlahMasuk,
            'jumlah_keluar' => $jumlahKeluar,
            'total_barang' => $stokPerBarang[$kode],
        ]));
    }








    // Ambil tanggal awal & akhir dari data
    $tanggalAwal = optional($enhanced->min('tanggal')) ? \Carbon\Carbon::parse($enhanced->min('tanggal'))->format('d/m/Y') : '-';
    $tanggalAkhir = now('Asia/Makassar')->format('d/m/Y');








    // Ambil nama kategori dan lokasi (jika ada)
    $namaKategori = optional(\App\Models\Kategori::find($kategori))->nama_kategori;
    $namaLokasi = optional(\App\Models\Lokasi::find($lokasi))->nama_lokasi;








    // Buat filters untuk ditampilkan di PDF
    $filters = [
        //'Tanggal Mulai' => $start,
        //'Tanggal Selesai' => $end,
        'Kategori' => $namaKategori,
        'Lokasi' => $namaLokasi,
        'Pencarian' => $search,
    ];








    return \Barryvdh\DomPDF\Facade\Pdf::loadView('laporan.arus_print', [
    'data' => $enhanced,
    'username' => $user->username,
    'nama' => $user->name,
    'tanggalCetak' => now('Asia/Makassar')->format('d/m/Y H:i:s'),
    'tanggalAwal' => $tanggalAwal,
    'tanggalAkhir' => $tanggalAkhir,
    'filters' => $filters,
    ])->stream('laporan_arus_barang.pdf');


}








































    public function exportArusExcel(Request $request)
    {
        return Excel::download(new LaporanArusExport($request), 'laporan_arus_barang.xlsx');
    }
















    public function exportOmzetPdf(Request $request)
{
    $user = auth()->user();
    $userId = $user->id;




    $query = \App\Models\BarangKeluar::with(['item', 'lokasi', 'kondisi'])
        ->where('user_id', $userId);
       
    if ($request->filled('lokasi')) {
        $query->where('id_lokasi', $request->lokasi);
    }
   
    if ($request->filled('kondisi')) {
        $query->where('id_kondisi', $request->kondisi);
    }




    // Perbaiki filter untuk search
    if ($request->filled('search')) {
        $search = $request->search;
        $query->whereHas('item', fn($q) =>
            $q->where('nama_barang', 'like', "%{$search}%")
              ->orWhere('kode_barang', 'like', "%{$search}%")
        );
    }




    // Perbaiki nama variabel tanggal
    $startDate = $request->start_date;
    $endDate = $request->end_date;




    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereBetween('tanggal_keluar', [$startDate, $endDate]);
    }




    $data = $query->get()->map(function ($item) {
        $omzet = $item->jumlah_keluar * $item->harga_jual;
        return [
            'tanggal'       => $item->tanggal_keluar,
            'nama_barang'   => optional($item->item)->nama_barang ?? '-',
            'lokasi'        => optional($item->lokasi)->nama_lokasi ?? '-',
            'kondisi'       => optional($item->kondisi)->nama_kondisi ?? '-',
            'jumlah_keluar' => $item->jumlah_keluar,
            'harga_jual'    => $item->harga_jual,
            'omzet_item'    => $omzet,
        ];
    });




    $total_omzet = $data->sum('omzet_item');




    // Gunakan variabel yang benar untuk parsing tanggal
    $tanggalMulai = $request->filled('start_date') ? Carbon::parse($request->start_date)->format('d/m/Y') : '-';
    $tanggalSelesai = $request->filled('end_date') ? Carbon::parse($request->end_date)->format('d/m/Y') : '-';
   
    $filters = [
        'Tanggal Mulai' => $tanggalMulai,
        'Tanggal Selesai' => $tanggalSelesai,
        'Cari Barang' => $request->search, // Menggunakan 'search'
        'Lokasi' => optional(\App\Models\Lokasi::find($request->lokasi))->nama_lokasi,
        'Kondisi' => optional(\App\Models\Kondisi::find($request->kondisi))->nama_kondisi,
    ];




    return \Barryvdh\DomPDF\Facade\Pdf::loadView('omzet.print', [
    'data' => $data,
    'total_omzet' => $total_omzet,
    'username' => $user->username,
    'nama' => auth()->user()->name,
    'tanggalCetak' => now('Asia/Makassar')->format('d/m/Y H:i:s'),
    'tanggalAwal' => $tanggalMulai,
    'tanggalAkhir' => $tanggalSelesai,
    'filters' => $filters,
    ])->stream('laporan_omzet.pdf');


}




public function exportOmzetExcel(Request $request)
{
    // ... (logic sebelumnya, tidak perlu diubah, tapi pastikan variabel tanggalnya konsisten)
    $data = \App\Models\BarangKeluar::with(['item', 'lokasi', 'kondisi'])
        ->when($request->lokasi, fn($q) => $q->where('id_lokasi', $request->lokasi))
        ->when($request->kondisi, fn($q) => $q->where('id_kondisi', $request->kondisi))
        ->when($request->filled('search'), fn($q) =>
            $q->whereHas('item', fn($q2) =>
                $q2->where('nama_barang', 'like', "%{$request->search}%")
                   ->orWhere('kode_barang', 'like', "%{$request->search}%")
            )
        )
        ->when($request->filled('start_date') && $request->filled('end_date'), fn($q) =>
            $q->whereBetween('tanggal_keluar', [$request->start_date, $request->end_date])
        )
        ->get()
        ->map(function ($row, $i) {
            return [
                'No'            => $i + 1,
                'Nama Barang'   => $row->item->nama_barang ?? '-',
                'Tanggal'       => $row->tanggal_keluar,
                'Lokasi'        => $row->lokasi->nama_lokasi ?? '-',
                'Kondisi'       => $row->kondisi->nama_kondisi ?? '-',
                'Jumlah Keluar' => $row->jumlah_keluar,
                'Harga Jual'    => $row->harga_jual,
                'Omzet'         => $row->jumlah_keluar * $row->harga_jual,
            ];
        });




    return Excel::download(new OmzetExport($request), 'laporan_omzet.xlsx');
}








    public function exportAsetExcel(Request $request)
    {
    return Excel::download(new LaporanAsetExport($request), 'laporan_aset.xlsx');
    }
















   public function exportAsetPdf(Request $request)
{
    $export = new LaporanAsetExport($request);
    $data = $export->collection();




    // Ambil tanggal berdasarkan transaksi aset
    $itemIds = \App\Models\Item::pluck('kode_barang');
    $tanggalAwalRaw = \App\Models\BarangMasuk::whereIn('kode_barang', $itemIds)->min('tanggal_masuk') ?? now();
    $tanggalAkhirRaw = now();




    // Format tanggal ke dd/mm/YYYY
    $tanggalAwal = \Carbon\Carbon::parse($tanggalAwalRaw)->format('d/m/Y');
    $tanggalAkhir = \Carbon\Carbon::parse($tanggalAkhirRaw)->format('d/m/Y');




    $filters = [
        'Nama Barang'    => $request->nama_barang,
        'Lokasi'         => $request->lokasi,
        'Kondisi'        => $request->kondisi,
        'Tanggal Mulai'  => $tanggalAwal,
        'Tanggal Selesai'=> $tanggalAkhir,
    ];




    return Pdf::loadView('aset.aset_print', [
    'data'         => $data,
    'tanggalCetak' => now()->format('d/m/Y H:i:s'),
    'tanggalAwal'  => $tanggalAwal,
    'tanggalAkhir' => $tanggalAkhir,
    'nama'         => auth()->user()->name,
    'username'     => auth()->user()->username,
    'filters'      => $filters,
    ])->stream('laporan_aset.pdf');


}




}
