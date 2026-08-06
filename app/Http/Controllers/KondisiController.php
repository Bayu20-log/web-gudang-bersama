<?php


namespace App\Http\Controllers;


use App\Models\Kondisi;
use Illuminate\Http\Request;


class KondisiController extends Controller
{
    public function index(Request $request)
    {
        $query = Kondisi::query();


        if ($request->search) {
            $query->where('nama_kondisi', 'like', '%' . $request->search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
        }


        // ✅ Tambah pagination
        $kondisis = $query->paginate(10);


        return view('kondisi.index', compact('kondisis'));
    }


    public function create()
    {
        return view('kondisi.create');
    }


    public function store(Request $request)
    {
        // ✅ Validasi input dan beri pesan khusus
        $request->validate([
            'nama_kondisi' => 'required|unique:kondisis',
            'deskripsi' => 'nullable',
        ], [
            'nama_kondisi.required' => 'Nama kondisi wajib diisi.',
            'nama_kondisi.unique' => 'Nama kondisi ini sudah ada.',
        ]);


        try {
            Kondisi::create($request->only('nama_kondisi', 'deskripsi'));
            return redirect()->route('kondisi.index')->with('success', 'Data berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }


    public function edit(Kondisi $kondisi)
    {
        return view('kondisi.edit', compact('kondisi'));
    }


    public function update(Request $request, Kondisi $kondisi)
    {
        // ✅ Validasi update (dengan pengecualian id sendiri)
        $request->validate([
            'nama_kondisi' => 'required|unique:kondisis,nama_kondisi,' . $kondisi->id,
            'deskripsi' => 'nullable',
        ], [
            'nama_kondisi.required' => 'Nama kondisi wajib diisi.',
            'nama_kondisi.unique' => 'Nama kondisi ini sudah ada.',
        ]);


        try {
            $kondisi->update($request->only('nama_kondisi', 'deskripsi'));
            return redirect()->route('kondisi.index')->with('success', 'Data berhasil diubah.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat mengubah data.');
        }
    }


    public function destroy(Kondisi $kondisi)
    {
        try {
            $kondisi->delete();
            return redirect()->route('kondisi.index')->with('success', 'Kondisi berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}
