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
     * Ambil data histori (series) untuk 1 item, lalu potong jadi hanya titik-titik
     * SEBELUM $cutoff. Dipakai supaya model evaluasi/forecast tidak pernah "mengintip"
     * data yang secara kronologis belum diketahui pada tanggal mulai prediksi yang
     * dipilih user (penting untuk mode backtest, ketika tanggal mulai yang dipilih
     * overlap dengan data historis yang sudah ada).
     */
    protected function seriesSebelumTanggal($series, Carbon $cutoff)
    {
        return $series->filter(
            fn($point) => Carbon::parse($point['tanggal'])->lt($cutoff)
        )->values();
    }

    /**
     * STEP 1: pilih item -> tampilkan info histori + form pilih rentang tanggal.
     * STEP 2: setelah tanggal_mulai & tanggal_akhir dipilih -> BARU sistem cek
     * kelayakan data & uji SES/HWES/ARIMA -> tampilkan tabel perbandingan +
     * rekomendasi model terbaik.
     *
     * Evaluasi model HANYA memakai data historis SEBELUM tanggal_mulai (bukan
     * seluruh histori sampai data_end). Ini mencegah kebocoran data: kalau user
     * memilih tanggal mulai yang ternyata sudah ada datanya (mode backtest),
     * model tetap dilatih seolah-olah tanggal setelahnya belum diketahui, supaya
     * perbandingan actual vs prediksi di step berikutnya valid.
     */
    public function create(Request $request)
    {
        $items = Item::orderBy('nama_barang')->get();

        $kodeBarang   = $request->query('kode_barang');
        $tanggalMulai = $request->query('tanggal_mulai');
        $tanggalAkhir = $request->query('tanggal_akhir');

        $selectedItem       = null;
        $feasible           = null;
        $isThin             = false;
        $evaluations        = [];
        $dataPoints         = 0;      // total histori item (seluruhnya, buat info umum)
        $trainingDataPoints = 0;      // histori yang benar-benar dipakai buat training (sebelum tanggal_mulai)
        $dataStart          = null;
        $dataEnd            = null;
        $horizonHari        = null;
        $dateError          = null;

        if ($kodeBarang) {
            $selectedItem = Item::where('kode_barang', $kodeBarang)->firstOrFail();

            $series = $this->forecasting->getDailySeries($kodeBarang, auth()->id());
            $dataPoints = $series->count();
            $dataStart  = $series->first()['tanggal'] ?? null;
            $dataEnd    = $series->last()['tanggal'] ?? null;

            // Default tanggal (kalau user belum submit form step 2): mulai sehari
            // setelah data terakhir, horizon default 7 hari. User tetap bebas ubah
            // sebelum klik "Lihat Rekomendasi Model" (termasuk mundurin ke tanggal
            // yang overlap data historis buat mode backtest).
            if (!$tanggalMulai && $dataEnd) {
                $tanggalMulai = Carbon::parse($dataEnd)->addDay()->format('Y-m-d');
            }
            if (!$tanggalAkhir && $tanggalMulai) {
                $tanggalAkhir = Carbon::parse($tanggalMulai)->copy()->addDays(6)->format('Y-m-d');
            }

            // Evaluasi model HANYA jalan kalau tanggal sudah eksplisit ada di query
            // string (artinya user sudah submit form step 2), bukan sekadar nilai
            // default di atas.
            if ($request->query('tanggal_mulai') && $request->query('tanggal_akhir')) {
                $mulaiDate = Carbon::parse($tanggalMulai)->startOfDay();
                $akhirDate = Carbon::parse($tanggalAkhir)->startOfDay();

                if ($akhirDate->lt($mulaiDate)) {
                    $dateError = 'Tanggal akhir tidak boleh sebelum tanggal mulai.';
                } elseif ($mulaiDate->diffInDays($akhirDate) + 1 > 90) {
                    $dateError = 'Rentang prediksi maksimal 90 hari. Perpendek rentang tanggalnya.';
                } else {
                    $horizonHari = $mulaiDate->diffInDays($akhirDate) + 1;

                    $trainingSeries = $this->seriesSebelumTanggal($series, $mulaiDate);
                    $trainingDataPoints = $trainingSeries->count();

                    $feasible = $this->forecasting->isFeasible($trainingSeries);

                    if ($feasible) {
                        $isThin = $this->forecasting->isDataThin($trainingSeries);
                        $evaluations = $this->forecasting->evaluateAllModels($trainingSeries);
                    }
                }
            }
        }

        return view('prediksi.create', compact(
            'items', 'kodeBarang', 'selectedItem', 'feasible', 'isThin', 'evaluations',
            'dataPoints', 'trainingDataPoints', 'dataStart', 'dataEnd',
            'tanggalMulai', 'tanggalAkhir', 'horizonHari', 'dateError'
        ))->with('minDataPoints', $this->forecasting->minDataPoints);
    }

    /**
     * STEP 3: user memilih model (rekomendasi atau model lain untuk dibandingkan),
     * sistem menghitung forecast final + error, lalu OTOMATIS tersimpan ke history:
     * - 1 baris forecast_runs
     * - 3 baris forecast_model_results (semua model yang diuji, is_selected untuk 1 model)
     * - N baris forecast_values (hanya untuk model yang dipilih, sejumlah horizon)
     *
     * tanggal_mulai & tanggal_akhir dikunci dari step 2 (dikirim sebagai hidden
     * input, bukan diketik ulang di sini), dan horizon dihitung ulang di server
     * dari kedua tanggal itu -- bukan dipercaya begitu saja dari client.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_barang'   => 'required|exists:items,kode_barang',
            'model'         => 'required|in:SES,HWES,ARIMA',
            'params'        => 'required|string', // JSON string dari form
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
        ]);

        $params = json_decode($request->params, true);
        if (!is_array($params)) {
            return back()->with('error', 'Parameter model tidak valid, silakan ulangi dari halaman pilih item.');
        }

        $userId = auth()->id();
        $kodeBarang = $request->kode_barang;
        $item = Item::where('kode_barang', $kodeBarang)->firstOrFail();

        $tanggalMulai = Carbon::parse($request->tanggal_mulai)->startOfDay();
        $tanggalAkhir = Carbon::parse($request->tanggal_akhir)->startOfDay();

        $horizon = $tanggalMulai->diffInDays($tanggalAkhir) + 1;
        if ($horizon > 90) {
            return back()->with('error', 'Rentang prediksi maksimal 90 hari. Perpendek rentang tanggalnya.');
        }

        // Series lengkap (dipakai belakangan buat isi actual_quantity_out kalau
        // tanggal forecast ternyata overlap sama data historis yang sudah ada).
        $series = $this->forecasting->getDailySeries($kodeBarang, $userId);

        // Series yang BENERAN dipakai buat training model: hanya data SEBELUM
        // tanggal_mulai. Ini yang mencegah model "curang" lihat data yang
        // seharusnya belum diketahui pada tanggal_mulai yang dipilih user.
        $trainingSeries = $this->seriesSebelumTanggal($series, $tanggalMulai);

        if (!$this->forecasting->isFeasible($trainingSeries)) {
            return back()->with('error', "Data historis SEBELUM tanggal mulai yang dipilih belum cukup (kurang dari {$this->forecasting->minDataPoints} hari) untuk diprediksi.");
        }

        $daysBefore = $trainingSeries->count();
        if ($daysBefore < $this->forecasting->minDataPoints) {
            return back()->with('error', "Tanggal mulai prediksi yang dipilih ({$tanggalMulai->format('d M Y')}) cuma punya {$daysBefore} hari data historis sebelumnya. Minimal {$this->forecasting->minDataPoints} hari data historis dibutuhkan SEBELUM tanggal mulai prediksi. Pilih tanggal mulai yang lebih jauh ke depan, atau lengkapi data historis lebih awal.");
        }

        // Uji ulang ketiga model (pakai training series yang sama dengan yang
        // ditampilkan di halaman rekomendasi) supaya ketiganya tersimpan di
        // forecast_model_results dan konsisten dengan apa yang user lihat.
        $allEvaluations = $this->forecasting->evaluateAllModels($trainingSeries);

        $result = $this->forecasting->runFinalForecast($trainingSeries, $request->model, $params, $horizon, $tanggalMulai);

        DB::beginTransaction();
        try {
            $run = ForecastRun::create([
                'user_id'          => $userId,
                'item_id'          => $item->kode_barang,
                'data_start'       => $trainingSeries->first()['tanggal'],
                'data_end'         => $trainingSeries->last()['tanggal'],
                'frequency'        => 'harian',
                'horizon'          => $horizon,
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
            // Tanggal mulai persis sesuai pilihan user di step 2 (bebas overlap
            // dengan data historis untuk mode backtest).
            //
            // Kalau tanggal forecast ternyata sudah ada di data historis LENGKAP
            // ($series, bukan $trainingSeries), langsung isi actual_quantity_out
            // dan realized_error dari data historis itu -- ini valid sebagai
            // perbandingan karena modelnya sendiri TIDAK dilatih pakai data itu
            // (trainingSeries sudah dipotong sebelum tanggal_mulai).
            $historicalByDate = $series->keyBy('tanggal');

            $forecastDate = $tanggalMulai->copy();
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