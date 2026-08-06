<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Tambahkan kolom user_id nullable
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
        });

        // Hapus baris notifications yang user_id tidak valid
        DB::table('notifications')->whereNotIn('user_id', function($query) {
            $query->select('id')->from('users');
        })->delete();

        // Set semua user_id null menjadi default user (opsional)
        // DB::table('notifications')->whereNull('user_id')->update(['user_id' => 1]);

        Schema::table('notifications', function (Blueprint $table) {
            // Tambahkan foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};
