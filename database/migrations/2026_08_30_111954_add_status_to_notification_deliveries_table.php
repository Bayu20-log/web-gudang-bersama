<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Menambahkan kolom status pengiriman (pending/sent/failed) ke
     * notification_deliveries, sesuai rancangan "4 konsep terpisah"
     * yang sudah difinalisasi di Tugas 5 tapi belum sempat dibuatkan
     * migration-nya.
     */
    public function up(): void
    {
        Schema::table('notification_deliveries', function (Blueprint $table) {
            // Default 'pending' -- setiap percobaan kirim dicatat dulu
            // sebagai pending SEBELUM benar-benar dikirim, baru diupdate
            // jadi 'sent' atau 'failed' setelah hasil percobaan diketahui.
            $table->enum('status', ['pending', 'sent', 'failed'])
                ->default('pending')
                ->after('channel');

            // Diisi kalau status = 'failed', supaya ada jejak untuk
            // debugging (misal: pesan error SMTP). Null kalau sukses.
            $table->text('failure_reason')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('notification_deliveries', function (Blueprint $table) {
            $table->dropColumn(['status', 'failure_reason']);
        });
    }
};