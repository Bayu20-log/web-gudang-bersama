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
                });
            })
            ->when($lokasiId, fn($q) => $q->where('id_lokasi', $lokasiId))
            ->latest()
            ->paginate(10);

        $lokasiIds = BarangMasuk::where('user_id', auth()->id())
            ->distinct()
            ->pluck('id_lokasi');

        $lokasis = Lokasi::whereIn('id', $lokasiIds)
            ->orderBy('nama_lokasi')
            ->get();

        return view('barangmasuk.index', compact('barangMasuks', 'lokasis'));
    }




    public function create(Request $request)
    {

        $selectedItemId = $request->query('item.id');

        $selectedKodeBarang = $request->query('kode_barang');

        $barangs = Item::all();

        return view('barangmasuk.create', [
            'items' => Item::where('user_id', Auth::id())->get(),
            'pemasoks' => Pemasok::where('user_id', Auth::id())->get(),
            'lokasis' => Lokasi::where('user_id', Auth::id())->get(),
            'kondisis' => Kondisi::where('user_id', Auth::id())->get(),
            'users' => User::all(),
            'selectedItemId' => $selectedItemId,
            'selectedKodeBarang' => $selectedKodeBarang,
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
            'tanggal_kadaluarsa' => 'required|date|after_or_equal:tanggal_masuk',
            'id_pemasok' => 'required|exists:pemasoks,id',
            'id_lokasi' => 'required|exists:lokasis,id',
            'id_kondisi' => 'required|exists:kondisis,id',
            'catatan' => 'nullable|string|max:1000',
        ], $this->pesanValidasi());

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

            $data = BarangMasuk::with(['item', 'pemasok', 'lokasi', 'kondisi', 'user'])->find($barang->id);
            $qrData = "Kode Barang: {$data->kode_barang}\n"
                    . "Nama Barang: {$data->item->nama_barang}\n"
                    . "Kondisi: {$data->kondisi->nama_kondisi}";

            // PERBAIKAN IMAGICK: Gunakan format SVG
            $qrPath = 'qrcodes/' . $barang->kode_barang . '.svg';
            $qrCode = QrCode::format('svg')->size(300)->margin(2)->generate($qrData);
            
            Storage::disk('public_direct')->put($qrPath, $qrCode);
            $barang->update(['qr_code' => $qrPath]);

            $pesanSukses = 'Barang masuk berhasil ditambahkan beserta QR Code.';

            // Evaluasi ulang status stok barang ini supaya notifikasi langsung muncul (tanpa email).
            try {
                $hasil = app(\App\Services\AdaptiveThresholdService::class)->evaluateWithoutEmail((string) $validated['kode_barang'], 'transaksi');

                // Kalau status stok berubah karena barang masuk ini, beri tahu user langsung
                // di pesan sukses (mis. "Status stok Beans berubah: Habis → Aman.").
                if (!empty($hasil['changed']) && !empty($hasil['previous_status'])) {
                    $pesanSukses .= ' Status stok ' . ($data->item->nama_barang ?? $validated['kode_barang'])
                        . ' berubah: ' . ucfirst($hasil['previous_status'])
                        . ' → ' . ucfirst($hasil['status']) . '.';
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Evaluasi otomatis gagal: ' . $e->getMessage());
            }

            return redirect()->route('barang-masuk.index')->with('success', $pesanSukses);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    /** Pesan validasi (Bahasa Indonesia) untuk form barang masuk. */
    private function pesanValidasi(): array
    {
        return [
            'jumlah.required'       => 'Jumlah wajib diisi.',
            'jumlah.integer'        => 'Jumlah harus berupa bilangan bulat.',
            'jumlah.min'            => 'Jumlah tidak boleh kurang dari 1.',
            'harga_satuan.required' => 'Harga beli wajib diisi.',
            'harga_satuan.numeric'  => 'Harga beli harus berupa angka.',
            'harga_satuan.min'      => 'Harga beli tidak boleh minus.',
        ];
    }

    public function edit($id)
    {
        return view('barangmasuk.edit', [
            'barangmasuk' => BarangMasuk::findOrFail($id),
            'items' => Item::where('user_id', Auth::id())->get(),
            'pemasoks' => Pemasok::where('user_id', Auth::id())->get(),
            'lokasis' => Lokasi::where('user_id', Auth::id())->get(),
            'kondisis' => Kondisi::where('user_id', Auth::id())->get(),
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
        ], $this->pesanValidasi());

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
        
        if ($barangMasuk->qr_code && Storage::disk('public_direct')->exists($barangMasuk->qr_code)) {
            Storage::disk('public_direct')->delete($barangMasuk->qr_code);
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
        
        $qrBase64 = null;
        if ($barangMasuk->qr_code && Storage::disk('public_direct')->exists($barangMasuk->qr_code)) {
            $qrContent = Storage::disk('public_direct')->get($barangMasuk->qr_code);
            // Sesuaikan base64 header untuk SVG
            $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrContent);
        }

        $pdf = Pdf::loadView('barangmasuk.qr_card_pdf', compact('barangMasuk', 'qrBase64'))
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

    public function cetakQRKecil($id)
    {
        $barangMasuk = BarangMasuk::with(['item'])->findOrFail($id);
        
        $qrBase64 = null;
        if ($barangMasuk->qr_code && Storage::disk('public_direct')->exists($barangMasuk->qr_code)) {
            $qrContent = Storage::disk('public_direct')->get($barangMasuk->qr_code);
            // Sesuaikan base64 header untuk SVG
            $qrBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrContent);
        }

        $pdf = Pdf::loadView('barangmasuk.qr_only_pdf', compact('barangMasuk', 'qrBase64'))
                ->setPaper('A7', 'portrait'); 

        return $pdf->download('qr_kecil_' . $barangMasuk->kode_barang . '.pdf');
    }

    /**
     * Pesan validasi form Barang Masuk dalam bahasa Indonesia
     * (bawaan Laravel berbahasa Inggris, mis. "The tanggal kadaluarsa must be ...").
     */
    private function pesanValidasi(): array
    {
        return [
            'kode_barang.required'             => 'Pilih barang terlebih dahulu.',
            'kode_barang.exists'               => 'Barang tidak ditemukan.',
            'jumlah.required'                  => 'Jumlah wajib diisi.',
            'jumlah.integer'                   => 'Jumlah harus berupa angka bulat.',
            'jumlah.min'                       => 'Jumlah minimal 1.',
            'harga_satuan.required'            => 'Harga satuan wajib diisi.',
            'harga_satuan.numeric'             => 'Harga satuan harus berupa angka.',
            'harga_satuan.min'                 => 'Harga satuan tidak boleh kurang dari 0.',
            'tanggal_masuk.required'           => 'Tanggal masuk wajib diisi.',
            'tanggal_masuk.date'               => 'Format tanggal masuk tidak valid.',
            'tanggal_kadaluarsa.required'      => 'Tanggal kadaluarsa wajib diisi.',
            'tanggal_kadaluarsa.date'          => 'Format tanggal kadaluarsa tidak valid.',
            'tanggal_kadaluarsa.after_or_equal'=> 'Tanggal kadaluarsa tidak boleh sebelum tanggal masuk.',
            'id_pemasok.required'              => 'Pilih pemasok terlebih dahulu.',
            'id_pemasok.exists'                => 'Pemasok tidak ditemukan.',
            'id_lokasi.required'               => 'Pilih lokasi terlebih dahulu.',
            'id_lokasi.exists'                 => 'Lokasi tidak ditemukan.',
            'id_kondisi.required'              => 'Pilih kondisi terlebih dahulu.',
            'id_kondisi.exists'                => 'Kondisi tidak ditemukan.',
            'catatan.max'                      => 'Catatan maksimal 1000 karakter.',
        ];
    }
}
