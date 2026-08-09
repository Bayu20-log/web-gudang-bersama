<?php


namespace App\Http\Controllers;


use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class LokasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Lokasi::query()
            ->where('user_id', Auth::id());


        if ($request->search) {
            $query->where('nama_lokasi', 'like', '%' . $request->search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
        }


        $lokasis = $query->paginate(10); // ✅ gunakan pagination


        return view('lokasi.index', compact('lokasis'));
    }


    public function create()
    {
        return view('lokasi.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'nama_lokasi' => 'required|unique:lokasis,nama_lokasi',
            'deskripsi' => 'nullable|string',
        ], [
            'nama_lokasi.required' => 'Nama lokasi wajib diisi.',
            'nama_lokasi.unique' => 'Nama lokasi ini sudah ada.',
        ]);


        try {
            Lokasi::create([
                'nama_lokasi'  => $request->nama_lokasi,
                'deskripsi' => $request->deskripsi,
                'user_id'   => Auth::id(),
            ]);

            return redirect()->route('lokasi.index')->with('success', 'Lokasi berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }


    public function edit(Lokasi $lokasi)
    {
        return view('lokasi.edit', compact('lokasi'));
    }


    public function update(Request $request, Lokasi $lokasi)
    {
        $request->validate([
            'nama_lokasi' => 'required|unique:lokasis,nama_lokasi,' . $lokasi->id,
            'deskripsi' => 'nullable|string',
        ], [
            'nama_lokasi.required' => 'Nama lokasi wajib diisi.',
            'nama_lokasi.unique' => 'Nama lokasi ini sudah ada.',
        ]);


        try {
            $lokasi->update($request->only('nama_lokasi', 'deskripsi'));
            return redirect()->route('lokasi.index')->with('success', 'Lokasi berhasil diperbarui.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }


    public function destroy(Lokasi $lokasi)
    {
        try {
            $lokasi->delete();
            return redirect()->route('lokasi.index')->with('success', 'Lokasi berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Tidak dapat menghapus data karena sudah digunakan untuk pencatatan barang masuk/keluar.');
        }
    }
}
