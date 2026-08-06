<?php


namespace App\Http\Controllers;


use App\Models\Satuan;
use Illuminate\Http\Request;


class SatuanController extends Controller
{
    public function index(Request $request)
    {
        $query = Satuan::query();


        if ($request->search) {
            $query->where('nama_satuan', 'like', '%' . $request->search . '%');
        }


        $satuans = $query->paginate(10); // ✅ pakai pagination


        return view('satuan.index', compact('satuans'));
    }


    public function create()
    {
        return view('satuan.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'nama_satuan' => 'required|string|max:255|unique:satuans,nama_satuan',
        ], [
            'nama_satuan.required' => 'Nama satuan wajib diisi.',
            'nama_satuan.unique' => 'Nama satuan ini sudah ada.',
        ]);


        try {
            Satuan::create([
                'nama_satuan' => $request->nama_satuan,
            ]);


            return redirect()->route('satuan.index')->with('success', 'Satuan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }


    public function edit(Satuan $satuan)
    {
        return view('satuan.edit', compact('satuan'));
    }


    public function update(Request $request, Satuan $satuan)
    {
        $request->validate([
            'nama_satuan' => 'required|string|max:255|unique:satuans,nama_satuan,' . $satuan->id,
        ], [
            'nama_satuan.required' => 'Nama satuan wajib diisi.',
            'nama_satuan.unique' => 'Nama satuan ini sudah ada.',
        ]);


        try {
            $satuan->update([
                'nama_satuan' => $request->nama_satuan,
            ]);


            return redirect()->route('satuan.index')->with('success', 'Satuan berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }


    public function destroy(Satuan $satuan)
    {
        try {
            $satuan->delete();
            return redirect()->route('satuan.index')->with('success', 'Satuan berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}


