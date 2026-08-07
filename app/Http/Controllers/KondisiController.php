<?php


namespace App\Http\Controllers;


use App\Models\Kondisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class KondisiController extends Controller
{
    public function index(Request $request)
    {
        $query = Kondisi::query()
            ->where('user_id', Auth::id());

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
            Kondisi::create([
            'nama_kondisi' => $request->nama_kondisi,
            'deskripsi'   => $request->deskripsi,
            'user_id'     => Auth::id(),
        ]);

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
            // Pre-check relasi agar user dapat pesan yang jelas
            $dipakaiDiMasuk  = method_exists($kondisi, 'barangMasuks')   && $kondisi->barangMasuks()->exists();
            $dipakaiDiKeluar = method_exists($kondisi, 'barangKeluars')  && $kondisi->barangKeluars()->exists();

            if ($dipakaiDiMasuk || $dipakaiDiKeluar) {
                return back()->with(
                    'error',
                    'Tidak dapat menghapus data karena sudah digunakan untuk pencatatan barang masuk/keluar.'
                );
            }

            $kondisi->delete();

            return redirect()
                ->route('kondisi.index')
                ->with('success', 'Kondisi berhasil dihapus.');
        } catch (QueryException $e) {
            // FK violation (MySQL: 1451 / SQLSTATE 23000, Postgres: 23503)
            $mysqlCode = $e->errorInfo[1] ?? null; // 1451
            $sqlState  = $e->errorInfo[0] ?? null; // 23000
            $pgCode    = $e->getCode();            // 23503

            if ($sqlState === '23000' || $mysqlCode == 1451 || $pgCode == '23503') {
                return back()->with(
                    'error',
                    'Tidak dapat menghapus data karena sudah digunakan untuk pencatatan barang masuk/keluar.'
                );
            }

            report($e);
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
    
}
