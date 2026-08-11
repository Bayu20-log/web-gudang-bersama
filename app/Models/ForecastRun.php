<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ForecastRun extends Model
{
    protected $table = 'forecast_runs';

    protected $fillable = [
        'user_id', 'item_id', 'data_start', 'data_end', 'frequency',
        'horizon', 'selected_model', 'selection_metric', 'status',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id', 'kode_barang');
    }

    public function modelResults()
    {
        return $this->hasMany(ForecastModelResult::class, 'forecast_run_id');
    }

    public function selectedResult()
    {
        return $this->hasOne(ForecastModelResult::class, 'forecast_run_id')->where('is_selected', true);
    }
}