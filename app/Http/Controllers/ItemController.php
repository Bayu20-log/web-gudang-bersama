<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Kategori;
use App\Models\Satuan;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['kategori', 'satuan'])
                     ->withSum('barangMasuk', 'jumlah')
                     ->withSum('barangKeluar', 'jumlah_keluar')
                     ->where('user_id', Auth::id()); 

        if ($request->filled('search')) {
            $query->where('nama_barang', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kategori')) {
            $query->where('id_kategori', $request->kategori);
        }

        // Terbaru di atas. created_at bisa sama persis untuk banyak data (mis. hasil seeder),
        // jadi kode_barang dipakai sebagai pembeda supaya urutannya selalu konsisten (43, 42, 41, ...).
        $items = $query->orderBy('created_at', 'desc')
                       ->orderBy('kode_barang', 'desc')
                       ->paginate(10)
                       ->withQueryString();

        $kategoris = Kategori::where('user_id', Auth::id())->get(); 
        
        return view('item.index', [
            'items' => $items,
            'kategoris' => $kategoris,
        ]);
    }

    public function create()
    {
        return view('item.create', [
            'kategori' => Kategori::where('user_id', Auth::id())->get(), 
            'satuan'   => Satuan::where('user_id', Auth::id())->get(),
        ]);
    }

    public function store(Request $request)
    {
        // Rapikan spasi di awal/akhir supaya "Cup 12oz " dan "Cup 12oz" dianggap sama.
        $request->merge(['nama_barang' => trim((string) $request->nama_barang)]);

        $request->validate($this->rules(), $this->messages());

        $item = new Item();
        $item->kode_barang = Item::generateKodeBarang();
        $item->nama_barang = $request->nama_barang;
        $item->id_kategori = $request->id_kategori;
        $item->id_satuan   = $request->id_satuan;
        $item->stok_minimum= $request->stok_minimum;
        $item->harga_dasar = $request->harga_dasar;
        $item->deskripsi   = $request->deskripsi;
        $item->user_id     = Auth::id(); 

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('foto_barang', 'public_direct');
            $item->foto = $fotoPath;
        }

        try {
            $item->save();
        } catch (QueryException $e) {
            // Jaga-jaga: kalau database masih punya unique index nama_barang (items_nama_barang_unique),
            // jangan tampilkan halaman error 500, cukup beri pesan di form.
            if ($fotoPath) {
                Storage::disk('public_direct')->delete($fotoPath);
            }
            if ($this->isDuplicateNamaBarang($e)) {
                return back()->withInput()->withErrors(['nama_barang' => self::PESAN_NAMA_DUPLIKAT]);
            }
            throw $e;
        }
        
        if ($request->redirect_to == 'barang-masuk') {
            return redirect()->route('barang-masuk.create')->with('success', 'Produk berhasil ditambahkan dan sudah tersedia di pilihan.');
        }
        
        return redirect()->route('item.index')->with('success', 'Item berhasil ditambahkan.');
    }

    public function edit(Item $item)
    {
        if ($item->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        return view('item.edit', [
            'item' => $item,
            'kategori' => Kategori::where('user_id', Auth::id())->get(),
            'satuan'   => Satuan::where('user_id', Auth::id())->get(),
        ]);
    }

    public function update(Request $request, Item $item)
    {
        if ($item->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $request->merge(['nama_barang' => trim((string) $request->nama_barang)]);

        $request->validate($this->rules($item), $this->messages());

        $item->nama_barang = $request->nama_barang;
        $item->id_kategori = $request->id_kategori;
        $item->id_satuan   = $request->id_satuan;
        $item->stok_minimum= $request->stok_minimum;
        $item->harga_dasar = $request->harga_dasar;
        $item->deskripsi   = $request->deskripsi;

        $fotoBaru = null;
        if ($request->hasFile('foto')) {
            $fotoBaru = $request->file('foto')->store('foto_barang', 'public_direct');
            $item->foto = $fotoBaru;
        }

        try {
            $item->save();
        } catch (QueryException $e) {
            if ($fotoBaru) {
                Storage::disk('public_direct')->delete($fotoBaru);
            }
            if ($this->isDuplicateNamaBarang($e)) {
                return back()->withInput()->withErrors(['nama_barang' => self::PESAN_NAMA_DUPLIKAT]);
            }
            throw $e;
        }

        return redirect()->route('item.index')->with('success', 'Item berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        if ($item->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        // Produk yang sudah punya riwayat barang masuk/keluar tidak boleh dihapus.
        // (Foreign key barang_masuks/barang_keluars ke items memakai ON DELETE CASCADE,
        //  jadi tanpa pengecekan ini seluruh riwayat transaksinya ikut terhapus.)
        if ($item->barangMasuk()->exists() || $item->barangKeluar()->exists()) {
            return back()->with(
                'error',
                'Tidak dapat menghapus data karena sudah digunakan untuk pencatatan barang masuk/keluar.'
            );
        }

        try {
            $item->delete();
        } catch (QueryException $e) {
            $sqlState  = $e->errorInfo[0] ?? null;
            $mysqlCode = $e->errorInfo[1] ?? null;

            if ($sqlState === '23000' || $mysqlCode == 1451) {
                return back()->with(
                    'error',
                    'Tidak dapat menghapus data karena sudah digunakan untuk pencatatan barang masuk/keluar.'
                );
            }

            report($e);
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }

        return redirect()->route('item.index')->with('success', 'Item berhasil dihapus.');
    }

    // ==========================================
    // VALIDASI (dipakai store & update)
    // ==========================================
    private const PESAN_NAMA_DUPLIKAT = 'Nama produk sudah ada. Gunakan nama yang berbeda.';

    private function rules(?Item $item = null): array
    {
        $unikNama = Rule::unique('items', 'nama_barang')
            ->where(fn ($q) => $q->where('user_id', Auth::id()));

        if ($item) {
            $unikNama->ignore($item->kode_barang, 'kode_barang');
        }

        return [
            'nama_barang' => ['required', 'string', 'max:255', $unikNama],
            'id_kategori' => 'required|exists:kategoris,id',
            'id_satuan'   => 'required|exists:satuans,id',
            'stok_minimum'=> 'required|integer|min:0',
            'harga_dasar' => 'required|numeric|min:0',
            'deskripsi'   => 'nullable|string|max:500',
            'foto'        => 'nullable|image|max:2048',
        ];
    }

    private function messages(): array
    {
        return [
            'nama_barang.required' => 'Nama produk wajib diisi.',
            'nama_barang.max'      => 'Nama produk maksimal 255 karakter.',
            'nama_barang.unique'   => self::PESAN_NAMA_DUPLIKAT,
            'id_kategori.required' => 'Kategori wajib dipilih.',
            'id_kategori.exists'   => 'Kategori yang dipilih tidak valid.',
            'id_satuan.required'   => 'Satuan wajib dipilih.',
            'id_satuan.exists'     => 'Satuan yang dipilih tidak valid.',
            'stok_minimum.required'=> 'Stok minimum wajib diisi.',
            'stok_minimum.integer' => 'Stok minimum harus berupa bilangan bulat.',
            'stok_minimum.min'     => 'Stok minimum tidak boleh minus.',
            'harga_dasar.required' => 'Harga dasar wajib diisi.',
            'harga_dasar.numeric'  => 'Harga dasar harus berupa angka.',
            'harga_dasar.min'      => 'Harga dasar tidak boleh minus.',
            'deskripsi.max'        => 'Catatan / deskripsi maksimal 500 karakter.',
            'foto.image'           => 'File foto harus berupa gambar.',
            'foto.max'             => 'Ukuran foto maksimal 2MB.',
        ];
    }

    /** True kalau error 1062 (duplicate entry) berasal dari index nama_barang. */
    private function isDuplicateNamaBarang(QueryException $e): bool
    {
        return ($e->errorInfo[1] ?? null) == 1062
            && str_contains($e->getMessage(), 'nama_barang');
    }

    // ==========================================
    // PERBAIKAN MASALAH 5: TAMPILKAN STOK & RIWAYAT
    // ==========================================
    public function show($kode_barang)
    {
        $item = Item::with(['kategori', 'satuan'])
                    ->where('kode_barang', $kode_barang)
                    ->where('user_id', Auth::id()) 
                    ->firstOrFail();

        // Hitung Stok Saat Ini
        $totalMasuk = \App\Models\BarangMasuk::where('kode_barang', $kode_barang)->where('user_id', Auth::id())->sum('jumlah');
        $totalKeluar = \App\Models\BarangKeluar::where('kode_barang', $kode_barang)->where('user_id', Auth::id())->sum('jumlah_keluar');
        $stokSaatIni = $totalMasuk - $totalKeluar;

        // Ambil Riwayat Masuk
        $masuk = \App\Models\BarangMasuk::with(['lokasi', 'user'])
            ->where('kode_barang', $kode_barang)
            ->where('user_id', Auth::id())
            ->get()
            ->map(function($m) {
                return [
                    'tanggal' => $m->tanggal_masuk,
                    'jenis' => 'Masuk',
                    'jumlah' => $m->jumlah,
                    'lokasi' => $m->lokasi->nama_lokasi ?? '-',
                    'user' => $m->user->name ?? $m->user->username ?? '-'
                ];
            });

        // Ambil Riwayat Keluar
        $keluar = \App\Models\BarangKeluar::with(['lokasi', 'user'])
            ->where('kode_barang', $kode_barang)
            ->where('user_id', Auth::id())
            ->get()
            ->map(function($k) {
                return [
                    'tanggal' => $k->tanggal_keluar,
                    'jenis' => 'Keluar',
                    'jumlah' => $k->jumlah_keluar,
                    'lokasi' => $k->lokasi->nama_lokasi ?? '-',
                    'user' => $k->user->name ?? $k->user->username ?? '-'
                ];
            });

        // Gabungkan dan urutkan berdasarkan tanggal terbaru
        $riwayat = $masuk->merge($keluar)->sortByDesc('tanggal')->values();

        return view('item.show', compact('item', 'stokSaatIni', 'riwayat'));
    }
}