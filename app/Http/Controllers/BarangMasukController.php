<?php




namespace App\Http\Controllers;




use Illuminate\Http\Request;
use App\Models\BarangMasuk;
use App\Models\Item;
use App\Models\Pemasok;
use App\Models\Lokasi;
use App\Models\Kondisi;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Barryvdh\DomPDF\Facade\Pdf;




class BarangMasukController extends Controller
{
    public function index(Request $request)
    {
        $user    = Auth::user();
        $search  = $request->input('search');
        $lokasiId = $request->input('lokasi');




        $barangMasuks = BarangMasuk::with(['item', 'pemasok', 'lokasi', 'kondisi', 'user'])
            ->where('user_id', $user->id)
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('item', fn($qi) => $qi->where('nama_barang', 'like', "%{$search}%"))
                      ->orWhereHas('lokasi', fn($ql) => $ql->where('nama_lokasi', 'like', "%{$search}%"))
                      ->orWhereHas('kondisi', fn($qk) => $qk->where('nama_kondisi', 'like', "%{$search}%"))
                      ->orWhereHas('pemasok', fn($qp) => $qp->where('nama_pemasok', 'like', "%{$search}%"));
                    // (Tidak mengubah logika lain; tetap seperti sebelumnya)
                });
            })
            ->when($lokasiId, fn($q) => $q->where('id_lokasi', $lokasiId))
            ->latest()
            ->paginate(10);
             // supaya pagination mempertahankan filter




        // ✅ Hanya lokasi yang memang muncul pada data Barang Keluar (user ini)
        $lokasiIds = BarangMasuk::where('user_id', auth()->id())
            ->distinct()
            ->pluck('id_lokasi');




        $lokasis = Lokasi::whereIn('id', $lokasiIds)
            ->orderBy('nama_lokasi')
            ->get();






        return view('barangmasuk.index', compact('barangMasuks', 'lokasis'));
    }




    public function create()
    {
        return view('barangmasuk.create', [
            'items' => Item::all(),
            'pemasoks' => Pemasok::all(),
            'lokasis' => Lokasi::all(),
            'kondisis' => Kondisi::all(),
            'users' => User::all()
        ]);
    }




    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'total_harga' => 'nullable|numeric',
            'tanggal_masuk' => 'required|date',
            'tanggal_kadaluarsa' => 'nullable|date|after_or_equal:tanggal_masuk',
            'id_pemasok' => 'required|exists:pemasoks,id',
            'id_lokasi' => 'required|exists:lokasis,id',
            'id_kondisi' => 'required|exists:kondisis,id',
            'catatan' => 'nullable|string|max:1000',
        ]);




        try {
            $total = $validated['jumlah'] * $validated['harga_satuan'];




            $barang = BarangMasuk::create([
                'kode_barang' => $validated['kode_barang'],
                'jumlah' => $validated['jumlah'],
                'harga_satuan' => $validated['harga_satuan'],
                'total_harga' => $total,
                'tanggal_masuk' => $validated['tanggal_masuk'],
                'tanggal_kadaluarsa' => $validated['tanggal_kadaluarsa'],
                'id_pemasok' => $validated['id_pemasok'],
                'id_lokasi' => $validated['id_lokasi'],
                'id_kondisi' => $validated['id_kondisi'],
                'catatan' => $validated['catatan'],
                'user_id' => Auth::id(),
            ]);




            // (Bagian QR & lainnya tetap seperti punyamu; tidak diubah)
            $data = BarangMasuk::with(['item', 'pemasok', 'lokasi', 'kondisi', 'user'])->find($barang->id);




            $qrData = "Kode Barang: {$data->kode_barang}\n"
                    . "Jumlah: {$data->jumlah}\n"
                    . "Harga Satuan: {$data->harga_satuan}\n"
                    . "Total Harga: {$data->total_harga}\n"
                    . "Tanggal Masuk: {$data->tanggal_masuk}\n"
                    . "Tanggal Kadaluarsa: {$data->tanggal_kadaluarsa}\n"
                    . "Pemasok: {$data->pemasok->nama_pemasok}\n"
                    . "Lokasi: {$data->lokasi->nama_lokasi}\n"
                    . "Kondisi: {$data->kondisi->nama_kondisi}\n"
                    . "Catatan: {$data->catatan}\n"
                    . "Petugas: {$data->user->name}";




            $qrPath = 'qrcodes/' . $barang->kode_barang . '.png';
            $qrCode = QrCode::format('png')->size(300)->margin(2)->generate($qrData);
            Storage::disk('public')->put($qrPath, $qrCode);




            $barang->update(['qr_code' => $qrPath]);




            return redirect()->route('barang-masuk.index')->with('success', 'Barang masuk berhasil ditambahkan beserta QR Code.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }




    public function edit($id)
    {
        return view('barangmasuk.edit', [
            'barangmasuk' => BarangMasuk::findOrFail($id),
            'items' => Item::all(),
            'pemasoks' => Pemasok::all(),
            'lokasis' => Lokasi::all(),
            'kondisis' => Kondisi::all(),
        ]);
    }




    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_barang' => 'required|exists:items,kode_barang',
            'jumlah' => 'required|integer|min:1',
            'harga_satuan' => 'required|numeric|min:0',
            'tanggal_masuk' => 'required|date',
            'tanggal_kadaluarsa' => 'required|date|after_or_equal:tanggal_masuk',
            'id_pemasok' => 'required|exists:pemasoks,id',
            'id_lokasi' => 'required|exists:lokasis,id',
            'id_kondisi' => 'required|exists:kondisis,id',
            'catatan' => 'nullable|string',
        ]);




        $barang = BarangMasuk::findOrFail($id);




        $barang->update([
            'kode_barang' => $request->kode_barang,
            'jumlah' => $request->jumlah,
            'harga_satuan' => $request->harga_satuan,
            'total_harga' => $request->jumlah * $request->harga_satuan,
            'tanggal_masuk' => $request->tanggal_masuk,
            'tanggal_kadaluarsa' => $request->tanggal_kadaluarsa,
            'id_pemasok' => $request->id_pemasok,
            'id_lokasi' => $request->id_lokasi,
            'id_kondisi' => $request->id_kondisi,
            'catatan' => $request->catatan,
        ]);




        return redirect()->route('barang-masuk.index')->with('success', 'Data barang masuk berhasil diperbarui.');
    }




    public function destroy($id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);




        if ($barangMasuk->qr_code && Storage::disk('public')->exists($barangMasuk->qr_code)) {
            Storage::disk('public')->delete($barangMasuk->qr_code);
        }




        $barangMasuk->delete();




        return redirect()->route('barang-masuk.index')->with('success', 'Data barang masuk berhasil dihapus.');
    }




    public function show($id)
    {
        $barangMasuk = BarangMasuk::with(['item', 'pemasok', 'lokasi', 'kondisi', 'user'])->findOrFail($id);
        return view('barangmasuk.show', compact('barangMasuk'));
    }




    public function qrCard($id)
    {
        $barangMasuk = BarangMasuk::with(['item', 'pemasok', 'lokasi', 'kondisi', 'user'])->findOrFail($id);
        return view('barangmasuk.qr_card', compact('barangMasuk'));
    }




    public function cetakPDF($id)
    {
        $barangMasuk = BarangMasuk::with(['item', 'kondisi', 'user'])->findOrFail($id);




        $qrPath = $barangMasuk->qr_code && Storage::disk('public')->exists($barangMasuk->qr_code)
            ? public_path('storage/' . $barangMasuk->qr_code)
            : null;




        $pdf = Pdf::loadView('barangmasuk.qr_card_pdf', compact('barangMasuk', 'qrPath'))
                ->setPaper('A4', 'portrait');




        return $pdf->download('detail_barang_masuk_' . $barangMasuk->kode_barang . '.pdf');
    }




    public function cetakBeritaAcara($id)
    {
        $barangMasuk = BarangMasuk::with(['item', 'lokasi', 'kondisi', 'user'])->findOrFail($id);




        $pdf = PDF::loadView('barangmasuk.berita_acara_pdf', [
            'barangMasuk' => $barangMasuk,
            'tanggal_lengkap' => now()->translatedFormat('d F Y'),
            'hari' => now()->translatedFormat('l'),
            'bulan' => now()->format('m'),
            'tahun' => now()->format('Y'),
            'nomor' => str_pad($barangMasuk->id, 3, '0', STR_PAD_LEFT),
            'lokasi' => $barangMasuk->lokasi->nama_lokasi ?? '-',
        ])->setPaper('A4', 'portrait');




        return $pdf->stream('berita_acara_barang_masuk.pdf');
    }




        // Fungsi Cetak QR Kecil
    public function cetakQRKecil($id)
    {
        $barangMasuk = BarangMasuk::findOrFail($id);


        // Ambil path QR yang sudah tersimpan
        $qrPath = $barangMasuk->qr_code && Storage::disk('public')->exists($barangMasuk->qr_code)
            ? public_path('storage/' . $barangMasuk->qr_code)
            : null;


        // Ukuran A7: 74mm × 105mm dalam point (1mm ≈ 2.83465pt)
        $a7Width  = 74 * 2.83465;
        $a7Height = 105 * 2.83465;
        $customPaper = array(0, 0, $a7Width, $a7Height);


        $pdf = Pdf::loadView('barangmasuk.qr_only_pdf', [
            'barangMasuk' => $barangMasuk,
            'qrPath' => $qrPath
        ])->setPaper($customPaper, 'portrait');


        return $pdf->download('QR_' . $barangMasuk->kode_barang . '.pdf');
    }






}
