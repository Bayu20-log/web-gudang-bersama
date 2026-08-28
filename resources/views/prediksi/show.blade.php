@extends('layouts.app')

@section('content')
<style>
    * { box-sizing: border-box; }

    .container-laporan {
        max-width: 1200px;
        margin: auto;
        padding: 30px 20px 60px;
        font-family: 'Segoe UI', sans-serif;
        color: #1f2937;
    }

    .header {
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }
    .header h2 {
        font-size: 22px;
        font-weight: 700;
        margin: 0;
        color: #111827;
    }
    .header .item-sub {
        font-size: 13px;
        color: #6b7280;
        font-weight: 400;
        display: block;
        margin-top: 2px;
    }

    .back-button {
        padding: 9px 16px;
        background-color: #ffffff;
        color: #374151;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.15s ease;
    }
    .back-button:hover { background-color: #f3f4f6; border-color: #9ca3af; }
    .btn-pdf {
        padding: 9px 16px;
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: #fff;
        border-radius: 8px;
        font-size: 14px;
        text-decoration: none;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
        transition: opacity 0.15s ease;
    }
    .btn-pdf:hover { opacity: 0.9; }

    .info-card {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 20px 22px;
        margin-bottom: 22px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 16px;
    }
    .info-card .label {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: #94a3b8;
        margin-bottom: 3px;
        font-weight: 600;
    }
    .info-card .value { font-size: 15px; font-weight: 600; color: #0f172a; }

    /* Ringkasan Hasil Prediksi - card besar di atas kurva */
    .summary-card {
        background: linear-gradient(135deg, #1d4ed8, #2563eb 60%, #3b82f6);
        border-radius: 16px;
        padding: 26px 28px;
        margin-bottom: 26px;
        color: white;
        box-shadow: 0 8px 20px rgba(37, 99, 235, 0.25);
        position: relative;
        overflow: hidden;
    }
    .summary-card::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 160px; height: 160px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
    }
    .summary-card .summary-heading {
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        opacity: 0.85;
        margin-bottom: 4px;
        font-weight: 600;
    }
    .summary-card .summary-title {
        font-size: 15px;
        font-weight: 500;
        opacity: 0.95;
        margin-bottom: 18px;
    }
    .summary-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 18px;
        position: relative;
        z-index: 1;
    }
    .summary-stat .summary-number {
        font-size: 28px;
        font-weight: 800;
        line-height: 1.1;
    }
    .summary-stat .summary-unit {
        font-size: 12px;
        opacity: 0.85;
        margin-top: 2px;
    }
    .summary-note {
        margin-top: 18px;
        padding-top: 16px;
        border-top: 1px solid rgba(255,255,255,0.25);
        font-size: 13px;
        line-height: 1.6;
        opacity: 0.95;
        position: relative;
        z-index: 1;
    }

    .metric-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 14px;
        margin-bottom: 12px;
    }
    .metric-card {
        background: white;
        border: 1px solid #eef0f3;
        border-radius: 12px;
        padding: 18px 16px;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        transition: transform 0.15s ease;
    }
    .metric-card:hover { transform: translateY(-2px); }
    .metric-card .metric-label {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: #94a3b8;
        font-weight: 600;
    }
    .metric-card .metric-value { font-size: 24px; font-weight: 800; color: #2563eb; margin-top: 6px; }

    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 8px;
        margin-bottom: 10px;
    }
    th, td { padding: 11px 12px; background-color: white; text-align: center; font-size: 13.5px; }
    th {
        background-color: #f8fafc;
        font-weight: 700;
        color: #475569;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        border-bottom: 1px solid #e2e8f0;
    }
    tr { box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); border-radius: 10px; transition: box-shadow 0.15s ease; }
    tbody tr:hover { box-shadow: 0 3px 10px rgba(0,0,0,0.08); }

    .badge-model {
        padding: 4px 11px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
        color: #fff;
        letter-spacing: 0.02em;
    }
    .badge-ses { background-color: #10b981; }
    .badge-hwes { background-color: #8b5cf6; }
    .badge-arima { background-color: #f59e0b; }
    .row-selected { background-color: #ecfdf5 !important; }

    .section-title {
        font-size: 17px;
        font-weight: 700;
        margin: 34px 0 14px;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-title::before {
        content: '';
        width: 4px;
        height: 18px;
        background: #2563eb;
        border-radius: 4px;
        display: inline-block;
    }

    /* Accordion / collapsible ("bentangan yang dilipat") */
    .accordion {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: white;
        margin-bottom: 25px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    .accordion summary {
        list-style: none;
        cursor: pointer;
        padding: 15px 20px;
        font-weight: 600;
        font-size: 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8fafc;
        user-select: none;
        transition: background 0.15s ease;
    }
    .accordion summary:hover { background: #f1f5f9; }
    .accordion summary::-webkit-details-marker { display: none; }
    .accordion summary .chevron {
        transition: transform 0.2s ease;
        color: #94a3b8;
        font-size: 12px;
    }
    .accordion[open] summary .chevron { transform: rotate(180deg); }
    .accordion .accordion-body {
        padding: 20px 22px;
        border-top: 1px solid #eef0f3;
    }
    .accordion.small-toggle summary {
        background: transparent;
        padding: 6px 0;
        font-size: 13px;
        color: #2563eb;
        font-weight: 600;
    }
    .accordion.small-toggle {
        border: none;
        margin-bottom: 20px;
        box-shadow: none;
    }
    .accordion.small-toggle .accordion-body {
        border: none;
        background: #f8fafc;
        border-radius: 10px;
        padding: 18px 20px;
        margin-top: 8px;
    }

    .explain-item { margin-bottom: 16px; }
    .explain-item:last-child { margin-bottom: 0; }
    .explain-item .explain-title {
        font-weight: 700;
        font-size: 13.5px;
        color: #111827;
        margin-bottom: 4px;
    }
    .explain-item .explain-desc {
        font-size: 13px;
        color: #4b5563;
        line-height: 1.6;
    }

    .note-box {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        padding: 13px 15px;
        font-size: 13px;
        color: #1e40af;
        margin-bottom: 16px;
        line-height: 1.6;
    }

    .chart-card {
        background: white;
        border: 1px solid #eef0f3;
        border-radius: 14px;
        padding: 22px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        margin-bottom: 10px;
    }
</style>

<div class="container-laporan">
    <div class="header">
        <div>
            <h2>Detail Prediksi
                <span class="item-sub">{{ $prediksi->item->kode_barang ?? '-' }} - {{ $prediksi->item->nama_barang ?? '-' }}</span>
            </h2>
        </div>
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
            <div class="value">{{ \Carbon\Carbon::parse($prediksi->data_start)->format('d M Y') }} - {{ \Carbon\Carbon::parse($prediksi->data_end)->format('d M Y') }}</div>
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

    {{-- Ringkasan Hasil Prediksi - DI ATAS KURVA --}}
    @php
        $totalPrediksi = $forecastValues->sum('predicted_requirement');
        $rataRata = $forecastValues->count() > 0 ? $totalPrediksi / $forecastValues->count() : 0;
        $tanggalMulai = $forecastValues->min('forecast_date');
        $tanggalSelesai = $forecastValues->max('forecast_date');
    @endphp
    <div class="summary-card">
        <div class="summary-heading">Ringkasan Hasil Prediksi</div>
        <div class="summary-title">
            Perkiraan kebutuhan <strong>{{ $prediksi->item->nama_barang }}</strong> untuk
            {{ $prediksi->horizon }} hari ke depan
        </div>
        <div class="summary-stats">
            <div class="summary-stat">
                <div class="summary-number">{{ number_format($totalPrediksi, 0, ',', '.') }}</div>
                <div class="summary-unit">Total kebutuhan (unit)</div>
            </div>
            <div class="summary-stat">
                <div class="summary-number">{{ number_format($rataRata, 1, ',', '.') }}</div>
                <div class="summary-unit">Rata-rata per hari</div>
            </div>
            <div class="summary-stat">
                <div class="summary-number">{{ $prediksi->horizon }} hari</div>
                <div class="summary-unit">
                    @if($tanggalMulai && $tanggalSelesai)
                        {{ \Carbon\Carbon::parse($tanggalMulai)->format('d M') }} - {{ \Carbon\Carbon::parse($tanggalSelesai)->format('d M Y') }}
                    @else
                        Periode prediksi
                    @endif
                </div>
            </div>
        </div>
        <div class="summary-note">
            💡 Angka ini adalah perkiraan sistem berdasarkan pola historis barang keluar, dihitung
            pakai model <strong>{{ $prediksi->selected_model }}</strong>. Gunakan angka "Total kebutuhan"
            sebagai patokan awal untuk menyiapkan stok, tapi tetap perhatikan rentang batas atas/bawah
            di bagian "Rincian Hasil Prediksi" di bawah, karena angka aktual bisa sedikit berbeda.
        </div>
    </div>

    {{-- Kurva actual vs forecast --}}
    <div class="section-title">Kurva Actual vs Prediksi</div>
    <div class="chart-card">
        <canvas id="forecastChart" height="110"></canvas>
    </div>

    {{-- 3 nilai error model terpilih - DI BAWAH KURVA --}}
    @if($selectedResult)
        <div class="section-title">Seberapa Akurat Prediksi Ini?</div>
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
                <div class="metric-label">MAPE</div>
                <div class="metric-value">{{ number_format($selectedResult->mase_atau_wape, 2) }}%</div>
            </div>
            @if($selectedResult->aic_aicc !== null)
                <div class="metric-card">
                    <div class="metric-label">AIC</div>
                    <div class="metric-value">{{ number_format($selectedResult->aic_aicc, 1) }}</div>
                </div>
            @endif
        </div>

        {{-- More Info: penjelasan angka error untuk orang awam --}}
        <details class="accordion small-toggle">
            <summary>ℹ️ Apa maksud angka-angka ini? (klik untuk info lebih lanjut) <span class="chevron">▼</span></summary>
            <div class="accordion-body">
                <div class="explain-item">
                    <div class="explain-title">🎯 RMSE {{ number_format($selectedResult->rmse, 3) }}</div>
                    <div class="explain-desc">
                        Ini kira-kira seberapa besar "rata-rata melesetnya" prediksi dibanding kejadian
                        sebenarnya, dalam satuan barang yang sama. Kalau RMSE-nya
                        <strong>{{ number_format($selectedResult->rmse, 1) }}</strong>, artinya prediksi
                        biasanya meleset sekitar segitu unit dari angka aslinya. Semakin kecil angkanya,
                        semakin akurat prediksinya. RMSE ini juga yang dipakai sistem untuk memilih model
                        terbaik, karena RMSE lebih "peka" terhadap lonjakan tak terduga dibanding metrik lain.
                    </div>
                </div>
                <div class="explain-item">
                    <div class="explain-title">📏 MAE{{ number_format($selectedResult->mae, 3) }}</div>
                    <div class="explain-desc">
                        Mirip seperti RMSE, ini juga rata-rata selisih antara prediksi dan kejadian
                        sebenarnya. Bedanya, MAE menghitung rata-rata secara "adil" ke semua hari,
                        sedangkan RMSE lebih menghukum hari-hari yang melesetnya jauh banget. Kalau MAE
                        dan RMSE nilainya berdekatan, artinya kesalahan prediksinya relatif konsisten
                        (nggak ada hari yang meleset ekstrem).
                    </div>
                </div>
                <div class="explain-item">
                    <div class="explain-title">📊 MAPE{{ number_format($selectedResult->mase_atau_wape, 2) }}%</div>
                    <div class="explain-desc">
                        Ini versi persentase-nya, biar lebih gampang dibayangkan: rata-rata, prediksi
                        sistem ini meleset sekitar
                        <strong>{{ number_format($selectedResult->mase_atau_wape, 1) }}%</strong>
                        dari angka aslinya di tiap hari. Semakin kecil persentasenya, semakin bisa
                        diandalkan prediksinya.
                    </div>
                </div>
                @if($selectedResult->aic_aicc !== null)
                <div class="explain-item">
                    <div class="explain-title">⚙️ AIC{{ number_format($selectedResult->aic_aicc, 1) }}</div>
                    <div class="explain-desc">
                        Angka ini khusus dipakai untuk model ARIMA, untuk membandingkan seberapa "pas"
                        model ini dengan data tanpa dibuat terlalu rumit. Angka ini tidak punya satuan
                        khusus dan hanya berguna kalau dibandingkan dengan AIC dari model ARIMA lain
                        dengan pengaturan berbeda semakin kecil, semakin baik.
                    </div>
                </div>
                @endif
                <div class="note-box" style="margin-top: 14px; margin-bottom: 0;">
                    💡 Intinya: nggak ada prediksi yang 100% tepat. Angka-angka di atas ada supaya kamu
                    tahu seberapa "bisa dipercaya" hasil prediksi ini sebelum dipakai untuk keputusan
                    stok, bukan untuk dihafal rumusnya.
                </div>
            </div>
        </details>
    @endif

    {{-- Rincian Hasil Prediksi - DI ATAS Perbandingan Semua Model, collapsible --}}
    <div class="section-title">Rincian Hasil Prediksi ({{ $prediksi->selected_model }})</div>

    <div class="note-box">
        📌 <strong>Cara baca tabel ini:</strong> "Prediksi Kebutuhan" adalah angka perkiraan yang paling
        mungkin terjadi. "Batas Bawah" dan "Batas Atas" adalah rentang kemungkinan realistisnya
        karena masa depan nggak bisa ditebak 100% pasti, sistem kasih rentang aman: kemungkinan besar
        kebutuhan aslinya ada di antara dua angka itu. <strong>t+1, t+2, dst</strong> artinya "1 hari
        setelah data terakhir", "2 hari setelah data terakhir", dan seterusnya.
    </div>

    <details class="accordion">
        <summary>📅 Lihat rincian prediksi per hari <span class="chevron">▼</span></summary>
        <div class="accordion-body">
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
    </details>

    {{-- Perbandingan semua model - PALING BAWAH, collapsible, dengan penjelasan --}}
    <div class="section-title">Perbandingan Semua Model yang Diuji</div>

    <details class="accordion">
        <summary>🧪 Lihat perbandingan lengkap 3 model (SES, ARIMA, HWES) <span class="chevron">▼</span></summary>
        <div class="accordion-body">
            <div class="note-box">
                Sebelum menentukan model <strong>{{ $prediksi->selected_model }}</strong> sebagai yang
                dipakai, sistem mencoba 3 model peramalan berbeda dan membandingkan tingkat kesalahannya.
                Model dengan RMSE paling kecil biasanya jadi rekomendasi, tapi kamu tetap bisa pilih
                model lain kalau mau membandingkan sendiri.
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

            <div class="explain-item">
                <div class="explain-title">🟢 SES (Simple Exponential Smoothing)</div>
                <div class="explain-desc">
                    Model paling sederhana, nebak masa depan dengan mengambil rata-rata tertimbang dari
                    data-data terbaru (data yang lebih baru dikasih bobot lebih besar). Cocok kalau pola
                    keluar-masuk barangnya cenderung stabil, nggak naik-turun drastis dan nggak ada musim
                    tertentu. Parameter <strong>alpha</strong> menentukan seberapa besar pengaruh data
                    terbaru alpha tinggi berarti model lebih "reaktif" terhadap perubahan terakhir.
                </div>
            </div>
            <div class="explain-item">
                <div class="explain-title">🟣 HWES (Holt-Winters)</div>
                <div class="explain-desc">
                    Lebih canggih dari SES model ini juga memperhitungkan <strong>tren</strong> (naik/turun
                    perlahan) dan <strong>pola musiman</strong> (misal ramai tiap akhir pekan). Cocok kalau
                    barangnya punya pola berulang mingguan. Parameter <strong>alpha, beta, gamma</strong>
                    masing-masing mengatur seberapa cepat model menyesuaikan level, tren, dan pola musiman.
                </div>
            </div>
            <div class="explain-item">
                <div class="explain-title">🟠 ARIMA</div>
                <div class="explain-desc">
                    Model statistik yang mempelajari hubungan antara nilai hari ini dengan nilai beberapa
                    hari sebelumnya. Parameter <strong>p</strong> menentukan berapa hari ke belakang yang
                    dijadikan acuan, dan <strong>d</strong> menentukan berapa kali data "diratakan" dulu
                    (differencing) supaya polanya lebih mudah dibaca sistem. Cocok untuk pola yang lebih
                    kompleks dan nggak terlalu musiman.
                </div>
            </div>
            <div class="explain-item">
                <div class="explain-title">Kenapa parameternya bisa beda-beda tiap prediksi?</div>
                <div class="explain-desc">
                    Sistem otomatis mencoba banyak kombinasi parameter untuk tiap model (proses ini disebut
                    <em>grid search</em>), lalu memilih kombinasi yang menghasilkan RMSE paling kecil pada
                    data historis item ini. Jadi parameter yang muncul di kolom "Parameter" di atas bukan
                    angka tetap itu adalah hasil pencarian otomatis yang paling cocok khusus untuk pola keluar
                    masuk item <strong>{{ $prediksi->item->nama_barang }}</strong>.
                </div>
            </div>
        </div>
    </details>
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