<?php
namespace App\Http\Controllers;
use App\Models\Notification;
use App\Services\ExpiryMonitoringService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = Notification::where('user_id', auth()->id()) // 🔹 Tambah filter
            ->latest()
            ->get();

        $statusFilter = $request->query('status');

        // Filter yang tidak dikenal (mis. ?status=abc) dikembalikan ke "Semua",
        // supaya alamatnya bersih dan tombol filter "Semua" ikut aktif.
        if ($statusFilter !== null && !in_array($statusFilter, ['info', 'warning', 'critical', 'kadaluarsa'], true)) {
            return redirect()->route('notifications.index');
        }

        // ------------------------------------------------------------------
        // Halaman ini menampilkan SATU kartu per barang: notifikasi TERBARU-nya
        // saja. Notifikasi lama tetap tersimpan dan bisa dilihat di riwayat
        // (/stock-notifications). Dengan begitu notifikasi "Rendah" lama tidak
        // tertinggal setelah status barang berubah (mis. jadi Habis/Aman).
        // ------------------------------------------------------------------
        $userId = auth()->id();

        // Cek kadaluarsa setiap kali halaman Notifikasi dibuka (pengganti scheduler
        // di server yang tidak mendukung cron), supaya notifikasi kadaluarsa tetap
        // muncul saat hari berganti walaupun tidak ada transaksi.
        $expiryService = app(ExpiryMonitoringService::class);
        $expiryService->cekKadaluarsa((int) $userId);
        $daftarKadaluarsa = $expiryService->daftarAktifPerBarang((int) $userId);

        // Status TERKINI tiap barang = log status terakhir dari evaluasi.
        $logTerakhir = DB::table('stock_status_logs')
            ->join('items', 'items.kode_barang', '=', 'stock_status_logs.item_id')
            ->where('items.user_id', $userId)
            ->groupBy('stock_status_logs.item_id')
            ->selectRaw('MAX(stock_status_logs.id)');
        $statusTerkini = DB::table('stock_status_logs')
            ->whereIn('id', $logTerakhir)
            ->pluck('current_status', 'item_id');

        // Kartu ringkasan = JUMLAH BARANG yang saat ini Rendah/Kritis/Habis
        // (bukan jumlah notifikasi), jadi angkanya turun saat barang direstok.
        // Kunci info/warning/critical tetap dipakai supaya cocok dengan tampilan.
        $jumlahPerStatus = $statusTerkini->countBy();
        $levelCounts = collect([
            'info'     => $jumlahPerStatus['rendah'] ?? 0,
            'warning'  => $jumlahPerStatus['kritis'] ?? 0,
            'critical' => $jumlahPerStatus['habis'] ?? 0,
            // Kartu ke-4: jumlah BARANG yang punya batch segera/sudah kadaluarsa
            'kadaluarsa' => $daftarKadaluarsa->count(),
        ]);

        // Notifikasi terbaru tiap barang
        $notifTerakhir = DB::table('stock_notifications')
            ->join('items', 'items.kode_barang', '=', 'stock_notifications.item_id')
            ->where('items.user_id', $userId)
            ->groupBy('stock_notifications.item_id')
            ->selectRaw('MAX(stock_notifications.id)');

        // Daftar notifikasi yang ditampilkan -- kena filter & dibagi 10 per halaman.
        $stockNotificationsQuery = DB::table('stock_notifications')
            ->join('items', 'items.kode_barang', '=', 'stock_notifications.item_id')
            ->whereIn('stock_notifications.id', $notifTerakhir);

        if ($statusFilter === 'kadaluarsa') {
            // Filter Kadaluarsa: daftar status stok disembunyikan, yang tampil daftar kadaluarsa
            $stockNotificationsQuery->whereRaw('1 = 0');
        } elseif (in_array($statusFilter, ['info', 'warning', 'critical'])) {
            // Filter Rendah/Kritis/Habis berdasarkan status TERKINI barangnya
            $statusDicari = ['info' => 'rendah', 'warning' => 'kritis', 'critical' => 'habis'][$statusFilter];
            $stockNotificationsQuery->whereIn(
                'stock_notifications.item_id',
                $statusTerkini->filter(fn ($status) => $status === $statusDicari)->keys()->all()
            );
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

        // ------------------------------------------------------------------
        // Tab "Riwayat": semua notifikasi yang pernah dibuat, terbaru di atas,
        // bisa dicari per nama barang atau disaring untuk satu barang.
        // Memakai nama halaman "hal" supaya tidak bentrok dengan pagination tab utama.
        // ------------------------------------------------------------------
        $isRiwayat   = $request->query('tab') === 'riwayat';
        $cariBarang  = trim((string) $request->query('cari', ''));
        $barangDipilih = $request->query('barang');
        $riwayat = null;
        $namaBarangDipilih = null;

        if ($isRiwayat) {
            $riwayat = DB::table('stock_notifications')
                ->join('items', 'items.kode_barang', '=', 'stock_notifications.item_id')
                ->leftJoin('stock_status_logs', 'stock_status_logs.id', '=', 'stock_notifications.stock_status_log_id')
                ->where('items.user_id', $userId)
                ->when($barangDipilih, fn ($q) => $q->where('stock_notifications.item_id', $barangDipilih))
                ->when($cariBarang !== '', fn ($q) => $q->where('items.nama_barang', 'like', '%' . $cariBarang . '%'))
                ->orderByDesc('stock_notifications.created_at')
                ->orderByDesc('stock_notifications.id')
                ->select('stock_notifications.*', 'items.nama_barang', 'stock_status_logs.stock_snapshot')
                ->paginate(20, ['*'], 'hal')
                ->withQueryString();

            if ($barangDipilih) {
                $namaBarangDipilih = DB::table('items')->where('kode_barang', $barangDipilih)->value('nama_barang');
            }
        }

        // Info stok untuk tiap kartu: stok SAAT INI + Batas Rendah & Kritis dari
        // threshold adaptif, supaya pengguna tahu patokan statusnya.
        // Dihitung sekaligus untuk barang di halaman ini saja (maks. 10).
        $kodeDiHalaman = $stockNotifications->getCollection()->pluck('item_id')->unique()->values()->all();

        $totalMasuk = DB::table('barang_masuks')->whereIn('kode_barang', $kodeDiHalaman)
            ->groupBy('kode_barang')->select('kode_barang', DB::raw('SUM(jumlah) as total'))
            ->pluck('total', 'kode_barang');
        $totalKeluar = DB::table('barang_keluars')->whereIn('kode_barang', $kodeDiHalaman)
            ->groupBy('kode_barang')->select('kode_barang', DB::raw('SUM(jumlah_keluar) as total'))
            ->pluck('total', 'kode_barang');
        $thresholds = DB::table('stock_thresholds')->whereIn('item_id', $kodeDiHalaman)
            ->get()->keyBy('item_id');

        $stockNotifications->getCollection()->transform(function ($notif) use ($totalMasuk, $totalKeluar, $thresholds, $statusTerkini) {
            $kode = $notif->item_id;
            // Status barang SEKARANG (dari evaluasi terakhir) -- badge di kartu mengikuti ini
            $notif->status_terkini = $statusTerkini[$kode] ?? null;
            $threshold = $thresholds[$kode] ?? null;

            $notif->stok_saat_ini = (float) ($totalMasuk[$kode] ?? 0) - (float) ($totalKeluar[$kode] ?? 0);
            // Batas hanya ditampilkan kalau barang sudah punya data pemakaian (ADC > 0)
            $notif->punya_batas  = $threshold && (float) $threshold->adc > 0;
            $notif->batas_rendah = $threshold ? ceil((float) $threshold->low_threshold) : null;
            $notif->batas_kritis = $threshold ? ceil((float) $threshold->critical_threshold) : null;

            return $notif;
        });

        // Daftar kadaluarsa (satu kartu per barang), 10 per halaman.
        // Nama halaman "hal_kadaluarsa" supaya tidak bentrok dengan pagination status stok.
        $halKadaluarsa = LengthAwarePaginator::resolveCurrentPage('hal_kadaluarsa');
        $kadaluarsaList = new LengthAwarePaginator(
            $daftarKadaluarsa->forPage($halKadaluarsa, 10)->values(),
            $daftarKadaluarsa->count(),
            10,
            $halKadaluarsa,
            [
                'path'     => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => 'hal_kadaluarsa',
                'query'    => $request->query(),
            ]
        );

        return view('notifications.index', compact(
            'kadaluarsaList',
            'notifications',
            'stockNotifications',
            'statusFilter',
            'levelCounts',
            'isRiwayat',
            'riwayat',
            'cariBarang',
            'barangDipilih',
            'namaBarangDipilih'
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

    /** Tandai semua notifikasi kadaluarsa aktif milik satu barang sebagai sudah dibaca. */
    public function kadaluarsaRead($item)
    {
        $this->ubahStatusBacaKadaluarsa($item, true);
        return redirect()->back()->with('success', 'Notifikasi kadaluarsa ditandai sudah dibaca.');
    }

    /** Tandai semua notifikasi kadaluarsa aktif milik satu barang sebagai belum dibaca. */
    public function kadaluarsaUnread($item)
    {
        $this->ubahStatusBacaKadaluarsa($item, false);
        return redirect()->back()->with('success', 'Notifikasi kadaluarsa ditandai belum dibaca.');
    }

    private function ubahStatusBacaKadaluarsa($item, bool $dibaca): void
    {
        DB::table('expiry_notifications')
            ->join('items', 'items.kode_barang', '=', 'expiry_notifications.item_id')
            ->where('items.user_id', auth()->id())
            ->where('expiry_notifications.item_id', $item)
            ->where('expiry_notifications.is_resolved', false)
            ->update(['expiry_notifications.is_read' => $dibaca]);
    }
}
