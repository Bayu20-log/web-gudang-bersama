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
    Schema::table('barang_masuks', function ($table) {
        $table->dateTime('tanggal_masuk')->change();
    });

    Schema::table('barang_keluars', function ($table) {
        $table->dateTime('tanggal_keluar')->change();
    });
}

public function down()
{
    Schema::table('barang_masuks', function ($table) {
        $table->date('tanggal_masuk')->change();
    });

    Schema::table('barang_keluars', function ($table) {
        $table->date('tanggal_keluar')->change();
    });
}

};
