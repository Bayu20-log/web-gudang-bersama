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
    public function index()
    {
        $items = DB::table('items')
            ->where('user_id', auth()->id())
            ->orderBy('nama_barang')
            ->get();
        $itemStatuses = $items->map(function ($item) {
            $stok = $this->service->getCurrentStock((string) $item->kode_barang);
            $status = $this->service->classifyStatus((string) $item->kode_barang);
            $threshold = DB::table('stock_thresholds')
                ->where('item_id', $item->kode_barang)
                ->first();
            $breakdown = $this->service->getThresholdBreakdown((string) $item->kode_barang);
            return (object) [
                'kode_barang'        => $item->kode_barang,
                'nama_barang'        => $item->nama_barang,
                'stok'               => $stok,
                'status'             => $status,
                'adc'                => $threshold->adc ?? null,
                'low_threshold'      => $threshold->low_threshold ?? null,
                'critical_threshold' => $threshold->critical_threshold ?? null,
                'breakdown'          => $breakdown,
            ];
        });
        $notifications = DB::table('stock_notifications')
            ->join('items', 'items.kode_barang', '=', 'stock_notifications.item_id')
            ->where('items.user_id', auth()->id())
            ->orderByDesc('stock_notifications.created_at')
            ->select('stock_notifications.*', 'items.nama_barang')
            ->limit(20)
            ->get();
        return view('stock-notifications.index', [
            'itemStatuses'  => $itemStatuses,
            'notifications' => $notifications,
        ]);
    }

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

    /**
     * Menandai satu notifikasi stok sebagai sudah dibaca.
     * Terpisah dari is_resolved -- ini murni penanda UI, tidak mengubah
     * kondisi/logic evaluasi status stok sama sekali.
     */
    public function markRead($id)
    {
        $this->authorizeNotification($id);

        DB::table('stock_notifications')->where('id', $id)->update(['is_read' => true]);

        return redirect()->back()->with('success', 'Notifikasi ditandai sudah dibaca.');
    }

    /**
     * Kebalikan dari markRead -- user bisa sengaja menandai balik sebagai
     * belum dibaca kalau masih perlu ditindaklanjuti nanti.
     */
    public function markUnread($id)
    {
        $this->authorizeNotification($id);

        DB::table('stock_notifications')->where('id', $id)->update(['is_read' => false]);

        return redirect()->back()->with('success', 'Notifikasi ditandai belum dibaca.');
    }

    /**
     * Menandai SEMUA notifikasi stok milik user ini sebagai sudah dibaca
     * sekaligus (mirip tombol "Tandai Semua Dibaca" di sistem lama).
     */
    public function markAllRead()
    {
        DB::table('stock_notifications')
            ->join('items', 'items.kode_barang', '=', 'stock_notifications.item_id')
            ->where('items.user_id', auth()->id())
            ->update(['stock_notifications.is_read' => true]);

        return redirect()->back()->with('success', 'Semua notifikasi stok ditandai sudah dibaca.');
    }

    /**
     * Pastikan notifikasi yang mau ditandai memang milik item milik user
     * yang sedang login (stock_notifications tidak punya kolom user_id
     * sendiri, jadi verifikasi lewat join ke items).
     */
    private function authorizeNotification($id): void
    {
        $owned = DB::table('stock_notifications')
            ->join('items', 'items.kode_barang', '=', 'stock_notifications.item_id')
            ->where('stock_notifications.id', $id)
            ->where('items.user_id', auth()->id())
            ->exists();

        if (!$owned) {
            abort(403, 'Unauthorized');
        }
    }
}