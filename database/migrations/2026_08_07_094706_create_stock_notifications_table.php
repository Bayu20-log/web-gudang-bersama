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
        Schema::create('stock_notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('kode_barang')->on('items')->onDelete('cascade');
            $table->foreignId('stock_status_log_id')->nullable()->constrained('stock_status_logs')->onDelete('set null');
            $table->enum('type', ['initial', 'reminder', 'escalation', 'resolution']);
            $table->enum('level', ['info', 'warning', 'critical']);
            $table->string('title');
            $table->text('message');
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('last_sent_at')->nullable();
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
        Schema::dropIfExists('stock_notifications');
    }
};
