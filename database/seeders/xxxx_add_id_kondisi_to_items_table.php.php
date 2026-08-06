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
        Schema::table('items', function (Blueprint $table) {
            $table->unsignedInteger('stok_awal')->default(0);
            $table->enum('kondisi', ['baik', 'rusak', 'hilang'])->default('baik');
            $table->foreignId('id_lokasi')->constrained('lokasis')->onDelete('cascade');
            $table->foreignId('id_pemasok')->constrained('pemasoks')->onDelete('cascade');
        });
    }




    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items', function (Blueprint $table) {
            //
        });
    }
};
