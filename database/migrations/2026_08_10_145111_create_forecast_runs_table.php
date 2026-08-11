<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forecast_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('kode_barang')->on('items')->onDelete('cascade');
            $table->date('data_start');
            $table->date('data_end');
            $table->enum('frequency', ['harian', 'mingguan', 'bulanan'])->default('harian');
            $table->unsignedInteger('horizon');
            $table->enum('selected_model', ['SES', 'ARIMA', 'HWES']);
            $table->enum('selection_metric', ['MAE', 'RMSE', 'MAPE'])->default('RMSE');
            $table->enum('status', ['pending', 'diproses', 'selesai', 'gagal'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forecast_runs');
    }
};