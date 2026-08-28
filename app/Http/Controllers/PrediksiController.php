<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\ForecastRun;
use App\Models\ForecastModelResult;
use App\Models\ForecastValue;
use App\Services\ForecastingService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PrediksiController extends Controller
{
    protected ForecastingService $forecasting;

    public function __construct(ForecastingService $forecasting)
    {
        $this->forecasting = $forecasting;
    }

    /**
     * Halaman riwayat (history) prediksi milik user login.
     * rmse/mae/mape diambil dari forecast_model_results yang is_selected = true,
     * karena forecast_runs sendiri tidak menyimpan nilai error (sesuai desain tabel TA).
     */
    public function index(Request $request)
    {
        $nama   = $request->nama_barang;
        $model  = $request->selected_model;
        $sortBy = $request->sort_by ?? 'created_at';
        $sortDir = $request->sort_dir === 'asc' ? 'asc' : 'desc';
        $userId = auth()->id();

        $query = ForecastRun::query()
            ->select('forecast_runs.*')
            ->addSelect([
                // Catatan: tabel forecast_model_results secara skema kolomnya bernama
                // 'mase_atau_wape', tapi nilai yang disimpan di sini adalah MAPE (sesuai
                // preferensi kamu), bukan WAPE/MASE.
                'selected_rmse' => ForecastModelResult::selectRaw('rmse')
                    ->whereColumn('forecast_run_id', 'forecast_runs.id')
                    ->where('is_selected', true)->limit(1),
                'selected_mae' => ForecastModelResult::selectRaw('mae')
                    ->whereColumn('forecast_run_id', 'forecast_runs.id')
                    ->where('is_selected', true)->limit(1),
                'selected_mape' => ForecastModelResult::selectRaw('mase_atau_wape')
                    ->whereColumn('forecast_run_id', 'forecast_runs.id')
                    ->where('is_selected', true)->limit(1),
                // Total kebutuhan hasil prediksi (jumlah semua predicted_requirement selama horizon)
                // dari model yang dipilih, dipakai untuk kolom "Hasil Prediksi" di tabel riwayat.
                'total_prediksi' => DB::table('forecast_values')
                    ->selectRaw('SUM(predicted_requirement)')
                    ->join('forecast_model_results', 'forecast_model_results.id', '=', 'forecast_values.model_result_id')
                    ->whereColumn('forecast_model_results.forecast_run_id', 'forecast_runs.id')
                    ->where('forecast_model_results.is_selected', true),
            ])
            ->with('item')
            ->where('user_id', $userId)
            ->when($nama, function ($q) use ($nama) {
                $q->whereHas('item', fn($qi) => $qi->where('nama_barang', 'like', "%{$nama}%"));
            })
            ->when($model, fn($q) => $q->where('selected_model', $model));

        $allowedSort = ['created_at', 'selected_model'];
        if (!in_array($sortBy, $allowedSort, true)) {
            $sortBy = 'created_at';
        }

        $data = $query->orderBy($sortBy, $sortDir)->paginate(10)->appends($request->query());

        return view('prediksi.index', [
            'data' => $data,
            'sortBy' => $sortBy,
            'sortDir' => $sortDir,
        ]);
    }

    /**
     * Detail 1 riwayat prediksi (history): perbandingan semua model yang diuji,
     * kurva actual (histori) vs forecast (model terpilih), dan tombol export PDF.
     */
    public function show(ForecastRun $prediksi)
    {
        abort_unless($prediksi->user_id === auth()->id(), 403);

        $prediksi->load(['item', 'modelResults' => fn($q) => $q->orderBy('rmse')]);

        $selectedResult = $prediksi->modelResults->firstWhere('is_selected', true);
        $forecastValues = $selectedResult
            ? ForecastValue::where('model_result_id', $selectedResult->id)->orderBy('forecast_date')->get()
            : collect();

        // Data historis actual diambil ulang dari transaksi barang keluar (bukan disimpan
        // di forecast_values, karena forecast_values hanya untuk titik hasil prediksi).
        $historicalSeries = $this->forecasting->getDailySeries($prediksi->item->kode_barang, auth()->id());

        return view('prediksi.show', compact('prediksi', 'selectedResult', 'forecastValues', 'historicalSeries'));
    }

    /**
     * STEP 1 & 2: pilih item -> sistem cek kelayakan data -> uji SES/HWES/ARIMA
     * -> tampilkan tabel perbandingan RMSE/MAPE/MAE + rekomendasi model terbaik.
     */
    public function create(Request $request)
    {
        $items = Item::orderBy('nama_barang')->get();

        $kodeBarang = $request->query('kode_barang');
        $selectedItem = null;
        $feasible = null;
        $isThin = false;
        $evaluations = [];
        $dataPoints = 0;
        $dataStart = null;
        $dataEnd = null;

        if ($kodeBarang) {
            $selectedItem = Item::where('kode_barang', $kodeBarang)->firstOrFail();

            $series = $this->forecasting->getDailySeries($kodeBarang, auth()->id());
            $dataPoints = $series->count();
            $feasible = $this->forecasting->isFeasible($series);

            if ($feasible) {
                $isThin = $this->forecasting->isDataThin($series);
                $evaluations = $this->forecasting->evaluateAllModels($series);
                $dataStart = $series->first()['tanggal'];
                $dataEnd = $series->last()['tanggal']; // dipakai buat hitung tanggal mulai prediksi (data_end + 1 hari)
            }
        }

        return view('prediksi.create', compact(
            'items', 'kodeBarang', 'selectedItem', 'feasible', 'isThin', 'evaluations', 'dataPoints', 'dataStart', 'dataEnd'
        ))->with('minDataPoints', $this->forecasting->minDataPoints);
    }

    /**
     * STEP 3: user memilih model (rekomendasi atau model lain untuk dibandingkan),
     * sistem menghitung forecast final + error, lalu OTOMATIS tersimpan ke history:
     * - 1 baris forecast_runs
     * - 3 baris forecast_model_results (semua model yang diuji, is_selected untuk 1 model)
     * - N baris forecast_values (hanya untuk model yang dipilih, sejumlah horizon)
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_barang'   => 'required|exists:items,kode_barang',
            'model'         => 'required|in:SES,HWES,ARIMA',
            'params'        => 'required|string', // JSON string dari form
            'horizon'       => 'required|integer|min:1|max:90',
            'tanggal_mulai' => 'required|date', // bebas dipilih user, tidak dikunci ke data_end+1
        ]);

        $params = json_decode($request->params, true);
        if (!is_array($params)) {
            return back()->with('error', 'Parameter model tidak valid, silakan ulangi dari halaman pilih item.');
        }

        $userId = auth()->id();
        $kodeBarang = $request->kode_barang;
        $item = Item::where('kode_barang', $kodeBarang)->firstOrFail();

        $series = $this->forecasting->getDailySeries($kodeBarang, $userId);

        if (!$this->forecasting->isFeasible($series)) {
            return back()->with('error', "Data historis item ini belum cukup (total data historis kurang dari {$this->forecasting->minDataPoints} hari) untuk diprediksi.");
        }

        // Validasi: data historis SEBELUM tanggal mulai yang dipilih user harus minimal
        // sejumlah minDataPoints (7 hari), supaya model tetap punya cukup dasar perhitungan.
        // Ini yang mencegah user memilih tanggal mulai yang terlalu mepet/di tengah data historis.
        $tanggalMulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
        $daysBefore = $series->filter(function ($point) use ($tanggalMulai) {
            return Carbon::parse($point['tanggal'])->lt($tanggalMulai);
        })->count();

        if ($daysBefore < $this->forecasting->minDataPoints) {
            return back()->with('error', "Tanggal mulai prediksi yang dipilih ({$tanggalMulai->format('d M Y')}) cuma punya {$daysBefore} hari data historis sebelumnya. Minimal {$this->forecasting->minDataPoints} hari data historis dibutuhkan SEBELUM tanggal mulai prediksi. Pilih tanggal mulai yang lebih jauh ke depan, atau lengkapi data historis lebih awal.");
        }

        // Uji ulang ketiga model supaya ketiganya tersimpan di forecast_model_results
        $allEvaluations = $this->forecasting->evaluateAllModels($series);

        $result = $this->forecasting->runFinalForecast($series, $request->model, $params, (int) $request->horizon);

        DB::beginTransaction();
        try {
            $run = ForecastRun::create([
                'user_id'          => $userId,
                'item_id'          => $item->kode_barang,
                'data_start'       => $series->first()['tanggal'],
                'data_end'         => $series->last()['tanggal'],
                'frequency'        => 'harian',
                'horizon'          => $request->horizon,
                'selected_model'   => $request->model,
                'selection_metric' => 'RMSE',
                'status'           => 'selesai',
            ]);

            $selectedModelResult = null;

            foreach ($allEvaluations as $ev) {
                $isSelected = $ev['model'] === $request->model;

                $modelResult = ForecastModelResult::create([
                    'forecast_run_id'   => $run->id,
                    'model_name'        => $ev['model'],
                    'model_parameters'  => json_encode($ev['params']),
                    'mae'               => $ev['mae'],
                    'rmse'              => $ev['rmse'],
                    'mase_atau_wape'    => $ev['mape'], // kolom ini dipakai untuk simpan MAPE (bukan WAPE)
                    'aic_aicc'          => $ev['aic'],
                    'diagnostic_status' => 'OK',
                    'is_selected'       => $isSelected,
                    'failure_message'   => null,
                ]);

                if ($isSelected) {
                    $selectedModelResult = $modelResult;
                }
            }

            // Kalau parameter model pilihan user beda dari hasil grid-search evaluateAllModels
            // (mis. user sengaja pilih model lain di luar rekomendasi), pastikan model_parameters
            // yang tersimpan tetap sinkron dengan parameter yang dipakai untuk forecast final.
            if ($selectedModelResult) {
                $selectedModelResult->update([
                    'model_parameters' => json_encode($params),
                    'mae' => $result['mae'],
                    'rmse' => $result['rmse'],
                    'mase_atau_wape' => $result['mape'],
                ]);
            }

            // Simpan hasil forecast masa depan hanya untuk model yang dipilih.
            // Tanggal mulai dihitung dari input user (bebas dipilih), BUKAN otomatis data_end+1,
            // supaya user bisa memprediksi periode yang mereka mau.
            //
            // Kalau tanggal forecast ternyata sudah ada di data historis (karena user pilih
            // tanggal mulai yang overlap dengan data historis), langsung isi actual_quantity_out
            // dan realized_error dari data historis itu, bukan dibiarkan kosong menunggu "nanti".
            $historicalByDate = $series->keyBy('tanggal');

            $forecastDate = Carbon::parse($request->tanggal_mulai);
            foreach ($result['future_forecast'] as $i => $pred) {
                $tanggal = $forecastDate->copy()->addDays($i)->format('Y-m-d');
                $actual = $historicalByDate->has($tanggal) ? (float) $historicalByDate[$tanggal]['y'] : null;

                ForecastValue::create([
                    'model_result_id'        => $selectedModelResult->id,
                    'forecast_date'          => $tanggal,
                    'horizon_step'           => $i + 1,
                    'predicted_requirement'  => $pred,
                    'lower_bound'            => $result['bounds'][$i]['lower'],
                    'upper_bound'            => $result['bounds'][$i]['upper'],
                    'actual_quantity_out'    => $actual, // otomatis terisi kalau tanggalnya ada di data historis
                    'realized_error'         => $actual !== null ? abs($pred - $actual) : null,
                ]);
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan prediksi: ' . $e->getMessage());
        }

        return redirect()->route('prediksi.show', $run->id)
            ->with('success', 'Prediksi berhasil dibuat dan otomatis tersimpan ke history.');
    }

    /**
     * Export hasil prediksi ke PDF (sesuai alur diagram: "dapat di export ke PDF").
     * Pakai barryvdh/laravel-dompdf, sesuaikan bila package lain yang dipakai.
     */
    public function exportPdf(ForecastRun $prediksi)
    {
        abort_unless($prediksi->user_id === auth()->id(), 403);

        $prediksi->load(['item', 'modelResults' => fn($q) => $q->orderBy('rmse')]);
        $selectedResult = $prediksi->modelResults->firstWhere('is_selected', true);
        $forecastValues = $selectedResult
            ? ForecastValue::where('model_result_id', $selectedResult->id)->orderBy('forecast_date')->get()
            : collect();

        $historicalSeries = $this->forecasting->getDailySeries($prediksi->item->kode_barang, auth()->id());

        $pdf = \PDF::loadView('prediksi.pdf', compact('prediksi', 'selectedResult', 'forecastValues', 'historicalSeries'));

        return $pdf->download('prediksi-' . $prediksi->item->kode_barang . '-' . $prediksi->id . '.pdf');
    }
}