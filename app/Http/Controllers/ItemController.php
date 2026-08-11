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
                     ->where('user_id', Auth::id()); // ✅ filter berdasarkan user

        if ($request->filled('search')) {
            $query->where('nama_barang', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kategori')) {
            $query->where('id_kategori', $request->kategori);
        }

        $items = $query->orderBy('created_at', 'desc')
                       ->paginate(10)
                       ->withQueryString();

        $kategoris = Kategori::where('user_id', Auth::id())->get(); // ✅ hanya kategori milik user

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
        $item->user_id     = Auth::id(); // ✅ simpan user id

        if ($request->hasFile('foto')) {
            $item->foto = $request->file('foto')->store('foto_barang', 'public');
        }

        $item->save();

        return redirect()->route('item.index')->with('success', 'Item berhasil ditambahkan.');
    }

    public function edit(Item $item)
    {
        // ✅ pastikan user hanya bisa edit item miliknya
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
            //'nama_barang' => 'required|string|max:255|unique:items,nama_barang,' . $item->kode_barang . ',kode_barang',
            'id_kategori' => 'required|exists:kategoris,id',
            'id_satuan'   => 'required|exists:satuans,id',
            'stok_minimum'=> 'required|integer|min:0',
            'harga_dasar' => 'required|numeric|min:0',
            'deskripsi'   => 'nullable|string|max:500',
            'foto'        => 'nullable|image|max:2048',
        ];

        $messages = [
            //'nama_barang.required' => 'Nama barang wajib diisi.',
            'nama_barang.unique'   => 'Nama barang sudah digunakan.',
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

    public function show($kode_barang)
    {
        $item = Item::with(['kategori', 'satuan'])
                    ->where('kode_barang', $kode_barang)
                    ->where('user_id', Auth::id()) // ✅ hanya data milik user
                    ->firstOrFail();

        return view('item.show', compact('item'));
    }
}
