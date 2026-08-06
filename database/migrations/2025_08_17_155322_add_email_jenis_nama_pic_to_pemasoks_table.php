<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pemasoks', function (Blueprint $table) {
            $table->string('email')->unique()->nullable()->after('nama_pemasok');
            $table->string('jenis')->nullable()->after('email');
            $table->string('nama_pic')->nullable()->after('jenis');
        });
    }

    public function down()
    {
        Schema::table('pemasoks', function (Blueprint $table) {
            $table->dropColumn(['email', 'jenis', 'nama_pic']);
        });
    }
};
