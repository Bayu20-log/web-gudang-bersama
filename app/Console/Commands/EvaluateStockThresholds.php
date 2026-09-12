<?php
namespace App\Console\Commands;

use App\Services\AdaptiveThresholdService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class EvaluateStockThresholds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:evaluate-thresholds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghitung ulang threshold dan status stok tiap barang';

    /**
     * Execute the console command.
     *
     * Ini versi CLI dari tombol "Evaluasi Sekarang" di halaman web, tapi
     * dijalankan untuk SEMUA item lintas user (bukan cuma milik satu user
     * seperti di StockNotificationController@evaluate) -- sesuai konteks
     * scheduler yang jalan otomatis untuk seluruh sistem, bukan per sesi
     * user yang sedang login.
     *
     * @return int
     */
    public function handle(AdaptiveThresholdService $thresholdService): int
    {
        $kodeBarangList = DB::table('items')->pluck('kode_barang');
        $changedCount = 0;

        foreach ($kodeBarangList as $kodeBarang) {
            $result = $thresholdService->evaluateAndLogStatus((string) $kodeBarang, 'scheduler');
            if ($result['changed']) {
                $changedCount++;
            }
        }

        // Reminder untuk item yang statusnya sudah lama Kritis/Habis dan
        // belum di-reminder lagi -- ini logic terpisah yang memang dirancang
        // dipanggil dari scheduler, bukan trigger manual dari web.
        $reminded = $thresholdService->checkAndSendReminders();

        $this->info("Evaluasi selesai. {$kodeBarangList->count()} item diperiksa, {$changedCount} mengalami perubahan status, " . count($reminded) . " reminder dikirim.");

        return Command::SUCCESS;
    }
}