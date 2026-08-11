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
        .section-title { font-size: 14px; font-weight: 700; margin: 20px 0 8px; }
        .footer { margin-top: 30px; font-size: 10px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <h2>Laporan Hasil Prediksi Barang Keluar</h2>
    <div class="subtitle">Dicetak pada {{ now()->format('d M Y H:i') }}</div>

    <table class="info-table">
        <tr>
            <td>Item</td>
            <td>{{ $prediksi->item->kode_barang }} - {{ $prediksi->item->nama_barang }}</td>
        </tr>
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
        @if($selectedResult)
        <tr>
            <td>RMSE / MAE / WAPE</td>
            <td>{{ number_format($selectedResult->rmse, 3) }} / {{ number_format($selectedResult->mae, 3) }} / {{ number_format($selectedResult->mase_atau_wape, 2) }}%</td>
        </tr>
        @endif
    </table>

    <div class="section-title">Perbandingan Semua Model yang Diuji</div>
    <table>
        <thead>
            <tr>
                <th>Model</th>
                <th>Parameter</th>
                <th>RMSE</th>
                <th>MAE</th>
                <th>WAPE</th>
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

    <div class="section-title">Rincian Hasil Prediksi ({{ $prediksi->selected_model }})</div>
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

    <div class="footer">Dokumen digenerate otomatis oleh sistem RAKSAKTI - Fitur Prediksi Barang Keluar.</div>
</body>
</html>