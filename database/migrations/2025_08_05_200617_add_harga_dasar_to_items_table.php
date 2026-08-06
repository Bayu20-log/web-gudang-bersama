<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambahkan kolom harga_dasar ke tabel items.
     */
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->decimal('harga_dasar', 15, 2)->default(0)->after('nama_barang');
            // Ganti 'nama_item' dengan kolom terakhir yang sudah ada di tabel items
        });
    }

    /**
     * Rollback perubahan.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('harga_dasar');
        });
    }
};
