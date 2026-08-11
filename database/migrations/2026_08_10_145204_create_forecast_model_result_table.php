<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forecast_model_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forecast_run_id')->constrained('forecast_runs')->onDelete('cascade');
            $table->enum('model_name', ['SES', 'ARIMA', 'HWES']);
            $table->string('model_parameters')->nullable(); // JSON string, mis. {"alpha":0.3}
            $table->decimal('mae', 14, 4)->nullable();
            $table->decimal('rmse', 14, 4)->nullable();
            $table->decimal('mase_atau_wape', 14, 4)->nullable(); // dipakai sebagai WAPE (%)
            $table->decimal('aic_aicc', 14, 4)->nullable(); // hanya diisi untuk ARIMA
            $table->string('diagnostic_status')->nullable(); // mis. "OK", "Residual tidak normal"
            $table->boolean('is_selected')->default(false);
            $table->string('failure_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forecast_model_results');
    }
};