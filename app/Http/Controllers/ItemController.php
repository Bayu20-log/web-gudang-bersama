<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Kategori;
use App\Models\Satuan;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['kategori', 'satuan'])
                     ->where('user_id', Auth::id()); 

        if ($request->filled('search')) {
            $query->where('nama_barang', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kategori')) {
            $query->where('id_kategori', $request->kategori);
        }

        $items = $query->orderBy('created_at', 'desc')
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
        $rules = [
            'nama_barang' => 'required|string|max:255',
            'id_kategori' => 'required|exists:kategoris,id',
            'id_satuan'   => 'required|exists:satuans,id',
            'stok_minimum'=> 'required|integer|min:0',
            'harga_dasar' => 'required|numeric|min:0',
            'deskripsi'   => 'nullable|string|max:500',
            'foto'        => 'nullable|image|max:2048',
        ];

        $messages = [
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'id_kategori.required' => 'Kategori wajib dipilih.',
            'id_satuan.required'   => 'Satuan wajib dipilih.',
            'stok_minimum.required'=> 'Stok minimum wajib diisi.',
            'harga_dasar.required' => 'Harga dasar wajib diisi.',
            'foto.image'           => 'File foto harus berupa gambar.',
            'foto.max'             => 'Ukuran foto maksimal 2MB.',
        ];

        $request->validate($rules, $messages);

        $item = new Item();
        $item->kode_barang = Item::generateKodeBarang();
        $item->nama_barang = $request->nama_barang;
        $item->id_kategori = $request->id_kategori;
        $item->id_satuan   = $request->id_satuan;
        $item->stok_minimum= $request->stok_minimum;
        $item->harga_dasar = $request->harga_dasar;
        $item->deskripsi   = $request->deskripsi;
        $item->user_id     = Auth::id(); 

        if ($request->hasFile('foto')) {
            $item->foto = $request->file('foto')->store('foto_barang', 'public');
        }

        $item->save();
        
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

        $rules = [
            'id_kategori' => 'required|exists:kategoris,id',
            'id_satuan'   => 'required|exists:satuans,id',
            'stok_minimum'=> 'required|integer|min:0',
            'harga_dasar' => 'required|numeric|min:0',
            'deskripsi'   => 'nullable|string|max:500',
            'foto'        => 'nullable|image|max:2048',
        ];

        $messages = [
            'id_kategori.required' => 'Kategori wajib dipilih.',
            'id_satuan.required'   => 'Satuan wajib dipilih.',
            'stok_minimum.required'=> 'Stok minimum wajib diisi.',
            'harga_dasar.required' => 'Harga dasar wajib diisi.',
            'foto.image'           => 'File foto harus berupa gambar.',
            'foto.max'             => 'Ukuran foto maksimal 2MB.',
        ];

        $request->validate($rules, $messages);

        $item->nama_barang = $request->nama_barang;
        $item->id_kategori = $request->id_kategori;
        $item->id_satuan   = $request->id_satuan;
        $item->stok_minimum= $request->stok_minimum;
        $item->harga_dasar = $request->harga_dasar;
        $item->deskripsi   = $request->deskripsi;

        if ($request->hasFile('foto')) {
            $item->foto = $request->file('foto')->store('foto_barang', 'public');
        }

        $item->save();

        return redirect()->route('item.index')->with('success', 'Item berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        if ($item->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
        $item->delete();
        return redirect()->route('item.index')->with('success', 'Item berhasil dihapus.');
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