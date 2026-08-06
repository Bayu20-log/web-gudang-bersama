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
        Schema::table('barang_keluars', function (Blueprint $table) {
    $table->unsignedBigInteger('id_lokasi')->after('kode_barang')->nullable();
    $table->decimal('harga_jual', 15, 2)->after('jumlah_keluar')->nullable();
    $table->decimal('total_harga_jual', 20, 2)->after('harga_jual')->nullable();

    $table->foreign('id_lokasi')->references('id')->on('lokasis');
});

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('barang_keluars', function (Blueprint $table) {
            $table->dropForeign(['id_lokasi']);
            $table->dropColumn(['id_lokasi', 'harga_jual', 'total_harga_jual']);
        });
    }
};
