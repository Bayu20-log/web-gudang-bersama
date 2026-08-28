<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Prediksi - {{ $prediksi->item->nama_barang ?? '-' }}</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; font-size: 12px; color: #111827; }
        h2 { margin-bottom: 4px; }
        .subtitle { color: #6b7280; margin-bottom: 20px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #d1d5db; padding: 6px 8px; text-align: center; }
        th { background-color: #f3f4f6; }

        .info-table td:first-child { font-weight: 600; width: 180px; background-color: #f9fafb; }
        .row-selected { background-color: #ecfdf5; }
        .section-title { font-size: 14px; font-weight: 700; margin: 20px 0 8px; border-left: 3px solid #2563eb; padding-left: 8px; }
        .footer { margin-top: 30px; font-size: 10px; color: #9ca3af; text-align: center; }

        /* Ringkasan card */
        .summary-box {
            background-color: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 14px 16px;
            margin-bottom: 20px;
        }
        .summary-box table { margin-bottom: 0; }
        .summary-box td { border: none; background: transparent; text-align: center; padding: 4px; }
        .summary-box .summary-number { font-size: 20px; font-weight: 700; color: #1d4ed8; }
        .summary-box .summary-label { font-size: 10px; color: #475569; text-transform: uppercase; }
        .summary-note { font-size: 10.5px; color: #1e3a8a; margin-top: 10px; line-height: 1.5; }

        /* Metric cards ringkas */
        .metric-table td { border: 1px solid #e5e7eb; padding: 10px; }
        .metric-table .metric-label { font-size: 10px; color: #6b7280; text-transform: uppercase; }
        .metric-table .metric-value { font-size: 17px; font-weight: 700; color: #2563eb; }

        /* Chart legend */
        .chart-legend { font-size: 10px; color: #4b5563; margin-top: 4px; }
        .legend-dot { display: inline-block; width: 8px; height: 8px; border-radius: 50%; margin-right: 4px; }

        .explain-box {
            font-size: 10.5px;
            color: #4b5563;
            line-height: 1.5;
            margin-bottom: 10px;
            padding: 8px 10px;
            background: #f9fafb;
            border-radius: 6px;
        }
        .explain-box strong { color: #111827; }
    </style>
</head>
<body>
    <h2>Laporan Hasil Prediksi Barang Keluar</h2>
    <div class="subtitle">{{ $prediksi->item->kode_barang }} - {{ $prediksi->item->nama_barang }} &bull; Dicetak pada {{ now()->format('d M Y H:i') }}</div>

    <table class="info-table">
        <tr>
            <td>Periode Data Historis</td>
            <td>{{ \Carbon\Carbon::parse($prediksi->data_start)->format('d M Y') }} — {{ \Carbon\Carbon::parse($prediksi->data_end)->format('d M Y') }}</td>
        </tr>
        <tr>
            <td>Frekuensi</td>
            <td>{{ ucfirst($prediksi->frequency) }}</td>
        </tr>
        <tr>
            <td>Horizon Prediksi</td>
            <td>{{ $prediksi->horizon }} hari ke depan</td>
        </tr>
        <tr>
            <td>Model Terpilih</td>
            <td>{{ $prediksi->selected_model }}</td>
        </tr>
        <tr>
            <td>Metrik Seleksi</td>
            <td>{{ $prediksi->selection_metric }}</td>
        </tr>
    </table>

    {{-- Ringkasan Hasil Prediksi --}}
    @php
        $totalPrediksi = $forecastValues->sum('predicted_requirement');
        $rataRata = $forecastValues->count() > 0 ? $totalPrediksi / $forecastValues->count() : 0;
        $tanggalMulai = $forecastValues->min('forecast_date');
        $tanggalSelesai = $forecastValues->max('forecast_date');
    @endphp
    <div class="section-title">Ringkasan Hasil Prediksi</div>
    <div class="summary-box">
        <table>
            <tr>
                <td style="width:33%">
                    <div class="summary-number">{{ number_format($totalPrediksi, 0, ',', '.') }}</div>
                    <div class="summary-label">Total Kebutuhan (unit)</div>
                </td>
                <td style="width:33%">
                    <div class="summary-number">{{ number_format($rataRata, 1, ',', '.') }}</div>
                    <div class="summary-label">Rata-rata per Hari</div>
                </td>
                <td style="width:34%">
                    <div class="summary-number">{{ $prediksi->horizon }} Hari</div>
                    <div class="summary-label">
                        @if($tanggalMulai && $tanggalSelesai)
                            {{ \Carbon\Carbon::parse($tanggalMulai)->format('d M') }} - {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d M Y') }}
                        @else
                            Periode Prediksi
                        @endif
                    </div>
                </td>
            </tr>
        </table>
        <div class="summary-note">
            Perkiraan kebutuhan <strong>{{ $prediksi->item->nama_barang }}</strong> untuk {{ $prediksi->horizon }} hari
            ke depan, dihitung memakai model <strong>{{ $prediksi->selected_model }}</strong>. Gunakan angka
            "Total Kebutuhan" sebagai patokan menyiapkan stok, dan tetap perhatikan rentang batas atas/bawah
            pada tabel rincian karena angka aktual bisa sedikit berbeda.
        </div>
    </div>

    {{-- Kurva Actual vs Prediksi (SVG, karena dompdf tidak bisa jalankan JS/Chart.js) --}}
    <div class="section-title">Kurva Actual vs Prediksi</div>
    @php
        $histPoints = $historicalSeries->map(fn($h) => ['y' => (float) $h['y']])->values();
        $forePoints = $forecastValues->map(fn($f) => [
            'y' => (float) $f->predicted_requirement,
            'lower' => (float) $f->lower_bound,
            'upper' => (float) $f->upper_bound,
        ])->values();

        $allY = collect();
        foreach ($histPoints as $p) { $allY->push($p['y']); }
        foreach ($forePoints as $p) { $allY->push($p['y']); $allY->push($p['lower']); $allY->push($p['upper']); }
        $minY = min(0, $allY->min() ?? 0);
        $maxY = $allY->max() ?? 1;
        if ($maxY <= $minY) { $maxY = $minY + 1; }

        $chartW = 680; $chartH = 220;
        $padL = 45; $padR = 15; $padT = 15; $padB = 15;
        $plotW = $chartW - $padL - $padR;
        $plotH = $chartH - $padT - $padB;

        $totalPoints = $histPoints->count() + $forePoints->count();
        $stepX = $totalPoints > 1 ? $plotW / ($totalPoints - 1) : 0;

        $yToPixel = function ($val) use ($minY, $maxY, $padT, $plotH) {
            if ($maxY == $minY) return $padT + $plotH;
            return $padT + $plotH - (($val - $minY) / ($maxY - $minY)) * $plotH;
        };

        $histCoords = [];
        foreach ($histPoints as $i => $p) {
            $histCoords[] = [$padL + $i * $stepX, $yToPixel($p['y'])];
        }

        $foreCoords = [];
        $foreUpperCoords = [];
        $foreLowerCoords = [];
        $startIdx = $histPoints->count() - 1;
        if (!empty($histCoords)) {
            $foreCoords[] = end($histCoords);
        }
        foreach ($forePoints as $i => $p) {
            $idx = $startIdx + 1 + $i;
            $x = $padL + $idx * $stepX;
            $foreCoords[] = [$x, $yToPixel($p['y'])];
            $foreUpperCoords[] = [$x, $yToPixel($p['upper'])];
            $foreLowerCoords[] = [$x, $yToPixel($p['lower'])];
        }

        $toPolyline = fn($coords) => collect($coords)->map(fn($c) => round($c[0], 1) . ',' . round($c[1], 1))->implode(' ');

        $histPolyline = $toPolyline($histCoords);
        $forePolyline = $toPolyline($foreCoords);

        $bandCoords = array_merge($foreUpperCoords, array_reverse($foreLowerCoords));
        $bandPolygon = $toPolyline($bandCoords);
    @endphp
    <svg width="{{ $chartW }}" height="{{ $chartH }}" viewBox="0 0 {{ $chartW }} {{ $chartH }}" xmlns="http://www.w3.org/2000/svg">
        {{-- sumbu --}}
        <line x1="{{ $padL }}" y1="{{ $padT }}" x2="{{ $padL }}" y2="{{ $padT + $plotH }}" stroke="#cbd5e1" stroke-width="1" />
        <line x1="{{ $padL }}" y1="{{ $padT + $plotH }}" x2="{{ $padL + $plotW }}" y2="{{ $padT + $plotH }}" stroke="#cbd5e1" stroke-width="1" />

        {{-- rentang batas bawah-atas (confidence interval) --}}
        @if($bandPolygon)
            <polygon points="{{ $bandPolygon }}" fill="#fde68a" fill-opacity="0.45" stroke="none" />
        @endif

        {{-- garis actual (historis) --}}
        @if($histPolyline)
            <polyline points="{{ $histPolyline }}" fill="none" stroke="#2563eb" stroke-width="2" />
        @endif

        {{-- garis prediksi --}}
        @if($forePolyline)
            <polyline points="{{ $forePolyline }}" fill="none" stroke="#f59e0b" stroke-width="2" />
        @endif

        {{-- label sumbu Y (min/max) --}}
        <text x="2" y="{{ $padT + 8 }}" font-size="9" fill="#64748b">{{ number_format($maxY, 0) }}</text>
        <text x="2" y="{{ $padT + $plotH }}" font-size="9" fill="#64748b">{{ number_format($minY, 0) }}</text>
    </svg>
    <div class="chart-legend">
        <span class="legend-dot" style="background:#2563eb;"></span> Actual (Historis) &nbsp;&nbsp;
        <span class="legend-dot" style="background:#f59e0b;"></span> Prediksi &nbsp;&nbsp;
        <span class="legend-dot" style="background:#fde68a;"></span> Rentang Batas Bawah-Atas
    </div>

    {{-- Seberapa akurat --}}
    <div class="section-title">Seberapa Akurat Prediksi Ini?</div>
    @if($selectedResult)
        <table class="metric-table">
            <tr>
                <td style="width:25%">
                    <div class="metric-label">RMSE</div>
                    <div class="metric-value">{{ number_format($selectedResult->rmse, 3) }}</div>
                </td>
                <td style="width:25%">
                    <div class="metric-label">MAE</div>
                    <div class="metric-value">{{ number_format($selectedResult->mae, 3) }}</div>
                </td>
                <td style="width:25%">
                    <div class="metric-label">MAPE</div>
                    <div class="metric-value">{{ number_format($selectedResult->mase_atau_wape, 2) }}%</div>
                </td>
                <td style="width:25%">
                    <div class="metric-label">AIC</div>
                    <div class="metric-value">{{ $selectedResult->aic_aicc !== null ? number_format($selectedResult->aic_aicc, 1) : '-' }}</div>
                </td>
            </tr>
        </table>
        <div class="explain-box">
            <strong>RMSE</strong> & <strong>MAE</strong> menunjukkan rata-rata selisih prediksi dari angka
            aslinya (dalam satuan unit barang) &mdash; makin kecil makin akurat. <strong>MAPE</strong> adalah
            versi persennya. <strong>AIC</strong> hanya berlaku untuk model ARIMA, dipakai membandingkan
            kecocokan model tanpa dibuat terlalu rumit.
        </div>
    @endif

    {{-- Rincian per hari --}}
    <div class="section-title">Rincian Hasil Prediksi ({{ $prediksi->selected_model }})</div>
    <div class="explain-box">
        "Prediksi Kebutuhan" adalah angka perkiraan paling mungkin. "Batas Bawah"/"Batas Atas" adalah
        rentang realistisnya. <strong>t+1, t+2, dst</strong> berarti "1 hari, 2 hari, dst setelah data
        historis terakhir".
    </div>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Langkah</th>
                <th>Prediksi Kebutuhan</th>
                <th>Batas Bawah</th>
                <th>Batas Atas</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($forecastValues as $fv)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($fv->forecast_date)->format('d M Y') }}</td>
                    <td>t+{{ $fv->horizon_step }}</td>
                    <td>{{ number_format($fv->predicted_requirement, 2) }}</td>
                    <td>{{ number_format($fv->lower_bound, 2) }}</td>
                    <td>{{ number_format($fv->upper_bound, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Perbandingan semua model --}}
    <div class="section-title">Perbandingan Semua Model yang Diuji</div>
    <div class="explain-box">
        Sebelum menentukan model <strong>{{ $prediksi->selected_model }}</strong>, sistem mencoba 3 model
        (SES, ARIMA, HWES) dan membandingkan tingkat kesalahannya. Model dengan RMSE terkecil jadi rekomendasi.
    </div>
    <table>
        <thead>
            <tr>
                <th>Model</th>
                <th>Parameter</th>
                <th>RMSE</th>
                <th>MAE</th>
                <th>MAPE</th>
                <th>AIC</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($prediksi->modelResults as $mr)
                <tr class="{{ $mr->is_selected ? 'row-selected' : '' }}">
                    <td>{{ $mr->model_name }}{{ $mr->is_selected ? ' (Dipilih)' : '' }}</td>
                    <td>{{ $mr->model_parameters }}</td>
                    <td>{{ number_format($mr->rmse, 3) }}</td>
                    <td>{{ number_format($mr->mae, 3) }}</td>
                    <td>{{ number_format($mr->mase_atau_wape, 2) }}%</td>
                    <td>{{ $mr->aic_aicc !== null ? number_format($mr->aic_aicc, 1) : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">Dokumen digenerate otomatis oleh sistem RAKSAKTI - Fitur Prediksi Barang Keluar.</div>
</body>
</html>