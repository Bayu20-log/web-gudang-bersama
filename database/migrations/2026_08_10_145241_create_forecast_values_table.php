<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forecast_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('model_result_id')->constrained('forecast_model_results')->onDelete('cascade');
            $table->date('forecast_date');
            $table->unsignedInteger('horizon_step'); // t+1, t+2, dst.
            $table->decimal('predicted_requirement', 14, 4);
            $table->decimal('lower_bound', 14, 4)->nullable();
            $table->decimal('upper_bound', 14, 4)->nullable();
            $table->decimal('actual_quantity_out', 14, 4)->nullable(); // diisi belakangan setelah periode berlalu
            $table->decimal('realized_error', 14, 4)->nullable(); // diisi belakangan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forecast_values');
    }
};