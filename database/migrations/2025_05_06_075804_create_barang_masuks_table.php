<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barang_masuks', function (Blueprint $table) {
            $table->id(); // Primary key dari barang_masuks

            // Foreign key ke items.kode_barang (bukan default id)
            $table->unsignedBigInteger('kode_barang');
            $table->foreign('kode_barang')->references('kode_barang')->on('items')->onDelete('cascade');

            // Field lainnya
            $table->integer('jumlah');
            $table->integer('harga_satuan');
            $table->bigInteger('total_harga')->default(0);
            $table->date('tanggal_masuk');
            $table->date('tanggal_kadaluarsa');

            // Relasi dengan tabel lain
            $table->foreignId('id_pemasok')->constrained('pemasoks')->restrictOnDelete();
            $table->foreignId('id_lokasi')->constrained('lokasis')->restrictOnDelete();
            $table->foreignId('id_kondisi')->constrained('kondisis')->restrictOnDelete();
            $table->foreignId('user_id')->constrained('users')->restrictOnDelete();

            $table->text('catatan')->nullable();
            $table->string('qr_code')->nullable();
            $table->timestamps();
                 // atau ->onDelete('restrict');

        });
    }

    public function down(): void
    {
        Schema::table('barang_masuks', function (Blueprint $table) {
            // Drop all foreign keys explicitly
            $table->dropForeign(['kode_barang']);
            $table->dropForeign(['id_pemasok']);
            $table->dropForeign(['id_lokasi']);
            $table->dropForeign(['id_kondisi']);
            $table->dropForeign(['user_id']);
        });

        Schema::dropIfExists('barang_masuks');
    }
};
