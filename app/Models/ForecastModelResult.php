<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForecastModelResult extends Model
{
    protected $table = 'forecast_model_results';

    protected $fillable = [
        'forecast_run_id', 'model_name', 'model_parameters',
        'mae', 'rmse', 'mase_atau_wape', 'aic_aicc',
        'diagnostic_status', 'is_selected', 'failure_message',
    ];

    protected $casts = [
        'is_selected' => 'boolean',
    ];

    public function run()
    {
        return $this->belongsTo(ForecastRun::class, 'forecast_run_id');
    }

    public function values()
    {
        return $this->hasMany(ForecastValue::class, 'model_result_id');
    }
}