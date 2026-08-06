<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Kategori;
use App\Models\Satuan;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['kategori', 'satuan']);

        if ($request->filled('search')) {
            $query->where('nama_barang', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kategori')) {
            $query->where('id_kategori', $request->kategori);
        }

        // Pagination dengan query string
        $items = $query->orderBy('created_at', 'desc')
                       ->paginate(10)
                       ->withQueryString();

        // Ambil kategori yang punya item
        $kategoris = Kategori::whereHas('items')->get();

        return view('item.index', [
            'items' => $items,
            'kategoris' => $kategoris,
        ]);
    }

    public function create()
    {
        return view('item.create', [
            'kategori' => Kategori::all(), // Ambil semua kategori
            'satuan' => Satuan::all(),
        ]);
    }


    public function store(Request $request)
    {
        $rules = [
            'nama_barang' => 'required|string|max:255|unique:items,nama_barang',
            'id_kategori' => 'required|exists:kategoris,id',
            'id_satuan' => 'required|exists:satuans,id',
            'stok_minimum' => 'required|integer|min:0',
            'harga_dasar' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string|max:500',
            'foto' => 'nullable|image|max:2048',
        ];

        $messages = [
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'nama_barang.unique'   => 'Nama barang sudah digunakan, silakan pilih nama lain.',
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
        $item->id_satuan = $request->id_satuan;
        $item->stok_minimum = $request->stok_minimum;
        $item->harga_dasar = $request->harga_dasar;
        $item->deskripsi = $request->deskripsi;

        if ($request->hasFile('foto')) {
            $item->foto = $request->file('foto')->store('foto_barang', 'public');
        }

        $item->save();

        return redirect()->route('item.index')->with('success', 'Item berhasil ditambahkan.');
    }

    public function edit(Item $item)
    {
        return view('item.edit', [
            'item' => $item,
            'kategori' => Kategori::whereHas('items')->get(),
            'satuan' => Satuan::all(),
        ]);
    }

        public function update(Request $request, Item $item)
    {
        $rules = [
            'nama_barang' => 'required|string|max:255|unique:items,nama_barang,' . $item->id,
            'id_kategori' => 'required|exists:kategoris,id',
            'id_satuan' => 'required|exists:satuans,id',
            'stok_minimum' => 'required|integer|min:0',
            'harga_dasar' => 'required|numeric|min:0',
            'deskripsi' => 'nullable|string|max:500',
            'foto' => 'nullable|image|max:2048',
        ];

        $messages = [
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'nama_barang.unique'   => 'Nama barang sudah digunakan, silakan pilih nama lain.',
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
        $item->id_satuan = $request->id_satuan;
        $item->stok_minimum = $request->stok_minimum;
        $item->harga_dasar = $request->harga_dasar;
        $item->deskripsi = $request->deskripsi;

        if ($request->hasFile('foto')) {
            $item->foto = $request->file('foto')->store('foto_barang', 'public');
        }

        $item->save();

        return redirect()->route('item.index')->with('success', 'Item berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return redirect()->route('item.index')->with('success', 'Item berhasil dihapus.');
    }

    public function show($kode_barang)
    {
        $item = Item::with(['kategori', 'satuan'])->findOrFail($kode_barang);
        return view('item.show', compact('item'));
    }
}
