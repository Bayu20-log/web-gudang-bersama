<?php


namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use App\Models\Notification;
use Carbon\Carbon;


class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }


    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();


        $user = $request->user();


        if ($user->role === 'gudang') {
            $userId = auth()->id();


            /** 1) Barang Kadaluarsa (H-30) — per USER */
            $expiredSoonItems = DB::table('barang_masuks')
                ->join('items', 'barang_masuks.kode_barang', '=', 'items.kode_barang')
                ->join('lokasis', 'barang_masuks.id_lokasi', '=', 'lokasis.id')
                ->join('kondisis', 'barang_masuks.id_kondisi', '=', 'kondisis.id')
                ->where('barang_masuks.user_id', $userId) // <— penting
                ->whereDate('barang_masuks.tanggal_kadaluarsa', '<=', now()->addDays(30))
                ->whereDate('barang_masuks.tanggal_kadaluarsa', '>=', now())
                ->select(
                    'items.nama_barang',
                    'barang_masuks.tanggal_kadaluarsa',
                    'lokasis.nama_lokasi as lokasi',
                    'kondisis.nama_kondisi as kondisi'
                )
                ->get();


            foreach ($expiredSoonItems as $item) {
                Notification::firstOrCreate(
                    [
                        'user_id' => $userId,
                        'message' => "{$item->nama_barang} kondisi {$item->kondisi} di {$item->lokasi} akan kadaluarsa pada {$item->tanggal_kadaluarsa}.",
                        'type'    => 'expired_soon',
                    ],
                    ['is_read' => false]
                );
            }


            /** 2) Stok Minimum — per USER */
            $lowStockItems = DB::table('items')
                ->select(
                    'items.kode_barang',
                    'items.nama_barang',
                    'items.stok_minimum'
                )
                ->selectRaw('
                    (SELECT COALESCE(SUM(jumlah),0)
                     FROM barang_masuks
                     WHERE barang_masuks.kode_barang = items.kode_barang
                       AND barang_masuks.user_id = ?) AS total_masuk,
                    (SELECT COALESCE(SUM(jumlah_keluar),0)
                     FROM barang_keluars
                     WHERE barang_keluars.kode_barang = items.kode_barang
                       AND barang_keluars.user_id = ?) AS total_keluar
                ', [$userId, $userId])
                ->get();


            foreach ($lowStockItems as $item) {
                $stok_akhir = (int)$item->total_masuk - (int)$item->total_keluar;


                if ((int)$item->total_masuk > 0 && $stok_akhir <= (int)$item->stok_minimum) {
                    $latestData = DB::table('barang_masuks')
                        ->join('lokasis', 'barang_masuks.id_lokasi', '=', 'lokasis.id')
                        ->join('kondisis', 'barang_masuks.id_kondisi', '=', 'kondisis.id')
                        ->where('barang_masuks.kode_barang', $item->kode_barang)
                        ->where('barang_masuks.user_id', $userId) // <— penting
                        ->latest('barang_masuks.tanggal_masuk')
                        ->select('lokasis.nama_lokasi', 'kondisis.nama_kondisi')
                        ->first();


                    $lokasi  = $latestData->nama_lokasi  ?? '-';
                    $kondisi = $latestData->nama_kondisi ?? '-';


                    Notification::firstOrCreate(
                        [
                            'user_id' => $userId,
                            'message' => "{$item->nama_barang} kondisi {$kondisi} di {$lokasi} sudah mencapai batas minimum (stok: {$stok_akhir}).",
                            'type'    => 'low_stock',
                        ],
                        ['is_read' => false]
                    );
                }
            }


            /** 3) Slow Moving (> 60 hari tanpa pergerakan) — per USER */
            $slowMovingItems = DB::table('items')
                ->select(
                    'items.kode_barang',
                    'items.nama_barang'
                )
                ->selectRaw('
                    (SELECT MAX(tanggal_masuk)
                     FROM barang_masuks
                     WHERE barang_masuks.kode_barang = items.kode_barang
                       AND barang_masuks.user_id = ?) AS last_in,
                    (SELECT MAX(tanggal_keluar)
                     FROM barang_keluars
                     WHERE barang_keluars.kode_barang = items.kode_barang
                       AND barang_keluars.user_id = ?) AS last_out
                ', [$userId, $userId])
                ->get();


            foreach ($slowMovingItems as $item) {
                $lastIn  = $item->last_in  ? Carbon::parse($item->last_in)   : null;
                $lastOut = $item->last_out ? Carbon::parse($item->last_out)  : null;


                if (!$lastIn && !$lastOut) {
                    continue;
                }


                $lastMovement = $lastIn && $lastOut ? max($lastIn, $lastOut) : ($lastIn ?: $lastOut);


                if ($lastMovement->diffInDays(now()) > 60) {
                    $latestData = DB::table('barang_masuks')
                        ->join('lokasis', 'barang_masuks.id_lokasi', '=', 'lokasis.id')
                        ->join('kondisis', 'barang_masuks.id_kondisi', '=', 'kondisis.id')
                        ->where('barang_masuks.kode_barang', $item->kode_barang)
                        ->where('barang_masuks.user_id', $userId) // <— penting
                        ->latest('barang_masuks.tanggal_masuk')
                        ->select('lokasis.nama_lokasi', 'kondisis.nama_kondisi')
                        ->first();


                    $lokasi  = $latestData->nama_lokasi  ?? '-';
                    $kondisi = $latestData->nama_kondisi ?? '-';


                    Notification::firstOrCreate(
                        [
                            'user_id' => $userId,
                            'message' => "{$item->nama_barang} kondisi {$kondisi} di {$lokasi} tidak bergerak lebih dari 60 hari.",
                            'type'    => 'slow_moving',
                        ],
                        ['is_read' => false]
                    );
                }
            }
        }


        $request->session()->put('show_notification_popup', true);
        return redirect()->intended(RouteServiceProvider::redirectByRole());
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();


        $request->session()->invalidate();
        $request->session()->regenerateToken();


        return redirect('/');
    }
}
