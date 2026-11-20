<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up()
    {
        // Kolom user_id sudah ditambahkan migration 2025_08_29_*; lewati kalau sudah ada.
        if (Schema::hasColumn('kondisis', 'user_id')) {
            return;
        }

        Schema::table('kondisis', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        // Sengaja dikosongkan: user_id dimiliki migration 2025_08_29_*, jangan dihapus di sini.
    }
};
