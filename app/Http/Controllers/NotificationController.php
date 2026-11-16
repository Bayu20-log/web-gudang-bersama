<?php
namespace App\Http\Controllers;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', auth()->id()) // 🔹 Tambah filter
            ->latest()
            ->get();

        $statusFilter = $request->query('status');

        // Hitung jumlah per level TANPA filter/limit -- dipakai untuk kartu
        // ringkasan di atas, supaya angkanya tetap akurat terlepas dari
        // filter mana yang sedang aktif di daftar bawahnya.
        $levelCounts = DB::table('stock_notifications')
            ->join('items', 'items.kode_barang', '=', 'stock_notifications.item_id')
            ->where('items.user_id', auth()->id())
            // Notifikasi pemulihan (stok membaik/kembali aman) bukan peringatan,
            // jadi tidak ikut dihitung di kartu Rendah/Kritis/Habis.
            ->where('stock_notifications.type', '!=', 'resolution')
            ->select('stock_notifications.level', DB::raw('count(*) as total'))
            ->groupBy('stock_notifications.level')
            ->pluck('total', 'level');

        // Daftar notifikasi yang ditampilkan -- kena filter & dibagi 10 per halaman.
        $stockNotificationsQuery = DB::table('stock_notifications')
            ->join('items', 'items.kode_barang', '=', 'stock_notifications.item_id')
            ->where('items.user_id', auth()->id());

        if (in_array($statusFilter, ['info', 'warning', 'critical'])) {
            // Filter Rendah/Kritis/Habis: hanya notifikasi peringatan
            $stockNotificationsQuery
                ->where('stock_notifications.level', $statusFilter)
                ->where('stock_notifications.type', '!=', 'resolution');
        }

        $stockNotifications = $stockNotificationsQuery
            ->orderBy('stock_notifications.is_read', 'asc')
            ->orderByRaw("FIELD(stock_notifications.level, 'critical', 'warning', 'info')")
            ->orderByDesc('stock_notifications.created_at')
            ->select('stock_notifications.*', 'items.nama_barang')
            ->paginate(10)
            ->withQueryString(); // supaya filter ?status= ikut terbawa saat pindah halaman

        // Kalau halaman yang diminta sudah tidak ada (mis. setelah notifikasi
        // berkurang), arahkan ke halaman terakhir yang masih berisi data.
        if ($stockNotifications->isEmpty() && $stockNotifications->currentPage() > 1) {
            return redirect($stockNotifications->url($stockNotifications->lastPage()));
        }

        return view('notifications.index', compact(
            'notifications',
            'stockNotifications',
            'statusFilter',
            'levelCounts'
        ));
    }

    public function markRead()
    {
        Notification::where('user_id', auth()->id()) // 🔹 Tambah filter
            ->where('is_read', false)
            ->update(['is_read' => true]);
        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai sebagai dibaca.');
    }

    public function markSingleRead($id)
    {
        $notif = Notification::where('user_id', auth()->id()) // 🔹 Tambah filter
            ->findOrFail($id);
        $notif->update(['is_read' => true]);
        return redirect()->back()->with('success', 'Notifikasi telah ditandai sebagai dibaca.');
    }
}