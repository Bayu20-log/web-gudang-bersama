<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('items', function (Blueprint $table) {
        // Hapus foreign key untuk id_pemasok kalau ada
        if (Schema::hasColumn('items', 'id_pemasok')) {
            $table->dropForeign(['id_pemasok']);
            $table->dropColumn('id_pemasok');
        }

        // Hapus foreign key untuk kondisi_id atau id_kondisi
        if (Schema::hasColumn('items', 'kondisi_id')) {
            $table->dropForeign(['kondisi_id']);
        } elseif (Schema::hasColumn('items', 'id_kondisi')) {
            $table->dropForeign(['id_kondisi']);
        }
    });
}


    public function down()
{
    Schema::table('items', function (Blueprint $table) {
        // Tambahkan kembali id_pemasok
        if (!Schema::hasColumn('items', 'id_pemasok')) {
            $table->unsignedBigInteger('id_pemasok')->nullable();
            $table->foreign('id_pemasok')->references('id')->on('pemasoks');
        }

        // Tambahkan kembali kondisi_id kalau tadi dihapus
        if (!Schema::hasColumn('items', 'kondisi_id')) {
            $table->unsignedBigInteger('kondisi_id')->nullable();
            $table->foreign('kondisi_id')->references('id')->on('kondisis');
        }
    });
}

};
