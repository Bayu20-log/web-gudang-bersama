<?php

namespace App\Services;

use App\Models\BarangKeluar;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdaptiveThresholdService
{
    /**
     * Periode observasi maksimal (hari) untuk menghitung ADC.
     */
    private const MAX_OBSERVATION_DAYS = 30;

    /**
     * Menghitung ADC (Average Daily Consumption) untuk satu item.
     *
     * ADC = total barang keluar dalam periode observasi ÷ jumlah hari valid
     * (hari tanpa transaksi tetap dihitung sebagai bagian periode).
     *
     * @param  string  $kodeBarang
     * @return float
     */
    public function calculateADC(string $kodeBarang): float
    {
        $today = Carbon::now();
        $observationStart = $today->copy()->subDays(self::MAX_OBSERVATION_DAYS);

        // Total barang keluar dalam periode observasi (maks 30 hari terakhir)
        $totalOutflow = BarangKeluar::where('kode_barang', $kodeBarang)
            ->where('tanggal_keluar', '>=', $observationStart)
            ->sum('jumlah_keluar');

        // Cari kapan item ini pertama kali punya transaksi keluar
        // (dipakai untuk menentukan jumlah hari valid, kalau item masih baru)
        $firstTransaction = BarangKeluar::where('kode_barang', $kodeBarang)
            ->orderBy('tanggal_keluar', 'asc')
            ->value('tanggal_keluar');

        if ($firstTransaction === null) {
            // Belum pernah ada transaksi keluar sama sekali -> ADC = 0
            return 0.0;
        }

        $firstTransactionDate = Carbon::parse($firstTransaction);

        // Jumlah hari sejak transaksi pertama, dibatasi maksimal 30 hari,
        // dan minimal 1 hari supaya tidak dibagi nol.
        $daysSinceFirstTransaction = $firstTransactionDate->diffInDays($today) + 1;
        $validDays = min(self::MAX_OBSERVATION_DAYS, max(1, $daysSinceFirstTransaction));

        return round($totalOutflow / $validDays, 2);
    }

    /**
     * Menghitung Threshold Rendah & Threshold Kritis untuk satu item,
     * lalu menyimpan hasilnya (termasuk ADC) ke tabel stock_thresholds.
     *
     * Threshold Rendah (ROP) = (ADC x Lead Time) + Safety Stock
     * Safety Stock           = ADC x Hari Buffer (safety_stock_days)
     * Threshold Kritis       = ADC x Waktu Respons (response_time_days)
     *
     * @param  string  $kodeBarang
     * @return array{adc: float, low_threshold: float, critical_threshold: float}
     */
    public function calculateAndSaveThreshold(string $kodeBarang): array
    {
        // Ambil konfigurasi yang sudah ada untuk item ini, kalau belum ada
        // pakai default sesuai rancangan (lead time 3 hari, buffer 1 hari,
        // waktu respons 1 hari).
        $existing = DB::table('stock_thresholds')
            ->where('item_id', $kodeBarang)
            ->first();

        $leadTimeDays     = $existing->lead_time_days ?? 3;
        $safetyStockDays  = $existing->safety_stock_days ?? 1;
        $responseTimeDays = $existing->response_time_days ?? 1;

        $adc = $this->calculateADC($kodeBarang);

        $safetyStock     = $adc * $safetyStockDays;
        $lowThreshold    = ($adc * $leadTimeDays) + $safetyStock;
        $criticalThreshold = $adc * $responseTimeDays;

        DB::table('stock_thresholds')->updateOrInsert(
            ['item_id' => $kodeBarang],
            [
                'adc'                => $adc,
                'low_threshold'      => round($lowThreshold, 2),
                'lead_time_days'     => $leadTimeDays,
                'response_time_days' => $responseTimeDays,
                'safety_stock_days'  => $safetyStockDays,
                'critical_threshold' => round($criticalThreshold, 2),
                'calculated_at'      => now(),
            ]
        );

        return [
            'adc'                => $adc,
            'low_threshold'      => round($lowThreshold, 2),
            'critical_threshold' => round($criticalThreshold, 2),
        ];
    }

    /**
     * Menghitung stok aktual satu item:
     * total barang masuk - total barang keluar (sepanjang riwayat, bukan cuma 30 hari).
     *
     * @param  string  $kodeBarang
     * @return float
     */
    public function getCurrentStock(string $kodeBarang): float
    {
        $totalMasuk = DB::table('barang_masuks')
            ->where('kode_barang', $kodeBarang)
            ->sum('jumlah');

        $totalKeluar = DB::table('barang_keluars')
            ->where('kode_barang', $kodeBarang)
            ->sum('jumlah_keluar');

        return $totalMasuk - $totalKeluar;
    }

    /**
     * Menentukan status stok satu item (Aman/Rendah/Kritis/Habis)
     * dengan membandingkan stok aktual terhadap Threshold Rendah & Kritis
     * yang tersimpan di stock_thresholds.
     *
     * Catatan: method ini TIDAK menghitung ulang threshold. Pastikan
     * calculateAndSaveThreshold() sudah pernah dipanggil untuk item ini.
     *
     * @param  string  $kodeBarang
     * @return string  'Aman' | 'Rendah' | 'Kritis' | 'Habis'
     */
    public function classifyStatus(string $kodeBarang): string
    {
        $stokAktual = $this->getCurrentStock($kodeBarang);

        if ($stokAktual <= 0) {
            return 'habis';
        }

        $threshold = DB::table('stock_thresholds')
            ->where('item_id', $kodeBarang)
            ->first();

        if (!$threshold) {
            // Belum pernah dihitung threshold-nya sama sekali.
            // Hitung dulu sekarang supaya klasifikasi tetap bisa jalan.
            $this->calculateAndSaveThreshold($kodeBarang);
            $threshold = DB::table('stock_thresholds')
                ->where('item_id', $kodeBarang)
                ->first();
        }

        if ($stokAktual > $threshold->low_threshold) {
            return 'aman';
        }

        if ($stokAktual > $threshold->critical_threshold) {
            return 'rendah';
        }

        return 'kritis';
    }

    /**
     * Mengevaluasi status stok terkini satu item dan mencatat log HANYA
     * jika statusnya berubah dari record terakhir (sesuai rancangan Fitur 3).
     *
     * @param  string  $kodeBarang
     * @param  string  $triggerSource  'scheduler' | 'manual' - sumber pemicu evaluasi ini
     * @return array{changed: bool, status: string, previous_status: ?string}
     */
    public function evaluateAndLogStatus(string $kodeBarang, string $triggerSource = 'manual'): array
    {
        $currentStatus = $this->classifyStatus($kodeBarang);

        // Ambil log paling baru untuk item ini (kalau ada)
        $lastLog = DB::table('stock_status_logs')
            ->where('item_id', $kodeBarang)
            ->orderByDesc('created_at')
            ->first();

        $previousStatus = $lastLog->current_status ?? null;

        // Kalau status tidak berubah, skip -- tidak perlu simpan log baru
        if ($previousStatus === $currentStatus) {
            return [
                'changed'         => false,
                'status'          => $currentStatus,
                'previous_status' => $previousStatus,
            ];
        }

        $stokAktual = $this->getCurrentStock($kodeBarang);
        $threshold  = DB::table('stock_thresholds')
            ->where('item_id', $kodeBarang)
            ->first();

        // Kalau threshold belum pernah dihitung sama sekali (misal item ini
        // stoknya sudah 0 sejak awal, sehingga classifyStatus() langsung
        // return 'habis' tanpa sempat menghitung threshold), hitung dulu
        // sekarang -- supaya adc_snapshot tidak pernah null.
        if (!$threshold) {
            $this->calculateAndSaveThreshold($kodeBarang);
            $threshold = DB::table('stock_thresholds')
                ->where('item_id', $kodeBarang)
                ->first();
        }

        $logId = DB::table('stock_status_logs')->insertGetId([
            'item_id'            => $kodeBarang,
            'previous_status'    => $previousStatus,
            'current_status'     => $currentStatus,
            'stock_snapshot'     => $stokAktual,
            'adc_snapshot'       => $threshold->adc ?? null,
            // Kolom ini cuma bisa nyimpan 1 angka desimal, jadi kita simpan
            // low_threshold sebagai acuan utama (batas Aman <-> Rendah).
            // critical_threshold tetap bisa dilihat dari tabel stock_thresholds.
            'threshold_snapshot' => $threshold->low_threshold ?? null,
            'trigger_source'     => $triggerSource,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        $this->generateNotification($kodeBarang, $logId, $previousStatus, $currentStatus);

        return [
            'changed'         => true,
            'status'          => $currentStatus,
            'previous_status' => $previousStatus,
        ];
    }

    /**
     * Urutan tingkat keparahan status, dari paling ringan ke paling parah.
     * Dipakai untuk menentukan apakah status "memburuk" atau "membaik".
     */
    private function severityRank(string $status): int
    {
        return match ($status) {
            'aman'   => 0,
            'rendah' => 1,
            'kritis' => 2,
            'habis'  => 3,
            default  => 0,
        };
    }

    /**
     * Memetakan status stok ke level notifikasi.
     * Rendah->Info, Kritis->Warning, Habis->Critical (sesuai rancangan).
     */
    private function levelForStatus(string $status): string
    {
        return match ($status) {
            'rendah' => 'info',
            'kritis' => 'warning',
            'habis'  => 'critical',
            default  => 'info',
        };
    }

    /**
     * Membuat notifikasi berdasarkan perubahan status (initial/escalation/resolution).
     * Dipanggil otomatis dari evaluateAndLogStatus() setiap kali status berubah.
     *
     * @param  string  $kodeBarang
     * @param  int  $stockStatusLogId
     * @param  string|null  $previousStatus
     * @param  string  $currentStatus
     * @return array|null  null kalau tidak ada notifikasi yang dibuat
     */
    public function generateNotification(
        string $kodeBarang,
        int $stockStatusLogId,
        ?string $previousStatus,
        string $currentStatus
    ): ?array {
        $isFirstEverStatus = $previousStatus === null || $previousStatus === 'aman';
        $isImproving = !$isFirstEverStatus
            && $this->severityRank($currentStatus) < $this->severityRank($previousStatus);
        $isWorsening = !$isFirstEverStatus
            && $this->severityRank($currentStatus) > $this->severityRank($previousStatus);

        if ($isImproving) {
            $type  = 'resolution';
            $level = 'info';
            $isResolved = $currentStatus === 'aman';
            $title = $isResolved
                ? "Stok item #{$kodeBarang} kembali Aman"
                : "Stok item #{$kodeBarang} membaik menjadi " . ucfirst($currentStatus);
            $message = "Status stok item #{$kodeBarang} berubah dari {$previousStatus} menjadi {$currentStatus}.";
        } elseif ($isFirstEverStatus && $currentStatus !== 'aman') {
            $type  = 'initial';
            $level = $this->levelForStatus($currentStatus);
            $isResolved = false;
            $title = "Peringatan stok item #{$kodeBarang}: " . ucfirst($currentStatus);
            $message = "Status stok item #{$kodeBarang} terdeteksi {$currentStatus}.";
        } elseif ($isWorsening) {
            $type  = 'escalation';
            $level = $this->levelForStatus($currentStatus);
            $isResolved = false;
            $title = "Eskalasi stok item #{$kodeBarang}: " . ucfirst($previousStatus) . ' -> ' . ucfirst($currentStatus);
            $message = "Status stok item #{$kodeBarang} memburuk dari {$previousStatus} menjadi {$currentStatus}.";
        } else {
            // Status baru = 'aman' dan sebelumnya juga sudah 'aman' (harusnya
            // tidak pernah sampai sini karena evaluateAndLogStatus sudah skip
            // kalau status tidak berubah), atau kasus lain yang tidak dikenali.
            return null;
        }

        $notificationId = DB::table('stock_notifications')->insertGetId([
            'item_id'             => $kodeBarang,
            'stock_status_log_id' => $stockStatusLogId,
            'type'                => $type,
            'level'               => $level,
            'title'               => $title,
            'message'             => $message,
            'is_resolved'         => $isResolved,
            'last_sent_at'        => now(),
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);

        $this->deliverNotification($notificationId, $kodeBarang);

        return [
            'id'    => $notificationId,
            'type'  => $type,
            'level' => $level,
        ];
    }

    /**
     * Mengecek semua item yang statusnya Kritis/Habis dan SUDAH LAMA
     * (lebih dari 5 hari) tidak berubah, lalu mengirim notifikasi 'reminder'
     * jika reminder terakhir untuk item itu sudah lebih dari 2 hari lalu.
     *
     * Dipanggil secara periodik lewat scheduler (bukan reaktif seperti
     * initial/escalation/resolution).
     *
     * @return array  daftar item yang baru saja dikirimi reminder
     */
    public function checkAndSendReminders(): array
    {
        $reminderThresholdDays = 5;
        $reminderIntervalDays  = 2;
        $sent = [];

        // Ambil log status terbaru untuk tiap item yang statusnya Kritis/Habis
        $latestLogs = DB::table('stock_status_logs as sl1')
            ->whereIn('current_status', ['kritis', 'habis'])
            ->whereRaw('sl1.created_at = (
                select max(sl2.created_at) from stock_status_logs sl2
                where sl2.item_id = sl1.item_id
            )')
            ->get();

        foreach ($latestLogs as $log) {
            $daysSinceChange = Carbon::parse($log->created_at)->diffInDays(now());

            if ($daysSinceChange < $reminderThresholdDays) {
                continue; // belum cukup lama, skip
            }

            $lastNotification = DB::table('stock_notifications')
                ->where('item_id', $log->item_id)
                ->orderByDesc('last_sent_at')
                ->first();

            if ($lastNotification) {
                $daysSinceLastSent = Carbon::parse($lastNotification->last_sent_at)->diffInDays(now());
                if ($daysSinceLastSent < $reminderIntervalDays) {
                    continue; // belum waktunya reminder berikutnya
                }
            }

            $level = $this->levelForStatus($log->current_status);

            $notificationId = DB::table('stock_notifications')->insertGetId([
                'item_id'             => $log->item_id,
                'stock_status_log_id' => $log->id,
                'type'                => 'reminder',
                'level'               => $level,
                'title'               => "Pengingat: item #{$log->item_id} masih " . ucfirst($log->current_status),
                'message'             => "Status stok item #{$log->item_id} masih {$log->current_status} sejak {$daysSinceChange} hari lalu.",
                'is_resolved'         => false,
                'last_sent_at'        => now(),
                'created_at'          => now(),
                'updated_at'          => now(),
            ]);

            $sent[] = [
                'item_id'         => $log->item_id,
                'notification_id' => $notificationId,
            ];

            $this->deliverNotification($notificationId, (string) $log->item_id);
        }

        return $sent;
    }

    /**
     * Mencatat pengiriman notifikasi ke channel in-app (selalu),
     * dan ke channel email (kecuali pemilik barang menonaktifkan
     * email_notifications_enabled -- default aktif/opt-out).
     *
     * CATATAN: pengiriman email di sini bersifat SIMULASI -- hanya
     * dicatat ke notification_deliveries, belum benar-benar mengirim
     * lewat SMTP. Integrasi SMTP asli adalah pengembangan lanjutan.
     *
     * @param  int  $notificationId
     * @param  string  $kodeBarang
     * @return array  daftar channel yang tercatat terkirim
     */
    public function deliverNotification(int $notificationId, string $kodeBarang): array
    {
        $channelsDelivered = [];

        // In-app selalu terkirim, tanpa syarat.
        DB::table('notification_deliveries')->insert([
            'notification_id' => $notificationId,
            'channel'         => 'in_app',
            'delivered_at'    => now(),
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
        $channelsDelivered[] = 'in_app';

        // Cek preferensi email pemilik barang.
        $item = DB::table('items')->where('kode_barang', $kodeBarang)->first();
        $user = $item ? DB::table('users')->where('id', $item->user_id)->first() : null;

        if ($user && $user->email_notifications_enabled) {
            DB::table('notification_deliveries')->insert([
                'notification_id' => $notificationId,
                'channel'         => 'email',
                'delivered_at'    => now(),
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
            $channelsDelivered[] = 'email';
        }

        return $channelsDelivered;
    }
}