<?php

namespace App\Http\Controllers;

use App\Models\Pemasok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PemasokController extends Controller
{
    public function index(Request $request)
    {
        $query = Pemasok::query()
            ->where('user_id', Auth::id()); // filter hanya data user login

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
            'email'           => 'nullable|string|email|max:255',
            'no_telepon'      => 'nullable|string|max:20',
            'alamat'          => 'nullable|string',
            'jenis'           => 'nullable|string|max:50',
            'bergabung_sejak' => 'nullable|date',
            'nama_pic'        => 'nullable|string|max:255',
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

    /**
     * FUNGSI AJAX: Menangani simpan pemasok baru via Modal di form Barang Masuk
     */
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
            'email'           => 'nullable|string|email|max:255',
            'no_telepon'      => 'nullable|string|max:20',
            'alamat'          => 'nullable|string',
            'jenis'           => 'nullable|string|max:50',
            'bergabung_sejak' => 'nullable|date',
            'nama_pic'        => 'nullable|string|max:255',
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

    public function detailPasokan(Request $request)
    {
        $userId = Auth::id();

        $formattedDateFrom = $request->input('date_from', date('Y-m-01'));
        $formattedDateTo   = $request->input('date_to', date('Y-m-d'));
        $selectedPemasokId = $request->input('pemasok_id');

        $dateFrom = Carbon::parse($formattedDateFrom)->startOfDay();
        $dateTo   = Carbon::parse($formattedDateTo)->endOfDay();

        $listPemasok = DB::table('pemasoks')
            ->where('user_id', '=', $userId)
            ->select('id', 'nama_pemasok')
            ->orderBy('nama_pemasok', 'asc')
            ->get();

        $query = DB::table('pemasoks')
            ->where('pemasoks.user_id', '=', $userId)
            ->leftJoin('barang_masuks', function ($join) use ($userId, $dateFrom, $dateTo) {
                $join->on('pemasoks.id', '=', 'barang_masuks.id_pemasok')
                    ->where('barang_masuks.user_id', '=', $userId)
                    ->where('barang_masuks.tanggal_masuk', '>=', $dateFrom)
                    ->where('barang_masuks.tanggal_masuk', '<=', $dateTo);
            });

        if (!empty($selectedPemasokId)) {
            $query->where('pemasoks.id', '=', $selectedPemasokId);
        }

        $pemasoks = $query->select(
                'pemasoks.id',
                'pemasoks.nama_pemasok',
                'pemasoks.nama_pic',
                'pemasoks.no_telepon',
                'pemasoks.email',
                DB::raw('COUNT(barang_masuks.id) as total_transaksi'),
                DB::raw('IFNULL(SUM(barang_masuks.jumlah), 0) as total_barang_masuk'),
                DB::raw('IFNULL(SUM(barang_masuks.total_harga), 0) as total_nominal')
            )
            ->groupBy('pemasoks.id', 'pemasoks.nama_pemasok', 'pemasoks.nama_pic', 'pemasoks.no_telepon', 'pemasoks.email')
            ->orderByDesc('total_barang_masuk')
            ->get();

        return view('pemasok.detail_pasokan', compact(
            'pemasoks', 
            'listPemasok',
            'selectedPemasokId',
            'formattedDateFrom', 
            'formattedDateTo', 
            'dateFrom', 
            'dateTo'
        ));
    }

    public function exportPdf(Request $request)
    {
        $userId = Auth::id();

        $formattedDateFrom = $request->input('date_from', date('Y-m-01'));
        $formattedDateTo   = $request->input('date_to', date('Y-m-d'));
        $selectedPemasokId = $request->input('pemasok_id');

        $dateFrom = Carbon::parse($formattedDateFrom)->startOfDay();
        $dateTo   = Carbon::parse($formattedDateTo)->endOfDay();

        $query = DB::table('pemasoks')
            ->leftJoin('barang_masuks', function($join) use ($userId, $dateFrom, $dateTo) {
                $join->on('pemasoks.id', '=', 'barang_masuks.id_pemasok')
                ->where('barang_masuks.user_id', '=', $userId)
                ->whereBetween('barang_masuks.tanggal_masuk', [$dateFrom, $dateTo]);
            })
            ->select(
                'pemasoks.nama_pemasok',
                'pemasoks.email',
                'pemasoks.nama_pic as pic',
                'pemasoks.no_telepon as no_hp',
                DB::raw('COUNT(barang_masuks.id) as frekuensi'),
                DB::raw('COALESCE(SUM(barang_masuks.jumlah), 0) as total_barang_masuk'),
                DB::raw('COALESCE(SUM(barang_masuks.total_harga), 0) as total_nominal')
            )
            ->groupBy('pemasoks.id', 'pemasoks.nama_pemasok', 'pemasoks.email', 'pemasoks.nama_pic', 'pemasoks.no_telepon');

        if (!empty($selectedPemasokId)) {
            $query->where('pemasoks.id', '=', $selectedPemasokId);
        }

        $pemasoks = $query->get();

        $pdf = Pdf::loadView('pemasok.pemasok_pdf', [
            'pemasoks' => $pemasoks,
            'dateFrom' => $formattedDateFrom,
            'dateTo'   => $formattedDateTo,
            'printedBy' => Auth::check() ? Auth::user()->name : 'Gudang',
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('pemasok.pemasok_pdf' . date('Ymd_His') . '.pdf');
    }
}