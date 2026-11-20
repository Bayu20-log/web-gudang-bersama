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
    public function up(): void
    {
        // Kolom user_id sudah ditambahkan migration 2025_08_29_*; lewati kalau sudah ada.
        if (Schema::hasColumn('items', 'user_id')) {
            return;
        }

        Schema::table('items', function (Blueprint $table) {
            // tambahkan kolom user_id
            $table->unsignedBigInteger('user_id')->after('kode_barang');

            // foreign key ke users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        // Sengaja dikosongkan: user_id dimiliki migration 2025_08_29_*, jangan dihapus di sini.
    }
};
