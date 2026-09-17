<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class SatuanController extends Controller
{
    public function index(Request $request)
    {
        $query = Satuan::query()
            ->where('user_id', Auth::id()); 

        if ($request->search) {
            $query->where('nama_satuan', 'like', '%' . $request->search . '%');
        }

        $satuans = $query->paginate(10);
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
                'user_id' => Auth::id(), 
            ]);

            return redirect()->route('satuan.index')->with('success', 'Satuan berhasil ditambahkan.');
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    // ==========================================
    // FUNGSI BARU UNTUK MENERIMA AJAX DARI MODAL
    // ==========================================
    public function storeAjax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_satuan' => 'required|string|max:255|unique:satuans,nama_satuan',
        ], [
            'nama_satuan.required' => 'Nama satuan wajib diisi.',
            'nama_satuan.unique'   => 'Satuan ini sudah ada di database.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400); 
        }

        try {
            $satuan = Satuan::create([
                'nama_satuan' => $request->nama_satuan,
                'user_id'     => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'data'    => $satuan
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem di server.'
            ], 500);
        }
    }

    public function edit(Satuan $satuan)
    {
        if ($satuan->user_id !== Auth::id()) {
            return redirect()->route('satuan.index')->with('error', 'Anda tidak berhak mengedit data ini.');
        }
        return view('satuan.edit', compact('satuan'));
    }

    public function update(Request $request, Satuan $satuan)
    {
        if ($satuan->user_id !== Auth::id()) {
            return redirect()->route('satuan.index')->with('error', 'Anda tidak berhak mengedit data ini.');
        }

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
            if (method_exists($satuan, 'items') && $satuan->items()->exists()) {
                return back()->with('error', 'Tidak dapat menghapus data karena sudah digunakan untuk pencatatan item.');
            }

            $satuan->delete();
            return redirect()->route('satuan.index')->with('success', 'Satuan berhasil dihapus.');
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