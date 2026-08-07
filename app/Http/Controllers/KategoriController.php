<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
USE illuminate\Database\QueryException;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $query = Kategori::query()
             ->where('user_id', Auth::id());

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

            Kategori::create([
                'kategori'  => $request->kategori,
                'deskripsi' => $request->deskripsi,
                'user_id'   => Auth::id(),
            ]);


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

    public function destroy(Kategori $Kategori)
    {
        try {
            // (Opsional tapi rapi) Cek dulu jika punya relasi items()
            if (method_exists($Kategori, 'items') && $Kategori->items()->exists()) {
                return back()->with('error', 'Tidak dapat menghapus data karena sudah digunakan untuk pencatatan item.');
            }

            $Kategori->delete();

            return redirect()->route('Kategori.index')->with('success', 'Kategori berhasil dihapus.');
        } catch (QueryException $e) {
            // Tangani pelanggaran FK: SQLSTATE 23000 / MySQL 1451 / Postgres 23503
            $mysqlCode   = $e->errorInfo[1] ?? null;   // 1451
            $sqlState    = $e->errorInfo[0] ?? null;   // 23000
            $pgSqlCode   = $e->getCode();              // 23503 pada Postgres

            if ($sqlState === '23000' || $mysqlCode == 1451 || $pgSqlCode == '23503') {
                return back()->with('error', 'Tidak dapat menghapus data karena sudah digunakan untuk pencatatan item.');
            }

            // Error lain
            report($e);
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }}
}
