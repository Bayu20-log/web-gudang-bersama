<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('barang_keluars', function (Blueprint $table) {
            $table->id(); // primary key
            $table->unsignedBigInteger('kode_barang'); // FK ke items.kode_barang
            $table->foreign('kode_barang')->references('kode_barang')->on('items')->onDelete('cascade');
            $table->date('tanggal_keluar');
            $table->integer('jumlah_keluar');
            $table->string('penerima');
            $table->string('lokasi_tujuan');
            $table->foreignId('id_kondisi')->constrained('kondisis')->restrictOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();
            
        });
              
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('barang_keluars');
    }
};
