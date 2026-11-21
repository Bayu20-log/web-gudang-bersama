<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Notifikasi kadaluarsa (terpisah dari stock_notifications yang khusus status stok).
     *
     * Satu baris = satu batch barang masuk pada satu status kadaluarsa:
     *  - 'segera'     : kadaluarsa dalam 30 hari ke depan (sama dengan Laporan Aset)
     *  - 'kadaluarsa' : tanggal kadaluarsa sudah lewat
     * Hanya batch yang masih punya sisa stok (dihitung FEFO) yang dibuatkan notifikasi.
     * is_resolved = true kalau batch-nya sudah habis terpakai / statusnya berpindah.
     */
    public function up(): void
    {
        Schema::create('expiry_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('kode_barang')->on('items')->onDelete('cascade');
            $table->unsignedBigInteger('barang_masuk_id');
            $table->foreign('barang_masuk_id')->references('id')->on('barang_masuks')->onDelete('cascade');
            $table->enum('status', ['segera', 'kadaluarsa']);
            $table->dateTime('tanggal_kadaluarsa');
            $table->string('lokasi')->nullable();
            $table->integer('jumlah_sisa')->default(0);
            $table->boolean('is_read')->default(false);
            $table->boolean('is_resolved')->default(false);
            $table->timestamps();

            $table->unique(['barang_masuk_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expiry_notifications');
    }
};
