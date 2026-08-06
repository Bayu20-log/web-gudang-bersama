<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $query = Kategori::query();

        if ($request->search) {
            $query->where('kategori', 'like', '%' . $request->search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
        }

        $kategoris = $query->paginate(10);

        return view('kategori.index', compact('kategoris'));
    }

    public function create()
    {
        return view('kategori.create');
    }

    public function store(Request $request)
    {
        // ✅ Validasi eksplisit gunakan kolom "kategori"
        $request->validate([
            'kategori'  => 'required|unique:kategoris,kategori',
            'deskripsi' => 'nullable|string',
        ], [
            'kategori.required' => 'Field kategori wajib diisi.',
            'kategori.unique'   => 'Field kategori sudah ada.',
        ]);

        try {
            // sementara debug untuk pastikan request masuk
            // dd($request->all());

            Kategori::create($request->only('kategori', 'deskripsi'));

            return redirect()->route('kategori.index')
                ->with('success', 'Kategori berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit(Kategori $kategori)
    {
        return view('kategori.edit', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori)
    {
        $request->validate([
            'kategori'  => 'required|unique:kategoris,kategori,' . $kategori->id,
            'deskripsi' => 'nullable|string',
        ], [
            'kategori.required' => 'Field kategori wajib diisi.',
            'kategori.unique'   => 'Field kategori sudah ada.',
        ]);

        try {
            $kategori->update($request->only('kategori', 'deskripsi'));

            return redirect()->route('kategori.index')
                ->with('success', 'Kategori berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengubah data.');
        }
    }

    public function destroy(Kategori $kategori)
    {
        try {
            $kategori->delete();

            return redirect()->route('kategori.index')
                ->with('success', 'Kategori berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
