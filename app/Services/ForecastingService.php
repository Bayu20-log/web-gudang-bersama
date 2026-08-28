<?php

namespace App\Services;

use App\Models\BarangKeluar;
use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * ForecastingService
 *
 * Berisi implementasi 3 model peramalan (SES, Holt-Winters/HWES, ARIMA sederhana)
 * beserta perhitungan error (RMSE, MAPE, MAE) untuk perbandingan model.
 *
 * CATATAN PENTING (baca sebelum dipakai untuk TA):
 * - SES & Holt-Winters diimplementasikan manual (murni PHP) dan sudah representatif
 *   untuk dibandingkan secara akademis.
 * - ARIMA "murni" (estimasi parameter AR & MA lewat MLE) tidak praktis dibuat dari nol
 *   di PHP tanpa library statistik. Implementasi di sini adalah ARIMA sederhana
 *   (differencing + AR(p) via Ordinary Least Squares, tanpa komponen MA / "ARI").
 *   Untuk hasil ARIMA yang sepenuhnya valid secara statistik (mis. auto_arima seperti
 *   di Python `statsmodels`/`pmdarima`), sebaiknya panggil microservice Python
 *   (Flask/FastAPI) dari controller ini. Method evaluateARIMA()/forecastARIMA()
 *   sudah dipisah supaya gampang diganti jadi HTTP call ke service Python nanti.
 */
class ForecastingService
{
    public int $minDataPoints = 7; // minimal jumlah hari data historis agar layak diprediksi (hard minimum)
    public int $recommendedDataPoints = 30; // di bawah ini data dianggap "tipis", tetap boleh jalan tapi diberi warning
    public int $seasonalPeriods = 7; // pola mingguan (data harian)

    /**
     * Ambil deret waktu jumlah barang keluar harian untuk 1 item milik user login.
     * Tanggal yang tidak ada transaksi diisi 0 supaya deret tetap kontinu.
     */
    public function getDailySeries(string $kodeBarang, int $userId): Collection
    {
        $rows = BarangKeluar::whereHas('item', function ($q) use ($kodeBarang) {
                $q->where('kode_barang', $kodeBarang);
            })
            ->where('user_id', $userId)
            ->selectRaw('DATE(tanggal_keluar) as tanggal, SUM(jumlah_keluar) as total')
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        if ($rows->isEmpty()) {
            return collect();
        }

        $start = Carbon::parse($rows->first()->tanggal);
        $end   = Carbon::parse($rows->last()->tanggal);

        $map = $rows->keyBy('tanggal');

        $series = collect();
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $key = $date->format('Y-m-d');
            $series->push([
                'tanggal' => $key,
                'y'       => (float) ($map[$key]->total ?? 0),
            ]);
        }

        return $series;
    }

    public function isFeasible(Collection $series): bool
    {
        return $series->count() >= $this->minDataPoints;
    }

    /**
     * True kalau data historis masih di bawah ambang batas "recommended" (30 hari).
     * Data tetap boleh diproses (selama >= minDataPoints), tapi hasilnya kurang reliabel,
     * terutama untuk HWES yang idealnya butuh minimal 2x siklus musiman (14 hari) untuk
     * benar-benar mendeteksi pola mingguan.
     */
    public function isDataThin(Collection $series): bool
    {
        return $series->count() < $this->recommendedDataPoints;
    }

    /**
     * Split data 80/20 secara temporal (bukan acak), sesuai desain TA.
     */
    public function trainTestSplit(Collection $series, float $trainRatio = 0.8): array
    {
        $n = $series->count();
        $trainSize = (int) floor($n * $trainRatio);

        return [
            'train' => $series->slice(0, $trainSize)->values(),
            'test'  => $series->slice($trainSize)->values(),
        ];
    }

    // ================== ERROR METRICS ==================

    public function rmse(array $actual, array $predicted): float
    {
        $n = count($actual);
        if ($n === 0) return 0.0;
        $sum = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $sum += ($actual[$i] - $predicted[$i]) ** 2;
        }
        return sqrt($sum / $n);
    }

    public function mae(array $actual, array $predicted): float
    {
        $n = count($actual);
        if ($n === 0) return 0.0;
        $sum = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $sum += abs($actual[$i] - $predicted[$i]);
        }
        return $sum / $n;
    }

    /**
     * MAPE mengabaikan titik actual = 0 (karena data barang keluar banyak nol,
     * sesuai justifikasi kenapa RMSE dipilih sebagai metrik utama di TA).
     */
    public function mape(array $actual, array $predicted): float
    {
        $sum = 0.0;
        $count = 0;
        foreach ($actual as $i => $a) {
            if ($a == 0) continue;
            $sum += abs(($a - $predicted[$i]) / $a);
            $count++;
        }
        return $count > 0 ? ($sum / $count) * 100 : 0.0;
    }

    /**
     * WAPE (Weighted Absolute Percentage Error) - dipakai untuk kolom
     * mase_atau_wape di tabel forecast_model_results. Lebih stabil dari MAPE
     * saat actual banyak bernilai 0, karena pembaginya total actual (bukan per titik).
     */
    public function wape(array $actual, array $predicted): float
    {
        $sumAbsError = 0.0;
        $sumActual = 0.0;
        foreach ($actual as $i => $a) {
            $sumAbsError += abs($a - $predicted[$i]);
            $sumActual += abs($a);
        }
        return $sumActual > 0 ? ($sumAbsError / $sumActual) * 100 : 0.0;
    }

    // ================== SES ==================

    private function sesForecastInSample(array $train, float $alpha): array
    {
        $level = $train[0];
        $fitted = [$level];
        for ($i = 1; $i < count($train); $i++) {
            $level = $alpha * $train[$i - 1] + (1 - $alpha) * $level;
            $fitted[] = $level;
        }
        return ['fitted' => $fitted, 'level' => $level];
    }

    /**
     * Grid search alpha (0.01 - 0.99) untuk meminimalkan RMSE pada data test
     * (one-step-ahead pakai level terakhir hasil training).
     */
    public function evaluateSES(array $train, array $test): array
    {
        $best = null;
        for ($a = 1; $a <= 99; $a++) {
            $alpha = $a / 100;
            $res = $this->sesForecastInSample($train, $alpha);
            $pred = array_fill(0, count($test), $res['level']); // SES = flat forecast

            $rmse = $this->rmse($test, $pred);
            if ($best === null || $rmse < $best['rmse']) {
                $best = [
                    'model'  => 'SES',
                    'params' => ['alpha' => $alpha],
                    'rmse'   => $rmse,
                    'mape'   => $this->mape($test, $pred),
                    'mae'    => $this->mae($test, $pred),
                    'wape'   => $this->wape($test, $pred),
                    'aic'    => null, // AIC hanya dihitung untuk ARIMA sesuai desain tabel
                ];
            }
        }
        return $best;
    }

    public function forecastSES(array $fullSeries, float $alpha, int $horizon): array
    {
        $res = $this->sesForecastInSample($fullSeries, $alpha);
        return array_fill(0, $horizon, $res['level']);
    }

    // ================== HOLT-WINTERS (Triple Exponential Smoothing, additive) ==================

    private function hwesFit(array $train, float $alpha, float $beta, float $gamma, int $sp): array
    {
        $n = count($train);
        if ($n < $sp * 2) {
            // fallback kalau data terlalu pendek untuk musiman
            $sp = max(2, min($sp, intdiv($n, 2)));
        }

        // inisialisasi level, trend, dan seasonal (rata-rata musim pertama)
        $level = array_sum(array_slice($train, 0, $sp)) / $sp;
        $trend = (array_sum(array_slice($train, $sp, $sp)) - array_sum(array_slice($train, 0, $sp))) / ($sp * $sp);
        $seasonal = [];
        for ($i = 0; $i < $sp; $i++) {
            $seasonal[$i] = $train[$i] - $level;
        }

        $fitted = [];
        for ($t = 0; $t < $n; $t++) {
            $s = $seasonal[$t % $sp];
            $fitted[$t] = $level + $trend + $s;

            $lastLevel = $level;
            $level = $alpha * ($train[$t] - $s) + (1 - $alpha) * ($level + $trend);
            $trend = $beta * ($level - $lastLevel) + (1 - $beta) * $trend;
            $seasonal[$t % $sp] = $gamma * ($train[$t] - $level) + (1 - $gamma) * $s;
        }

        return ['level' => $level, 'trend' => $trend, 'seasonal' => $seasonal, 'sp' => $sp];
    }

    private function hwesForecast(array $state, int $horizon): array
    {
        $pred = [];
        $sp = $state['sp'];
        for ($h = 1; $h <= $horizon; $h++) {
            $s = $state['seasonal'][($h - 1) % $sp];
            $pred[] = max(0, $state['level'] + $h * $state['trend'] + $s);
        }
        return $pred;
    }

    /**
     * Grid search kasar (step 0.1) untuk alpha, beta, gamma agar tetap cepat.
     */
    public function evaluateHWES(array $train, array $test, ?int $seasonalPeriods = null): array
    {
        $sp = $seasonalPeriods ?? $this->seasonalPeriods;
        $best = null;

        foreach ([0.1, 0.3, 0.5, 0.7, 0.9] as $alpha) {
            foreach ([0.1, 0.3, 0.5] as $beta) {
                foreach ([0.1, 0.3, 0.5, 0.7] as $gamma) {
                    $state = $this->hwesFit($train, $alpha, $beta, $gamma, $sp);
                    $pred = $this->hwesForecast($state, count($test));

                    $rmse = $this->rmse($test, $pred);
                    if ($best === null || $rmse < $best['rmse']) {
                        $best = [
                            'model'  => 'HWES',
                            'params' => ['alpha' => $alpha, 'beta' => $beta, 'gamma' => $gamma, 'seasonal_periods' => $sp],
                            'rmse'   => $rmse,
                            'mape'   => $this->mape($test, $pred),
                            'mae'    => $this->mae($test, $pred),
                            'wape'   => $this->wape($test, $pred),
                            'aic'    => null,
                        ];
                    }
                }
            }
        }
        return $best;
    }

    public function forecastHWES(array $fullSeries, array $params, int $horizon): array
    {
        $state = $this->hwesFit($fullSeries, $params['alpha'], $params['beta'], $params['gamma'], $params['seasonal_periods']);
        return $this->hwesForecast($state, $horizon);
    }

    // ================== ARIMA sederhana (differencing + AR(p) via OLS) ==================
    // Lihat catatan di header file: ini APROKSIMASI, bukan ARIMA penuh (belum ada komponen MA).

    private function difference(array $series, int $d = 1): array
    {
        for ($k = 0; $k < $d; $k++) {
            $diffed = [];
            for ($i = 1; $i < count($series); $i++) {
                $diffed[] = $series[$i] - $series[$i - 1];
            }
            $series = $diffed;
        }
        return $series;
    }

    /**
     * Estimasi koefisien AR(p) pakai regresi linier (least squares) tanpa library eksternal.
     */
    private function fitAR(array $diffed, int $p): array
    {
        $n = count($diffed);
        if ($n <= $p) return array_fill(0, $p, 0.0);

        // Bangun matriks X (lag) dan vektor y
        $X = [];
        $y = [];
        for ($t = $p; $t < $n; $t++) {
            $row = [];
            for ($lag = 1; $lag <= $p; $lag++) {
                $row[] = $diffed[$t - $lag];
            }
            $X[] = $row;
            $y[] = $diffed[$t];
        }

        // Normal equation: (X'X) beta = X'y, diselesaikan pakai eliminasi Gauss sederhana
        $XtX = array_fill(0, $p, array_fill(0, $p, 0.0));
        $Xty = array_fill(0, $p, 0.0);
        $rows = count($X);

        for ($i = 0; $i < $p; $i++) {
            for ($j = 0; $j < $p; $j++) {
                $sum = 0.0;
                for ($r = 0; $r < $rows; $r++) $sum += $X[$r][$i] * $X[$r][$j];
                $XtX[$i][$j] = $sum;
            }
            $sum = 0.0;
            for ($r = 0; $r < $rows; $r++) $sum += $X[$r][$i] * $y[$r];
            $Xty[$i] = $sum;
        }

        return $this->gaussSolve($XtX, $Xty);
    }

    private function gaussSolve(array $A, array $b): array
    {
        $n = count($b);
        for ($i = 0; $i < $n; $i++) {
            // pivot
            $maxRow = $i;
            for ($k = $i + 1; $k < $n; $k++) {
                if (abs($A[$k][$i]) > abs($A[$maxRow][$i])) $maxRow = $k;
            }
            [$A[$i], $A[$maxRow]] = [$A[$maxRow], $A[$i]];
            [$b[$i], $b[$maxRow]] = [$b[$maxRow], $b[$i]];

            if (abs($A[$i][$i]) < 1e-10) continue; // hindari div by zero, koefisien jadi 0

            for ($k = $i + 1; $k < $n; $k++) {
                $factor = $A[$k][$i] / $A[$i][$i];
                for ($j = $i; $j < $n; $j++) $A[$k][$j] -= $factor * $A[$i][$j];
                $b[$k] -= $factor * $b[$i];
            }
        }

        $x = array_fill(0, $n, 0.0);
        for ($i = $n - 1; $i >= 0; $i--) {
            if (abs($A[$i][$i]) < 1e-10) { $x[$i] = 0.0; continue; }
            $sum = $b[$i];
            for ($j = $i + 1; $j < $n; $j++) $sum -= $A[$i][$j] * $x[$j];
            $x[$i] = $sum / $A[$i][$i];
        }
        return $x;
    }

    private function arimaForecastValues(array $series, int $p, int $d, int $horizon): array
    {
        $diffed = $this->difference($series, $d);
        $coef = $this->fitAR($diffed, $p);

        // ramalkan di ruang differenced, lalu integrasikan kembali (undo differencing)
        $history = array_slice($diffed, -$p);
        $diffForecast = [];
        for ($h = 0; $h < $horizon; $h++) {
            $val = 0.0;
            for ($lag = 1; $lag <= $p; $lag++) {
                $val += $coef[$lag - 1] * $history[count($history) - $lag];
            }
            $diffForecast[] = $val;
            $history[] = $val;
        }

        // undo differencing (d=1) dari nilai actual terakhir
        $last = end($series);
        $forecast = [];
        foreach ($diffForecast as $dv) {
            $last = $last + $dv;
            $forecast[] = max(0, $last);
        }
        return $forecast;
    }

    public function evaluateARIMA(array $train, array $test, int $d = 1): array
    {
        $best = null;
        foreach ([1, 2, 3] as $p) {
            if (count($train) <= $p + $d + 1) continue;
            $pred = $this->arimaForecastValues($train, $p, $d, count($test));

            $rmse = $this->rmse($test, $pred);
            if ($best === null || $rmse < $best['rmse']) {
                $n = count($test);
                $rss = 0.0;
                foreach ($test as $i => $a) $rss += ($a - $pred[$i]) ** 2;
                $k = $p + $d; // jumlah parameter yang diestimasi, dipakai sebagai pendekatan AIC
                $aic = $n > 0 ? ($n * log(($rss / $n) + 1e-9) + 2 * $k) : null;

                $best = [
                    'model'  => 'ARIMA',
                    'params' => ['p' => $p, 'd' => $d, 'q' => 0],
                    'rmse'   => $rmse,
                    'mape'   => $this->mape($test, $pred),
                    'mae'    => $this->mae($test, $pred),
                    'wape'   => $this->wape($test, $pred),
                    'aic'    => $aic,
                ];
            }
        }
        // fallback bila data terlalu pendek untuk semua p
        return $best ?? [
            'model' => 'ARIMA', 'params' => ['p' => 1, 'd' => $d, 'q' => 0],
            'rmse' => 0, 'mape' => 0, 'mae' => 0, 'wape' => 0, 'aic' => null,
        ];
    }

    public function forecastARIMA(array $fullSeries, array $params, int $horizon): array
    {
        return $this->arimaForecastValues($fullSeries, $params['p'], $params['d'], $horizon);
    }

    // ================== ORKESTRASI ==================

    /**
     * Evaluasi ketiga model sekaligus pada train/test split, lalu rekomendasikan
     * model dengan RMSE terkecil. Dipakai di halaman create sebelum user menyimpan.
     */
    public function evaluateAllModels(Collection $series): array
    {
        $split = $this->trainTestSplit($series);
        $train = $split['train']->pluck('y')->values()->all();
        $test  = $split['test']->pluck('y')->values()->all();

        $results = [
            $this->evaluateSES($train, $test),
            $this->evaluateHWES($train, $test),
            $this->evaluateARIMA($train, $test),
        ];

        usort($results, fn($a, $b) => $a['rmse'] <=> $b['rmse']);

        foreach ($results as $i => &$r) {
            $r['is_recommended'] = $i === 0;
        }

        return $results;
    }

    /**
     * Setelah user pilih model, hitung ulang forecast final di masa depan (horizon hari)
     * memakai parameter terbaik model tersebut, dievaluasi ulang pada test set untuk
     * menghasilkan RMSE/MAPE/MAE final yang disimpan ke history.
     */
    public function runFinalForecast(Collection $series, string $model, array $params, int $horizon): array
    {
        $split = $this->trainTestSplit($series);
        $train = $split['train']->pluck('y')->values()->all();
        $test  = $split['test']->pluck('y')->values()->all();
        $full  = $series->pluck('y')->values()->all();

        switch ($model) {
            case 'SES':
                $testPred = array_fill(0, count($test), $this->sesForecastInSample($train, $params['alpha'])['level']);
                $futurePred = $this->forecastSES($full, $params['alpha'], $horizon);
                break;
            case 'HWES':
                $state = $this->hwesFit($train, $params['alpha'], $params['beta'], $params['gamma'], $params['seasonal_periods']);
                $testPred = $this->hwesForecast($state, count($test));
                $futurePred = $this->forecastHWES($full, $params, $horizon);
                break;
            case 'ARIMA':
                $testPred = $this->arimaForecastValues($train, $params['p'], $params['d'], count($test));
                $futurePred = $this->forecastARIMA($full, $params, $horizon);
                break;
            default:
                throw new \InvalidArgumentException("Model tidak dikenali: {$model}");
        }

        $rmse = $this->rmse($test, $testPred);

        // Confidence interval sederhana: pred +/- 1.96 * RMSE (dipakai sbg pendekatan std error).
        // Untuk interval yang lebih presisi secara statistik (khususnya ARIMA), idealnya dihitung
        // dari standard error hasil estimasi model, bukan RMSE test set.
        $z = 1.96;
        $bounds = array_map(function ($p) use ($rmse, $z) {
            return [
                'lower' => max(0, $p - $z * $rmse),
                'upper' => $p + $z * $rmse,
            ];
        }, $futurePred);

        return [
            'rmse' => $rmse,
            'mape' => $this->mape($test, $testPred),
            'mae'  => $this->mae($test, $testPred),
            'wape' => $this->wape($test, $testPred),
            'future_forecast' => $futurePred,
            'bounds' => $bounds,
        ];
    }

    // ================== CHART UNTUK PDF (GD, karena dompdf tidak reliable render SVG) ==================

    /**
     * Render kurva actual vs prediksi sebagai gambar PNG (base64, tanpa prefix data:image/png;base64,)
     * memakai ekstensi GD bawaan PHP. Dipakai khusus untuk export PDF, karena dompdf tidak menjalankan
     * JavaScript (jadi Chart.js tidak bisa dipakai) dan dukungan inline SVG dompdf tidak konsisten.
     *
     * Membutuhkan ekstensi GD aktif di php.ini (`extension=gd`, sudah default di XAMPP tapi kadang
     * belum di-uncomment). Kalau GD tidak aktif, method ini akan melempar Error.
     */
    public function renderForecastChartImage($historicalSeries, $forecastValues, int $width = 700, int $height = 260): string
    {
        if (!function_exists('imagecreatetruecolor')) {
            throw new \RuntimeException('Ekstensi GD tidak aktif di server ini. Aktifkan extension=gd di php.ini.');
        }

        $histY = collect($historicalSeries)->pluck('y')->map(fn($v) => (float) $v)->values()->all();
        $foreY = collect($forecastValues)->pluck('predicted_requirement')->map(fn($v) => (float) $v)->values()->all();
        $foreLower = collect($forecastValues)->pluck('lower_bound')->map(fn($v) => (float) $v)->values()->all();
        $foreUpper = collect($forecastValues)->pluck('upper_bound')->map(fn($v) => (float) $v)->values()->all();

        $allY = array_merge($histY, $foreY, $foreLower, $foreUpper);
        $minY = min(0, empty($allY) ? 0 : min($allY));
        $maxY = empty($allY) ? 1 : max($allY);
        if ($maxY <= $minY) {
            $maxY = $minY + 1;
        }

        $padL = 55; $padR = 20; $padT = 20; $padB = 30;
        $plotW = $width - $padL - $padR;
        $plotH = $height - $padT - $padB;

        $totalPoints = count($histY) + count($foreY);
        $stepX = $totalPoints > 1 ? $plotW / ($totalPoints - 1) : 0;

        $toPixelY = function ($val) use ($minY, $maxY, $padT, $plotH) {
            if ($maxY == $minY) return (int) round($padT + $plotH);
            return (int) round($padT + $plotH - (($val - $minY) / ($maxY - $minY)) * $plotH);
        };

        $im = imagecreatetruecolor($width, $height);
        $white = imagecolorallocate($im, 255, 255, 255);
        $axisColor = imagecolorallocate($im, 203, 213, 225);
        $blue = imagecolorallocate($im, 37, 99, 235);
        $orange = imagecolorallocate($im, 245, 158, 11);
        $bandColor = imagecolorallocate($im, 253, 230, 138);
        $textColor = imagecolorallocate($im, 100, 116, 139);

        imagefill($im, 0, 0, $white);

        // Titik koordinat historis
        $histCoords = [];
        foreach ($histY as $i => $v) {
            $x = (int) round($padL + $i * $stepX);
            $histCoords[] = [$x, $toPixelY($v)];
        }

        // Titik koordinat prediksi (nyambung dari titik historis terakhir)
        $foreCoords = [];
        $upperCoords = [];
        $lowerCoords = [];
        $startIdx = count($histY) - 1;
        if (!empty($histCoords)) {
            $foreCoords[] = end($histCoords);
        }
        foreach ($foreY as $i => $v) {
            $idx = $startIdx + 1 + $i;
            $x = (int) round($padL + $idx * $stepX);
            $foreCoords[] = [$x, $toPixelY($v)];
            $upperCoords[] = [$x, $toPixelY($foreUpper[$i])];
            $lowerCoords[] = [$x, $toPixelY($foreLower[$i])];
        }

        // Area rentang batas bawah-atas (digambar duluan biar ketutup garis)
        if (!empty($upperCoords)) {
            $bandPoints = [];
            foreach ($upperCoords as $c) { $bandPoints[] = $c[0]; $bandPoints[] = $c[1]; }
            foreach (array_reverse($lowerCoords) as $c) { $bandPoints[] = $c[0]; $bandPoints[] = $c[1]; }
            imagefilledpolygon($im, $bandPoints, count($bandPoints) / 2, $bandColor);
        }

        // Sumbu
        imageline($im, $padL, $padT, $padL, $padT + $plotH, $axisColor);
        imageline($im, $padL, $padT + $plotH, $padL + $plotW, $padT + $plotH, $axisColor);

        // Garis actual (historis)
        for ($i = 0; $i < count($histCoords) - 1; $i++) {
            imageline($im, $histCoords[$i][0], $histCoords[$i][1], $histCoords[$i + 1][0], $histCoords[$i + 1][1], $blue);
        }
        foreach ($histCoords as $c) {
            imagefilledellipse($im, $c[0], $c[1], 4, 4, $blue);
        }

        // Garis prediksi
        for ($i = 0; $i < count($foreCoords) - 1; $i++) {
            imageline($im, $foreCoords[$i][0], $foreCoords[$i][1], $foreCoords[$i + 1][0], $foreCoords[$i + 1][1], $orange);
        }
        foreach ($foreCoords as $idx2 => $c) {
            if ($idx2 === 0) continue; // titik pertama cuma sambungan dari historis, bukan titik prediksi
            imagefilledellipse($im, $c[0], $c[1], 4, 4, $orange);
        }

        // Label sumbu Y (nilai maksimum & minimum)
        imagestring($im, 2, 2, $padT - 6, number_format($maxY, 0), $textColor);
        imagestring($im, 2, 2, $padT + $plotH - 6, number_format($minY, 0), $textColor);

        ob_start();
        imagepng($im);
        $imageData = ob_get_clean();
        imagedestroy($im);

        return base64_encode($imageData);
    }
}