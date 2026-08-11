@extends('layouts.app')

@section('content')
<style>
    .container-laporan {
        max-width: 1200px;
        margin: auto;
        padding: 30px 20px;
        font-family: 'Segoe UI', sans-serif;
    }
    .header {
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }
    .back-button {
        padding: 8px 14px;
        background-color: #e5e7eb;
        color: #111827;
        border-radius: 6px;
        font-size: 14px;
        text-decoration: none;
    }
    .back-button:hover { background-color: #d1d5db; }
    .btn-pdf {
        padding: 8px 16px;
        background-color: #2563eb;
        color: #fff;
        border-radius: 6px;
        font-size: 14px;
        text-decoration: none;
    }
    .btn-pdf:hover { background-color: #1d4ed8; }

    .info-card {
        background-color: #f9fafb;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 18px 20px;
        margin-bottom: 25px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 14px;
    }
    .info-card .label { font-size: 12px; color: #6b7280; margin-bottom: 2px; }
    .info-card .value { font-size: 15px; font-weight: 600; color: #111827; }

    .metric-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 14px;
        margin-bottom: 25px;
    }
    .metric-card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 16px;
        text-align: center;
        box-shadow: 0px 2px 4px rgba(0,0,0,0.04);
    }
    .metric-card .metric-label { font-size: 13px; color: #6b7280; }
    .metric-card .metric-value { font-size: 22px; font-weight: 700; color: #2563eb; margin-top: 4px; }

    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
        margin-bottom: 25px;
    }
    th, td { padding: 10px 12px; background-color: white; text-align: center; }
    th { background-color: #f3f4f6; font-weight: 600; border-bottom: 1px solid #ddd; }
    tr { box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.05); border-radius: 8px; }

    .badge-model {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        color: #fff;
    }
    .badge-ses { background-color: #10b981; }
    .badge-hwes { background-color: #8b5cf6; }
    .badge-arima { background-color: #f59e0b; }
    .row-selected { background-color: #ecfdf5 !important; }

    .section-title { font-size: 17px; font-weight: 600; margin: 30px 0 12px; }
</style>

<div class="container-laporan">
    <div class="header">
        <h2>Detail Prediksi — {{ $prediksi->item->nama_barang ?? '-' }}</h2>
        <div>
            <a href="{{ route('prediksi.index') }}" class="back-button">← Kembali ke History</a>
            <a href="{{ route('prediksi.pdf', $prediksi->id) }}" class="btn-pdf" target="_blank">📄 Export PDF</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Info umum run --}}
    <div class="info-card">
        <div>
            <div class="label">Item</div>
            <div class="value">{{ $prediksi->item->kode_barang }} - {{ $prediksi->item->nama_barang }}</div>
        </div>
        <div>
            <div class="label">Periode Data Historis</div>
            <div class="value">{{ \Carbon\Carbon::parse($prediksi->data_start)->format('d M Y') }} — {{ \Carbon\Carbon::parse($prediksi->data_end)->format('d M Y') }}</div>
        </div>
        <div>
            <div class="label">Frekuensi</div>
            <div class="value">{{ ucfirst($prediksi->frequency) }}</div>
        </div>
        <div>
            <div class="label">Horizon Prediksi</div>
            <div class="value">{{ $prediksi->horizon }} hari ke depan</div>
        </div>
        <div>
            <div class="label">Model Terpilih</div>
            <div class="value">
                @php
                    $badgeClass = match($prediksi->selected_model) {
                        'SES' => 'badge-ses', 'HWES' => 'badge-hwes', 'ARIMA' => 'badge-arima', default => 'badge-ses',
                    };
                @endphp
                <span class="badge-model {{ $badgeClass }}">{{ $prediksi->selected_model }}</span>
            </div>
        </div>
        <div>
            <div class="label">Metrik Seleksi</div>
            <div class="value">{{ $prediksi->selection_metric }}</div>
        </div>
    </div>

    {{-- 3 nilai error model terpilih --}}
    @if($selectedResult)
        <div class="metric-cards">
            <div class="metric-card">
                <div class="metric-label">RMSE</div>
                <div class="metric-value">{{ number_format($selectedResult->rmse, 3) }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">MAE</div>
                <div class="metric-value">{{ number_format($selectedResult->mae, 3) }}</div>
            </div>
            <div class="metric-card">
                <div class="metric-label">WAPE</div>
                <div class="metric-value">{{ number_format($selectedResult->mase_atau_wape, 2) }}%</div>
            </div>
            @if($selectedResult->aic_aicc !== null)
                <div class="metric-card">
                    <div class="metric-label">AIC</div>
                    <div class="metric-value">{{ number_format($selectedResult->aic_aicc, 1) }}</div>
                </div>
            @endif
        </div>
    @endif

    {{-- Kurva actual vs forecast --}}
    <div class="section-title">Kurva Actual vs Prediksi</div>
    <canvas id="forecastChart" height="110"></canvas>

    {{-- Perbandingan semua model yang diuji --}}
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
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($prediksi->modelResults as $mr)
                <tr class="{{ $mr->is_selected ? 'row-selected' : '' }}">
                    <td>
                        @php
                            $bc = match($mr->model_name) {
                                'SES' => 'badge-ses', 'HWES' => 'badge-hwes', 'ARIMA' => 'badge-arima', default => 'badge-ses',
                            };
                        @endphp
                        <span class="badge-model {{ $bc }}">{{ $mr->model_name }}</span>
                        @if($mr->is_selected) <strong>✔ Dipilih</strong> @endif
                    </td>
                    <td><small>{{ $mr->model_parameters }}</small></td>
                    <td>{{ number_format($mr->rmse, 3) }}</td>
                    <td>{{ number_format($mr->mae, 3) }}</td>
                    <td>{{ number_format($mr->mase_atau_wape, 2) }}%</td>
                    <td>{{ $mr->aic_aicc !== null ? number_format($mr->aic_aicc, 1) : '-' }}</td>
                    <td>{{ $mr->diagnostic_status ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Tabel nilai hasil prediksi per hari --}}
    <div class="section-title">Rincian Hasil Prediksi ({{ $prediksi->selected_model }})</div>
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Prediksi Kebutuhan</th>
                <th>Batas Bawah</th>
                <th>Batas Atas</th>
                <th>Actual (jika sudah lewat)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($forecastValues as $fv)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($fv->forecast_date)->format('d M Y') }} (t+{{ $fv->horizon_step }})</td>
                    <td>{{ number_format($fv->predicted_requirement, 2) }}</td>
                    <td>{{ number_format($fv->lower_bound, 2) }}</td>
                    <td>{{ number_format($fv->upper_bound, 2) }}</td>
                    <td>{{ $fv->actual_quantity_out !== null ? number_format($fv->actual_quantity_out, 2) : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const historicalLabels = {!! json_encode($historicalSeries->pluck('tanggal')->values()) !!};
    const historicalData   = {!! json_encode($historicalSeries->pluck('y')->values()) !!};

    const forecastLabels = {!! json_encode($forecastValues->pluck('forecast_date')->values()) !!};
    const forecastData   = {!! json_encode($forecastValues->pluck('predicted_requirement')->values()) !!};
    const lowerData       = {!! json_encode($forecastValues->pluck('lower_bound')->values()) !!};
    const upperData       = {!! json_encode($forecastValues->pluck('upper_bound')->values()) !!};

    const allLabels = [...historicalLabels, ...forecastLabels];

    // padding null di area historis supaya garis forecast mulai pas di ujung actual
    const paddedForecast = [...Array(historicalLabels.length - 1).fill(null), historicalData[historicalData.length - 1], ...forecastData];
    const paddedLower = [...Array(historicalLabels.length).fill(null), ...lowerData];
    const paddedUpper = [...Array(historicalLabels.length).fill(null), ...upperData];

    new Chart(document.getElementById('forecastChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: allLabels,
            datasets: [
                {
                    label: 'Actual (Historis)',
                    data: [...historicalData, ...Array(forecastLabels.length).fill(null)],
                    borderColor: '#2563eb',
                    backgroundColor: 'transparent',
                    tension: 0.2,
                },
                {
                    label: 'Prediksi',
                    data: paddedForecast,
                    borderColor: '#f59e0b',
                    borderDash: [6, 4],
                    backgroundColor: 'transparent',
                    tension: 0.2,
                },
                {
                    label: 'Batas Atas',
                    data: paddedUpper,
                    borderColor: 'rgba(245, 158, 11, 0.3)',
                    backgroundColor: 'transparent',
                    pointRadius: 0,
                    borderWidth: 1,
                },
                {
                    label: 'Batas Bawah',
                    data: paddedLower,
                    borderColor: 'rgba(245, 158, 11, 0.3)',
                    backgroundColor: 'rgba(245, 158, 11, 0.08)',
                    fill: '-1',
                    pointRadius: 0,
                    borderWidth: 1,
                },
            ]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });
</script>
@endsection