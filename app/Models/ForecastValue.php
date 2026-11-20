<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForecastValue extends Model
{
    protected $table = 'forecast_values';

    protected $fillable = [
        'model_result_id', 'forecast_date', 'horizon_step',
        'predicted_requirement', 'lower_bound', 'upper_bound',
        'actual_quantity_out', 'realized_error',
    ];

    public function modelResult()
    {
        return $this->belongsTo(ForecastModelResult::class, 'model_result_id');
    }
}