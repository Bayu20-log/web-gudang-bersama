<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

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
     * @return int
     */
    public function handle()
{
    $this->info('Perhitungan threshold stok belum diimplementasikan.');
    // TODO: loop semua item, hitung ADC, ROP, dan evaluasi status stok

    return Command::SUCCESS;
}
}
