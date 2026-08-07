<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropUnique('items_nama_barang_unique'); // hapus constraint unique
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->unique('nama_barang'); // kalau rollback, balikin unique lagi
        });
    }
};
