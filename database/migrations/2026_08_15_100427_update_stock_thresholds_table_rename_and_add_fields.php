<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('stock_thresholds', function (Blueprint $table) {
            // Rename hari_buffer -> safety_stock_days (konsisten bahasa Inggris)
            $table->renameColumn('hari_buffer', 'safety_stock_days');
        });

        Schema::table('stock_thresholds', function (Blueprint $table) {
            // Kolom baru: hasil hitungan Threshold Rendah (ROP)
            $table->decimal('low_threshold', 10, 2)->nullable()->after('adc');

            // Kolom baru: Waktu Respons (hari), default 1, dikonfigurasi per item
            $table->unsignedInteger('response_time_days')->default(1)->after('lead_time_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_thresholds', function (Blueprint $table) {
            $table->dropColumn(['low_threshold', 'response_time_days']);
        });

        Schema::table('stock_thresholds', function (Blueprint $table) {
            $table->renameColumn('safety_stock_days', 'hari_buffer');
        });
    }
};