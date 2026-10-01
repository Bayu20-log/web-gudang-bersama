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
        Schema::create('stock_status_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('kode_barang')->on('items')->onDelete('cascade');
            $table->enum('previous_status', ['aman', 'rendah', 'kritis', 'habis'])->nullable();
            $table->enum('current_status', ['aman', 'rendah', 'kritis', 'habis']);
            $table->decimal('stock_snapshot', 10, 2);
            $table->decimal('adc_snapshot', 10, 2);
            $table->decimal('threshold_snapshot', 10, 2);
            $table->string('trigger_source')->default('scheduler');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stock_status_logs');
    }
};
