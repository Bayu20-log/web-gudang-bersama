<?php

namespace App\Http\Controllers;

use App\Services\AdaptiveThresholdService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockNotificationController extends Controller
{
    protected AdaptiveThresholdService $service;

    public function __construct(AdaptiveThresholdService $service)
    {
        $this->service = $service;
    }

    /**
     * Menampilkan daftar status stok semua item + riwayat notifikasi.
     */
    public function index()
    {
        // Ambil semua item milik user yang sedang login, beserta info kategori/satuan
        $items = DB::table('items')
            ->where('user_id', auth()->id())
            ->orderBy('nama_barang')
            ->get();

        // Untuk setiap item, hitung status stok terkini + threshold-nya
        $itemStatuses = $items->map(function ($item) {
            $stok = $this->service->getCurrentStock((string) $item->kode_barang);
            $status = $this->service->classifyStatus((string) $item->kode_barang);
            $threshold = DB::table('stock_thresholds')
                ->where('item_id', $item->kode_barang)
                ->first();

            return (object) [
                'kode_barang'       => $item->kode_barang,
                'nama_barang'       => $item->nama_barang,
                'stok'              => $stok,
                'status'            => $status,
                'low_threshold'     => $threshold->low_threshold ?? null,
                'critical_threshold' => $threshold->critical_threshold ?? null,
            ];
        });

        // Ambil riwayat notifikasi terbaru (20 terakhir), gabung dengan nama barang
        $notifications = DB::table('stock_notifications')
            ->join('items', 'items.kode_barang', '=', 'stock_notifications.item_id')
            ->where('items.user_id', auth()->id())
            ->orderByDesc('stock_notifications.created_at')
            ->limit(20)
            ->select('stock_notifications.*', 'items.nama_barang')
            ->get();

        return view('stock-notifications.index', [
            'itemStatuses'  => $itemStatuses,
            'notifications' => $notifications,
        ]);
    }

    /**
     * Trigger evaluasi status stok untuk semua item milik user yang login.
     * Ini simulasi apa yang nantinya dijalankan otomatis oleh scheduler harian.
     */
    public function evaluate(Request $request)
    {
        $items = DB::table('items')
            ->where('user_id', auth()->id())
            ->pluck('kode_barang');

        $changedCount = 0;

        foreach ($items as $kodeBarang) {
            $result = $this->service->evaluateAndLogStatus((string) $kodeBarang, 'manual');
            if ($result['changed']) {
                $changedCount++;
            }
        }

        return redirect()
            ->route('stock-notifications.index')
            ->with('success', "Evaluasi selesai. {$changedCount} item mengalami perubahan status.");
    }
}