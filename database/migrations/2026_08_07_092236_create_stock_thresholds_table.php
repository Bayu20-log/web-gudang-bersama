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
        Schema::create('stock_thresholds', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('kode_barang')->on('items')->onDelete('cascade');
            $table->decimal('adc', 10, 2)->default(0);
            $table->unsignedInteger('lead_time_days')->default(3);
            $table->unsignedInteger('hari_buffer')->default(1);
            $table->decimal('critical_threshold', 10, 2)->default(0);
            $table->timestamp('calculated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_thresholds');
    }
};
