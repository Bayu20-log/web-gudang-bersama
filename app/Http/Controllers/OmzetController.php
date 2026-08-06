<?php




namespace App\Http\Controllers;




use Illuminate\Http\Request;
use App\Models\BarangKeluar;
use App\Models\Lokasi;
use App\Models\Kondisi;




class OmzetController extends Controller
{
    public function index(Request $request)
{
    $userId = auth()->id();
    $lokasiId = $request->lokasi;
    $kondisiId = $request->kondisi;
    $search = $request->search; // Menggunakan variabel 'search'
    $startDate = $request->start_date; // Menggunakan variabel yang benar
    $endDate = $request->end_date;   // Menggunakan variabel yang benar
    $sortBy = $request->get('sort_by', 'tanggal');
    $sortDir = $request->get('sort_dir', 'desc');




    $query = BarangKeluar::with(['item', 'lokasi', 'kondisi'])
        ->where('user_id', $userId);




    if ($lokasiId) {
        $query->where('id_lokasi', $lokasiId);
    }




    if ($kondisiId) {
        $query->where('id_kondisi', $kondisiId);
    }




    if ($request->filled('search')) {
        $query->whereHas('item', function($q) use ($search) {
            $q->where('kode_barang', 'like', "%{$search}%")
            ->orWhere('nama_barang', 'like', "%{$search}%");
        });
    }




    if ($request->filled('start_date') && $request->filled('end_date')) {
        $query->whereBetween('tanggal_keluar', [$startDate, $endDate]);
    }




    $results = $query->get();




    $data = $results->map(function ($item) {
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




    $data = $data->sortBy(
        fn($item) => strtolower($item[$sortBy]) ?? '',
        SORT_REGULAR,
        $sortDir === 'desc'
    )->values();




    $total_omzet = $data->sum('omzet_item');




    $lokasiIds = BarangKeluar::where('user_id', $userId)->pluck('id_lokasi')->unique();
    $kondisiIds = BarangKeluar::where('user_id', $userId)->pluck('id_kondisi')->unique();




    $lokasis = Lokasi::whereIn('id', $lokasiIds)->orderBy('nama_lokasi')->get();
    $kondisis = Kondisi::whereIn('id', $kondisiIds)->orderBy('nama_kondisi')->get();




    return view('omzet.index', compact(
        'data', 'lokasis', 'kondisis', 'total_omzet'
    ));
}
}
