<?php

namespace App\Http\Controllers;

use App\Models\Pemasok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;

class PemasokController extends Controller
{
    public function index(Request $request)
    {
        $query = Pemasok::query()
            ->where('user_id', Auth::id()); 

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_pemasok', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('no_telepon', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('jenis', 'like', "%{$search}%")
                  ->orWhere('nama_pic', 'like', "%{$search}%")
                  ->orWhere('bergabung_sejak', 'like', "%{$search}%");
            });
        }

        $pemasoks = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        return view('pemasok.index', compact('pemasoks'));
    }

    public function create()
    {
        return view('pemasok.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pemasok'    => 'required|string|max:255|unique:pemasoks,nama_pemasok',
            'email'           => 'required|string|email|max:255',
            'nama_pic'        => 'required|string|max:255',
            'no_telepon'      => 'nullable|string|max:20',
            'alamat'          => 'nullable|string',
            'jenis'           => 'nullable|string|max:50',
            'bergabung_sejak' => 'nullable|date',
        ]);

        Pemasok::create([
            'nama_pemasok'   => $request->nama_pemasok,
            'email'          => $request->email,
            'no_telepon'     => $request->no_telepon,
            'alamat'         => $request->alamat,
            'jenis'          => $request->jenis,
            'bergabung_sejak'=> $request->bergabung_sejak,
            'nama_pic'       => $request->nama_pic,
            'user_id'        => Auth::id(),
        ]);
        
        return redirect()->route('pemasok.index')->with('success', 'Pemasok berhasil ditambahkan');
    }

    // ==========================================
    // FUNGSI AJAX: DISAMAKAN DENGAN MASTER DATA
    // ==========================================
    public function storeAjax(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_pemasok' => 'required|string|max:255|unique:pemasoks,nama_pemasok',
            'nama_pic'     => 'required|string|max:255',
            'email'        => 'required|email|max:255',
        ], [
            'nama_pemasok.required' => 'Nama pemasok wajib diisi.',
            'nama_pemasok.unique'   => 'Pemasok ini sudah terdaftar.',
            'nama_pic.required'     => 'Nama PIC wajib diisi.',
            'email.required'        => 'Email wajib diisi.',
            'email.email'           => 'Format email tidak valid.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 400); 
        }

        try {
            $pemasok = Pemasok::create([
                'nama_pemasok' => $request->nama_pemasok,
                'nama_pic'     => $request->nama_pic,
                'email'        => $request->email,
                'user_id'      => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'data'    => $pemasok
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem di server.'
            ], 500);
        }
    }

    public function edit(Pemasok $pemasok)
    {
        return view('pemasok.edit', compact('pemasok'));
    }

    public function update(Request $request, Pemasok $pemasok)
    {
        $request->validate([
            'nama_pemasok'    => 'required|string|max:255|unique:pemasoks,nama_pemasok,' . $pemasok->id,
            'email'           => 'required|string|email|max:255',
            'nama_pic'        => 'required|string|max:255',
            'no_telepon'      => 'nullable|string|max:20',
            'alamat'          => 'nullable|string',
            'jenis'           => 'nullable|string|max:50',
            'bergabung_sejak' => 'nullable|date',
        ]);

        $pemasok->update($request->only([
            'nama_pemasok',
            'email',
            'no_telepon',
            'alamat',
            'jenis',
            'bergabung_sejak',
            'nama_pic',
        ]));

        return redirect()->route('pemasok.index')->with('success', 'Pemasok berhasil diperbarui');
    }

    public function destroy(Pemasok $pemasok)
    {
        try {
            if (method_exists($pemasok, 'items') && $pemasok->items()->exists()) {
                return back()->with('error', 'Tidak dapat menghapus data karena sudah digunakan untuk pencatatan item.');
            }

            $pemasok->delete();
            return redirect()->route('pemasok.index')->with('success', 'Pemasok berhasil dihapus.');
        } catch (QueryException $e) {
            $mysqlCode   = $e->errorInfo[1] ?? null;
            $sqlState    = $e->errorInfo[0] ?? null;
            $pgSqlCode   = $e->getCode();

            if ($sqlState === '23000' || $mysqlCode == 1451 || $pgSqlCode == '23503') {
                return back()->with('error', 'Tidak dapat menghapus data karena sudah digunakan untuk pencatatan barang masuk/keluar.');
            }

            report($e);
            return back()->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }
}