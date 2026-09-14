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
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        position: relative;
        z-index: 1;
    }
    .summary-stat {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 12px;
        padding: 14px 20px;
        min-width: 150px;
        flex: 1 1 150px;
    }
    .summary-stat .summary-icon {
        font-size: 12px;
        margin-bottom: 5px;
        opacity: 0.85;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        font-weight: 700;
    }
    .summary-stat .summary-number {
        font-size: 26px;
        font-weight: 800;
        line-height: 1.1;
    }
    .summary-stat .summary-unit {
        font-size: 12px;
        opacity: 0.85;
        margin-top: 3px;
    }

    /* Card highlight hijau khusus untuk "Total Kebutuhan (unit)" - ukurannya ngikutin isi, nggak melebar */
    .summary-stat-highlight {
        display: inline-flex;
        flex-direction: column;
        align-items: flex-start;
        flex: 0 1 auto;
        width: fit-content;
        background: rgba(34, 197, 94, 0.85);
        border: 1px solid rgba(220, 252, 231, 0.7);
        box-shadow: 0 4px 14px rgba(4, 120, 87, 0.35);
    }
    .summary-stat-highlight .summary-icon { color: #052e19; opacity: 0.75; }
    .summary-stat-highlight .summary-number { color: #ffffff; }
    .summary-stat-highlight .summary-unit { color: #ffffff; opacity: 1; font-weight: 700; }
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
        box-shadow: 0 2px 8px rgba(64, 61, 61, 0.04);
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

    /* Kuadran / badge tingkat akurasi berdasarkan MAPE (Lewis, 1982) */
    .accuracy-badge {
        display: flex;
        align-items: center;
        gap: 16px;
        border-radius: 14px;
        padding: 18px 22px;
        margin-bottom: 22px;
        border: 1px solid transparent;
    }
    .accuracy-icon { font-size: 30px; line-height: 1; }
    .accuracy-text { flex: 1; }
    .accuracy-label {
        font-size: 16px;
        font-weight: 800;
        margin-bottom: 3px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .accuracy-desc { font-size: 13px; opacity: 0.9; line-height: 1.5; }
    .accuracy-mape {
        font-size: 13px;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 20px;
        background: rgba(255,255,255,0.6);
        white-space: nowrap;
    }

    /* Tombol info bulat (i) di sebelah label kuadran */
    .accuracy-info-btn {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: none;
        background: rgba(255,255,255,0.55);
        color: inherit;
        font-weight: 800;
        font-size: 12px;
        font-family: Georgia, 'Times New Roman', serif;
        font-style: italic;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        padding: 0;
        line-height: 1;
        transition: background 0.15s ease;
    }
    .accuracy-info-btn:hover { background: rgba(255,255,255,0.9); }

    .akurasi-sangat-akurat { background: #ecfdf5; border-color: #a7f3d0; }
    .akurasi-sangat-akurat .accuracy-label { color: #047857; }
    .akurasi-sangat-akurat .accuracy-desc { color: #065f46; }

    .akurasi-akurat { background: #eff6ff; border-color: #bfdbfe; }
    .akurasi-akurat .accuracy-label { color: #1d4ed8; }
    .akurasi-akurat .accuracy-desc { color: #1e40af; }

    .akurasi-cukup-akurat { background: #fffbeb; border-color: #fde68a; }
    .akurasi-cukup-akurat .accuracy-label { color: #b45309; }
    .akurasi-cukup-akurat .accuracy-desc { color: #92400e; }

    .akurasi-tidak-akurat { background: #fef2f2; border-color: #fecaca; }
    .akurasi-tidak-akurat .accuracy-label { color: #b91c1c; }
    .akurasi-tidak-akurat .accuracy-desc { color: #991b1b; }

    /* Modal penjelasan kuadran akurasi */
    #accuracy-modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, 0.6);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    #accuracy-modal-overlay.show { display: flex; }
    .accuracy-modal {
        background: #fff;
        border-radius: 16px;
        max-width: 480px;
        width: 100%;
        padding: 26px 28px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.25);
        position: relative;
    }
    .accuracy-modal-close {
        position: absolute;
        top: 14px;
        right: 16px;
        border: none;
        background: none;
        font-size: 20px;
        color: #9ca3af;
        cursor: pointer;
        line-height: 1;
    }
    .accuracy-modal-close:hover { color: #4b5563; }
    .accuracy-modal h4 {
        margin: 0 0 12px;
        font-size: 16px;
        font-weight: 800;
        color: #111827;
        padding-right: 20px;
    }
    .accuracy-modal p {
        font-size: 13px;
        color: #374151;
        line-height: 1.65;
        margin: 0 0 12px;
    }
    .accuracy-modal table { width: 100%; border-collapse: collapse; margin: 14px 0; }
    .accuracy-modal th, .accuracy-modal td {
        padding: 8px 10px;
        font-size: 12.5px;
        text-align: left;
        border-bottom: 1px solid #f1f5f9;
    }
    .accuracy-modal th { color: #94a3b8; font-weight: 700; text-transform: uppercase; font-size: 11px; }
    .accuracy-modal tr.row-highlight { background: #eff6ff; font-weight: 700; }

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

    /* Section yang bisa dibuka/tutup: judulnya persis gaya .section-title, tapi jadi tombol */
    .section-accordion { margin: 34px 0 14px; }
    .section-accordion summary {
        list-style: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        user-select: none;
    }
    .section-accordion summary::-webkit-details-marker { display: none; }
    .section-accordion summary .stitle {
        font-size: 17px;
        font-weight: 700;
        color: #111827;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-accordion summary .stitle::before {
        content: '';
        width: 4px;
        height: 18px;
        background: #2563eb;
        border-radius: 4px;
        display: inline-block;
    }
    .section-accordion summary .chevron {
        color: #94a3b8;
        font-size: 13px;
        transition: transform 0.2s ease;
    }
    .section-accordion[open] summary .chevron { transform: rotate(180deg); }
    .section-accordion .section-accordion-body { margin-top: 16px; }

    /* Accordion kecil untuk penjelasan detail (tetap dipakai nested di dalam) */
    .accordion {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        background: white;
        margin-bottom: 10px;
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
        margin-bottom: 0;
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

    /* Tabel Rincian Hasil Prediksi - versi dipercantik */
    .rincian-table-wrapper {
        max-height: 480px;
        overflow-y: auto;
        border: 1px solid #eef0f3;
        border-radius: 14px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        margin-bottom: 16px;
    }
    .rincian-table {
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 0;
    }
    .rincian-table th,
    .rincian-table td {
        box-shadow: none;
        border-radius: 0;
        padding: 13px 18px;
        border-bottom: 1px solid #f1f5f9;
    }
    .rincian-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;
        background: #f8fafc;
        box-shadow: inset 0 -1px 0 #e2e8f0;
    }
    .rincian-table tbody tr {
        box-shadow: none;
        border-radius: 0;
        transition: background 0.15s ease;
    }
    .rincian-table tbody tr:last-child td { border-bottom: none; }
    .rincian-table tbody tr:nth-child(even) { background: #fafbfc; }
    .rincian-table tbody tr:hover { background: #eff6ff; box-shadow: none; }
    .rincian-table td.rincian-date {
        text-align: left;
        font-weight: 600;
        color: #111827;
    }
    .rincian-table .rincian-step {
        display: inline-block;
        margin-left: 6px;
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        background: #f1f5f9;
        padding: 2px 8px;
        border-radius: 10px;
        vertical-align: middle;
    }
    .rincian-table td.rincian-value {
        font-weight: 700;
        color: #2563eb;
        font-size: 14px;
    }
    .rincian-actual-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }
    .rincian-actual-done { background: #ecfdf5; color: #047857; }
    .rincian-actual-pending { background: #f1f5f9; color: #94a3b8; font-weight: 600; }
    .rincian-pred-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 700;
        background: #eff6ff;
        color: #1d4ed8;
    }
    .rincian-selisih-badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 700;
    }
    .rincian-selisih-plus { background: #ecfdf5; color: #047857; }
    .rincian-selisih-minus { background: #fef2f2; color: #b91c1c; }

    /* Tombol info bulat "i" di header (th) tabel rincian */
    .th-info-btn {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 1px solid #cbd5e1;
        background: #ffffff;
        color: #64748b;
        font-weight: 800;
        font-size: 10px;
        font-family: Georgia, 'Times New Roman', serif;
        font-style: italic;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        padding: 0;
        line-height: 1;
        margin-left: 5px;
        vertical-align: middle;
        transition: background 0.15s ease, color 0.15s ease, border-color 0.15s ease;
    }
    .th-info-btn:hover { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }

    /* Overlay modal generik untuk penjelasan tiap kolom (pakai style .accuracy-modal yang sama) */
    .rincian-info-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, 0.6);
        z-index: 9999;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .rincian-info-overlay.show { display: flex; }
</style>

<div class="container-laporan">
    <div class="header">
        <div>
            <h2>Detail Prediksi
                <span class="item-sub">{{ $prediksi->item->kode_barang ?? '-' }} - {{ $prediksi->item->nama_barang ?? '-' }}</span>
            </h2>
        </div>
        <div>
            
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
            <div class="summary-stat summary-stat-highlight">
                
                <div class="summary-number">{{ number_format($totalPrediksi, 0, ',', '.') }}</div>
                <span style="color: #000000;"><div class="summary-unit">Total kebutuhan (unit)</div></span>
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
            sebagai patokan awal untuk menyiapkan stok. Untuk melihat seberapa dekat prediksi ini
            dengan kenyataan, cek kolom <strong>Actual</strong> dan <strong>Selisih</strong> di bagian
            "Rincian Hasil Prediksi" di bawah, setelah tanggalnya lewat.
        </div>
    </div>

    {{-- Kurva actual vs forecast --}}
    <div class="section-title">
        Kurva Actual vs Prediksi
        <button type="button" class="th-info-btn"
                onclick="document.getElementById('chart-info-modal').classList.add('show')"
                aria-label="Info warna kurva">i</button>
    </div>
    <div class="chart-card">
        <canvas id="forecastChart" height="110"></canvas>
    </div>

    {{-- Modal penjelasan arti warna di kurva --}}
    <div id="chart-info-modal" class="rincian-info-overlay" onclick="if(event.target===this) this.classList.remove('show')">
        <div class="accuracy-modal">
            <button type="button" class="accuracy-modal-close"
                    onclick="document.getElementById('chart-info-modal').classList.remove('show')" aria-label="Tutup">&times;</button>
            <h4>Arti Warna di Kurva Ini</h4>
            <p>Kurva ini membandingkan data historis dengan hasil prediksi sistem. Berikut arti tiap warnanya:</p>
            <table>
                <tbody>
                    <tr>
                        <td style="width: 28px;"><span style="display:inline-block;width:14px;height:14px;border-radius:3px;background:#2563eb;"></span></td>
                        <td><strong>Actual (Historis)</strong> — data barang keluar yang sudah benar-benar terjadi, dipakai sebagai dasar perhitungan model.</td>
                    </tr>
                    <tr>
                        <td><span style="display:inline-block;width:14px;height:14px;border-radius:3px;background:#f59e0b;"></span></td>
                        <td><strong>Prediksi</strong> — garis putus-putus, perkiraan sistem untuk hari-hari ke depan.</td>
                    </tr>
                    <tr>
                        <td><span style="display:inline-block;width:14px;height:14px;border-radius:3px;background:#10b981;"></span></td>
                        <td><strong>Actual (Realisasi)</strong> — kalau tanggal prediksinya sudah lewat dan datanya sudah tercatat, garis ini muncul menunjukkan angka sebenarnya, biar kelihatan seberapa dekat prediksi sama kenyataan.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{--
        Kuadran tingkat akurasi (berdasarkan MAPE, skala Lewis 1982).
        Ditaruh langsung di bawah kurva. Tombol "i" bulat membuka modal
        penjelasan kenapa hasil ini masuk ke kategori tersebut.
        MAPE di sini HANYA untuk label interpretasi, bukan untuk memilih
        model (pemilihan model tetap pakai RMSE, lihat $prediksi->selection_metric).
    --}}
    @php
        $mapeValue = $selectedResult->mase_atau_wape ?? null;
        $akurasiLabel = null;
        $akurasiClass = null;
        $akurasiDesc = null;
        $akurasiIcon = null;

        if ($mapeValue !== null) {
            if ($mapeValue < 10) {
                $akurasiLabel = 'Sangat Akurat';
                $akurasiClass = 'akurasi-sangat-akurat';
                $akurasiIcon = '🟢';
                $akurasiDesc = 'Prediksi ini sangat bisa diandalkan rata-rata melesetnya kecil sekali.';
            } elseif ($mapeValue < 20) {
                $akurasiLabel = 'Akurat';
                $akurasiClass = 'akurasi-akurat';
                $akurasiIcon = '🔵';
                $akurasiDesc = 'Prediksi ini cukup bisa diandalkan untuk dijadikan acuan stok.';
            } elseif ($mapeValue < 50) {
                $akurasiLabel = 'Cukup Akurat';
                $akurasiClass = 'akurasi-cukup-akurat';
                $akurasiIcon = '🟡';
                $akurasiDesc = 'Prediksi ini masih wajar dipakai, tapi sebaiknya tetap dicek ulang berkala.';
            } else {
                $akurasiLabel = 'Tidak Akurat';
                $akurasiClass = 'akurasi-tidak-akurat';
                $akurasiIcon = '🔴';
                $akurasiDesc = 'Prediksi ini kurang bisa diandalkan, jangan dijadikan satu-satunya acuan.';
            }
        }
    @endphp

    @if($akurasiLabel)
        <div class="accuracy-badge {{ $akurasiClass }}">
            <div class="accuracy-icon">{{ $akurasiIcon }}</div>
            <div class="accuracy-text">
                <div class="accuracy-label">
                    Tingkat Akurasi: {{ $akurasiLabel }}
                    <button type="button" class="accuracy-info-btn"
                            onclick="document.getElementById('accuracy-modal-overlay').classList.add('show')"
                            aria-label="Info tingkat akurasi">i</button>
                </div>
                <div class="accuracy-desc">{{ $akurasiDesc }}</div>
            </div>
            <div class="accuracy-mape">MAPE {{ number_format($mapeValue, 1) }}%</div>
        </div>

        {{-- Modal penjelasan kenapa masuk kategori ini --}}
        <div id="accuracy-modal-overlay" onclick="if(event.target===this) this.classList.remove('show')">
            <div class="accuracy-modal">
                <button type="button" class="accuracy-modal-close"
                        onclick="document.getElementById('accuracy-modal-overlay').classList.remove('show')"
                        aria-label="Tutup">&times;</button>
                <h4>Kenapa masuk kategori "{{ $akurasiLabel }}"?</h4>
                <p>
                    Nilai <strong>MAPE (Mean Absolute Percentage Error)</strong> hasil prediksi ini adalah
                    <strong>{{ number_format($mapeValue, 2) }}%</strong> — artinya rata-rata prediksi
                    meleset sekitar segitu persen dari angka aslinya. Angka ini dipetakan ke salah satu
                    dari 4 kategori berikut, mengikuti skala interpretasi MAPE dari Lewis (1982):
                </p>
                <table>
                    <thead>
                        <tr><th>Rentang MAPE</th><th>Kategori</th></tr>
                    </thead>
                    <tbody>
                        <tr class="{{ $akurasiLabel === 'Sangat Akurat' ? 'row-highlight' : '' }}">
                            <td>&lt; 10%</td><td>🟢 Sangat Akurat</td>
                        </tr>
                        <tr class="{{ $akurasiLabel === 'Akurat' ? 'row-highlight' : '' }}">
                            <td>10% – 20%</td><td>🔵 Akurat</td>
                        </tr>
                        <tr class="{{ $akurasiLabel === 'Cukup Akurat' ? 'row-highlight' : '' }}">
                            <td>20% – 50%</td><td>🟡 Cukup Akurat</td>
                        </tr>
                        <tr class="{{ $akurasiLabel === 'Tidak Akurat' ? 'row-highlight' : '' }}">
                            <td>&gt; 50%</td><td>🔴 Tidak Akurat</td>
                        </tr>
                    </tbody>
                </table>
                <p style="margin-bottom: 0;">
                    Karena MAPE prediksi ini <strong>{{ number_format($mapeValue, 2) }}%</strong> berada
                    di rentang tersebut, hasilnya dikategorikan sebagai
                    <strong>"{{ $akurasiLabel }}"</strong>. Kategori ini hanya untuk memudahkan membaca
                    hasil model tetap dipilih berdasarkan RMSE terkecil, bukan berdasarkan kategori ini.
                </p>
            </div>
        </div>
    @endif

    {{--
        Rincian Hasil Prediksi - SELALU TAMPIL, langsung di bawah kuadran akurasi.
        Ini bagian yang sengaja tidak disembunyikan.
    --}}
    <div class="section-title">Rincian Hasil Prediksi ({{ $prediksi->selected_model }})</div>

    <div class="note-box">
        📌 <strong>Cara baca tabel ini:</strong> "Prediksi Kebutuhan" adalah angka perkiraan yang paling
        mungkin terjadi untuk tanggal tersebut. Kolom <strong>Actual</strong> menunjukkan angka
        kejadian sebenarnya kalau tanggalnya sudah lewat, dan tertulis "Menunggu" kalau tanggalnya
        belum terjadi. Kolom <strong>Selisih</strong> = Actual − Prediksi: hijau (+) artinya
        realisasi lebih banyak dari perkiraan, merah (−) artinya realisasi lebih sedikit dari
        perkiraan. <strong>t+1, t+2, dst</strong> artinya "1 hari setelah data terakhir",
        "2 hari setelah data terakhir", dan seterusnya.
    </div>

    <div class="rincian-table-wrapper">
        <table class="rincian-table">
            <thead>
                <tr>
                    <th>
                        Tanggal
                        <button type="button" class="th-info-btn"
                                onclick="document.getElementById('th-info-tanggal').classList.add('show')"
                                aria-label="Info kolom Tanggal">i</button>
                    </th>
                    <th>
                        Prediksi Kebutuhan
                        <button type="button" class="th-info-btn"
                                onclick="document.getElementById('th-info-prediksi').classList.add('show')"
                                aria-label="Info kolom Prediksi Kebutuhan">i</button>
                    </th>
                    <th>
                        Actual
                        <button type="button" class="th-info-btn"
                                onclick="document.getElementById('th-info-actual').classList.add('show')"
                                aria-label="Info kolom Actual">i</button>
                    </th>
                    <th>
                        Selisih
                        <button type="button" class="th-info-btn"
                                onclick="document.getElementById('th-info-selisih').classList.add('show')"
                                aria-label="Info kolom Selisih">i</button>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($forecastValues as $fv)
                    <tr>
                        <td class="rincian-date">
                            {{ \Carbon\Carbon::parse($fv->forecast_date)->format('d M Y') }}
                            <span class="rincian-step">t+{{ $fv->horizon_step }}</span>
                        </td>
                        <td>
                            <span class="rincian-pred-badge">{{ number_format($fv->predicted_requirement, 2) }}</span>
                        </td>
                        <td>
                            @if($fv->actual_quantity_out !== null)
                                <span class="rincian-actual-badge rincian-actual-done">{{ number_format($fv->actual_quantity_out, 2) }}</span>
                            @else
                                <span class="rincian-actual-badge rincian-actual-pending">Menunggu</span>
                            @endif
                        </td>
                        <td>
                            @if($fv->actual_quantity_out !== null)
                                @php $selisih = $fv->actual_quantity_out - $fv->predicted_requirement; @endphp
                                <span class="rincian-selisih-badge {{ $selisih >= 0 ? 'rincian-selisih-plus' : 'rincian-selisih-minus' }}">
                                    {{ $selisih >= 0 ? '+' : '' }}{{ number_format($selisih, 2) }}
                                </span>
                            @else
                                <span class="rincian-actual-badge rincian-actual-pending">-</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Modal penjelasan tiap kolom di tabel Rincian Hasil Prediksi --}}
    <div id="th-info-tanggal" class="rincian-info-overlay" onclick="if(event.target===this) this.classList.remove('show')">
        <div class="accuracy-modal">
            <button type="button" class="accuracy-modal-close"
                    onclick="document.getElementById('th-info-tanggal').classList.remove('show')" aria-label="Tutup">&times;</button>
            <h4>Kolom "Tanggal"</h4>
            <p>
                Tanggal ini adalah hari yang diprediksi sistem, dihitung mulai dari sehari setelah
                data historis terakhir yang dipakai. Label <strong>t+1, t+2, dst</strong> menunjukkan
                urutan hari ke berapa dari data terakhir misalnya t+1 berarti prediksi untuk 1 hari
                setelah tanggal terakhir data historis.
            </p>
        </div>
    </div>

    <div id="th-info-prediksi" class="rincian-info-overlay" onclick="if(event.target===this) this.classList.remove('show')">
        <div class="accuracy-modal">
            <button type="button" class="accuracy-modal-close"
                    onclick="document.getElementById('th-info-prediksi').classList.remove('show')" aria-label="Tutup">&times;</button>
            <h4>Kolom "Prediksi Kebutuhan"</h4>
            <p>
                Angka ini dihasilkan oleh model peramalan terpilih (<strong>{{ $prediksi->selected_model }}</strong>)
                berdasarkan pola historis barang keluar item ini. Model mempelajari tren dan (kalau ada)
                pola musiman dari data-data sebelumnya, lalu memproyeksikan perkiraan kebutuhan untuk
                tiap tanggal ke depan.
            </p>
        </div>
    </div>

    <div id="th-info-actual" class="rincian-info-overlay" onclick="if(event.target===this) this.classList.remove('show')">
        <div class="accuracy-modal">
            <button type="button" class="accuracy-modal-close"
                    onclick="document.getElementById('th-info-actual').classList.remove('show')" aria-label="Tutup">&times;</button>
            <h4>Kolom "Actual"</h4>
            <p>
                Angka ini diambil dari data transaksi barang keluar yang sesungguhnya terjadi pada
                tanggal tersebut. Kolom ini otomatis tertulis "Menunggu" selama tanggalnya belum
                lewat atau datanya belum tercatat di sistem begitu ada transaksi barang keluar pada
                tanggal itu, angkanya akan otomatis muncul di sini.
            </p>
        </div>
    </div>

    <div id="th-info-selisih" class="rincian-info-overlay" onclick="if(event.target===this) this.classList.remove('show')">
        <div class="accuracy-modal">
            <button type="button" class="accuracy-modal-close"
                    onclick="document.getElementById('th-info-selisih').classList.remove('show')" aria-label="Tutup">&times;</button>
            <h4>Kolom "Selisih"</h4>
            <p>
                Dihitung dengan rumus <strong>Actual − Prediksi Kebutuhan</strong>. Nilai positif
                (hijau) artinya jumlah barang keluar sebenarnya lebih banyak dari perkiraan sistem,
                dan nilai negatif (merah) artinya lebih sedikit dari perkiraan.
            </p>
            <p style="margin-bottom: 0;">
                Kolom ini membantu melihat akurasi prediksi per hari, sebagai pelengkap dari
                RMSE/MAPE yang menghitung rata-rata error untuk keseluruhan periode prediksi.
            </p>
        </div>
    </div>

    {{--
        Seberapa Akurat Prediksi Ini? (3 kartu metrik: RMSE, MAE, MAPE)
        Sekarang disembunyikan by default - judulnya sendiri jadi tombol buka/tutup.
    --}}
    @if($selectedResult)
        <details class="section-accordion">
            <summary>
                <span class="stitle">Seberapa Akurat Prediksi Ini?</span>
                <span class="chevron">▼</span>
            </summary>
            <div class="section-accordion-body">
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

                {{-- Detail/penjelasan angka error - tetap disembunyikan lagi (nested) --}}
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
                            <div class="explain-title">📏 MAE {{ number_format($selectedResult->mae, 3) }}</div>
                            <div class="explain-desc">
                                Mirip seperti RMSE, ini juga rata-rata selisih antara prediksi dan kejadian
                                sebenarnya. Bedanya, MAE menghitung rata-rata secara "adil" ke semua hari,
                                sedangkan RMSE lebih menghukum hari-hari yang melesetnya jauh banget. Kalau MAE
                                dan RMSE nilainya berdekatan, artinya kesalahan prediksinya relatif konsisten
                                (nggak ada hari yang meleset ekstrem).
                            </div>
                        </div>
                        <div class="explain-item">
                            <div class="explain-title">📊 MAPE {{ number_format($selectedResult->mase_atau_wape, 2) }}%</div>
                            <div class="explain-desc">
                                Ini versi persentase-nya, biar lebih gampang dibayangkan: rata-rata, prediksi
                                sistem ini meleset sekitar
                                <strong>{{ number_format($selectedResult->mase_atau_wape, 1) }}%</strong>
                                dari angka aslinya di tiap hari. Angka MAPE inilah yang dipetakan jadi label
                                "Tingkat Akurasi" di atas, mengikuti skala Lewis (1982): di bawah 10% = Sangat
                                Akurat, 10-20% = Akurat, 20-50% = Cukup Akurat, di atas 50% = Tidak Akurat.
                            </div>
                        </div>
                        @if($selectedResult->aic_aicc !== null)
                        <div class="explain-item">
                            <div class="explain-title">⚙️ AIC {{ number_format($selectedResult->aic_aicc, 1) }}</div>
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
            </div>
        </details>
    @endif

    {{--
        Perbandingan Semua Model yang Diuji.
        Disembunyikan by default judulnya jadi tombol buka/tutup, sama seperti bagian di atas.
    --}}
    <details class="section-accordion">
        <summary>
            <span class="stitle">Perbandingan Semua Model yang Diuji</span>
            <span class="chevron">▼</span>
        </summary>
        <div class="section-accordion-body">
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
    const actualForecastData = {!! json_encode($forecastValues->pluck('actual_quantity_out')->values()) !!};

    const allLabels = [...historicalLabels, ...forecastLabels];

    // Cek apakah ada data actual yang sudah terisi di periode forecast (periode yang sudah lewat)
    const hasActualForecastData = actualForecastData.some(v => v !== null && v !== undefined);

    // padding null di area historis supaya garis forecast mulai pas di ujung actual
    const paddedForecast = [...Array(historicalLabels.length - 1).fill(null), historicalData[historicalData.length - 1], ...forecastData];
    // garis actual (realisasi) di periode forecast, disambung dari titik actual historis terakhir
    const paddedActualForecast = [...Array(historicalLabels.length - 1).fill(null), historicalData[historicalData.length - 1], ...actualForecastData];

    const chartDatasets = [
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
    ];

    // Garis actual (realisasi) hanya ditampilkan kalau memang ada datanya
    if (hasActualForecastData) {
        chartDatasets.push({
            label: 'Actual (Realisasi)',
            data: paddedActualForecast,
            borderColor: '#10b981',
            backgroundColor: 'transparent',
            borderWidth: 2,
            pointRadius: 3,
            pointBackgroundColor: '#10b981',
            tension: 0.2,
        });
    }

    new Chart(document.getElementById('forecastChart').getContext('2d'), {
        type: 'line',
        data: {
            labels: allLabels,
            datasets: chartDatasets
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });
</script>
@endsection