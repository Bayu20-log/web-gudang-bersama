<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

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
        $request->validate([
            'kategori'  => 'required|unique:kategoris,kategori',
            'deskripsi' => 'nullable|string',
        ], [
            'kategori.required' => 'Field kategori wajib diisi.',
            'kategori.unique'   => 'Field kategori sudah ada.',
        ]);

        try {
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

    // ==========================================
    // FUNGSI BARU UNTUK MENERIMA AJAX DARI MODAL
    // DENGAN PENAMBAHAN DESKRIPSI
    // ==========================================
    public function storeAjax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'kategori'  => 'required|unique:kategoris,kategori',
            'deskripsi' => 'nullable|string',
        ], [
            'kategori.required' => 'Nama kategori wajib diisi.',
            'kategori.unique'   => 'Kategori ini sudah ada di database.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400); 
        }

        try {
            $kategori = Kategori::create([
                'kategori'  => $request->kategori,
                'deskripsi' => $request->deskripsi,
                'user_id'   => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'data'    => $kategori
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem di server.'
            ], 500);
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
            if (method_exists($kategori, 'items') && $kategori->items()->exists()) {
                return back()->with('error', 'Tidak dapat menghapus data karena sudah digunakan untuk pencatatan item.');
            }

            $kategori->delete();
            return redirect()->route('kategori.index')->with('success', 'Kategori berhasil dihapus.');
        } catch (QueryException $e) {
            $mysqlCode   = $e->errorInfo[1] ?? null;   
            $sqlState    = $e->errorInfo[0] ?? null;   
            $pgSqlCode   = $e->getCode();              

            if ($sqlState === '23000' || $mysqlCode == 1451 || $pgSqlCode == '23503') {
                return back()->with('error', 'Tidak dapat menghapus data karena sudah digunakan untuk pencatatan item.');
            }

            report($e);
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}