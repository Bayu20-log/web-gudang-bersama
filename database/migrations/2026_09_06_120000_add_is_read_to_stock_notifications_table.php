<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Kolom ini SENGAJA dipisah dari is_resolved yang sudah ada:
     * - is_resolved  = penanda kondisi (apakah stoknya sudah membaik secara nyata)
     * - is_read      = penanda UI (apakah user sudah melihat/menandai notifikasi ini)
     *
     * Keduanya independen. Notifikasi bisa is_read=true tapi is_resolved=false
     * (user sudah lihat, tapi barangnya masih kritis -- notifikasi baru akan
     * tetap muncul lagi kalau statusnya memburuk lebih lanjut).
     */
    public function up(): void
    {
        Schema::table('stock_notifications', function (Blueprint $table) {
            $table->boolean('is_read')->default(false)->after('is_resolved');
        });
    }

    public function down(): void
    {
        Schema::table('stock_notifications', function (Blueprint $table) {
            $table->dropColumn('is_read');
        });
    }
};