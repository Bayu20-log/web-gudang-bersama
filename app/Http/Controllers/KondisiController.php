<?php

namespace App\Http\Controllers;

use App\Models\Kondisi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

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

        $kondisis = $query->paginate(10);
        return view('kondisi.index', compact('kondisis'));
    }

    public function create()
    {
        return view('kondisi.create');
    }

    public function store(Request $request)
    {
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

    // ==========================================
    // FUNGSI BARU UNTUK AJAX QUICK-ADD
    // ==========================================
    public function storeAjax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_kondisi' => 'required|unique:kondisis,nama_kondisi',
            'deskripsi'    => 'nullable|string',
        ], [
            'nama_kondisi.required' => 'Nama kondisi wajib diisi.',
            'nama_kondisi.unique'   => 'Kondisi ini sudah ada di database.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400); 
        }

        try {
            $kondisi = Kondisi::create([
                'nama_kondisi' => $request->nama_kondisi,
                'deskripsi'    => $request->deskripsi,
                'user_id'      => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'data'    => $kondisi
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem di server.'
            ], 500);
        }
    }

    public function edit(Kondisi $kondisi)
    {
        return view('kondisi.edit', compact('kondisi'));
    }

    public function update(Request $request, Kondisi $kondisi)
    {
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
            $mysqlCode = $e->errorInfo[1] ?? null; 
            $sqlState  = $e->errorInfo[0] ?? null; 
            $pgCode    = $e->getCode();            

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