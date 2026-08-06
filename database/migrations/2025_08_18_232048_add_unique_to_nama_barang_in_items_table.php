<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->unique('nama_barang'); // kasih unique constraint
        });
    }

    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropUnique(['nama_barang']); // rollback hapus unique
        });
    }
};
