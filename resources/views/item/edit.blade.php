@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Item</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('success_threshold'))
        <div class="alert alert-success">{{ session('success_threshold') }}</div>
    @endif

    {{-- ===================== TAB NAVIGASI ===================== --}}
    <ul class="nav nav-tabs mb-3" id="editItemTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="info-barang-tab" data-bs-toggle="tab" data-bs-target="#info-barang" type="button" role="tab">
                Info Barang
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="konfigurasi-threshold-tab" data-bs-toggle="tab" data-bs-target="#konfigurasi-threshold" type="button" role="tab">
                Konfigurasi Threshold
            </button>
        </li>
    </ul>

    <div class="tab-content" id="editItemTabContent">

        {{-- ===================== TAB 1: INFO BARANG (form asli, tidak diubah) ===================== --}}
        <div class="tab-pane fade show active" id="info-barang" role="tabpanel">

            <form action="{{ route('item.update', $item->kode_barang) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Nama Barang --}}
                <div class="mb-3">
                    <label for="nama_barang">Nama Barang</label>
                    <input type="text" name="nama_barang" class="form-control" value="{{ old('nama_barang', $item->nama_barang) }}" required>
                </div>
                {{-- Harga Dasar --}}
                <div class="mb-3">
                    <label for="harga_dasar">harga dasar</label>
                    <input type="number" name="harga_dasar" class="form-control" value="{{ old('harga_dasar', $item->harga_dasar)}}" required>
                </div>
                {{-- Kategori --}}
                <div class="mb-3">
                    <label for="id_kategori">Kategori</label>
                    <select name="id_kategori" class="form-control" required>
                        @foreach ($kategori as $k)
                            <option value="{{ $k->id }}" {{ old('id_kategori', $item->id_kategori) == $k->id ? 'selected' : '' }}>
                                {{ $k->kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Satuan --}}
                <div class="mb-3">
                    <label for="id_satuan">Satuan</label>
                    <select name="id_satuan" class="form-control" required>
                        @foreach ($satuan as $s)
                            <option value="{{ $s->id }}" {{ old('id_satuan', $item->id_satuan) == $s->id ? 'selected' : '' }}>
                                {{ $s->nama_satuan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Stok Minimum --}}
                <div class="mb-3">
                    <label for="stok_minimum">Stok Minimum</label>
                    <input type="number" name="stok_minimum" class="form-control" value="{{ old('stok_minimum', $item->stok_minimum ?? '') }}" required>
                </div>

                {{-- Catatan / Deskripsi --}}
                <div class="mb-3">
                    <label for="deskripsi">Catatan</label>
                    <textarea name="deskripsi" class="form-control" rows="2">{{ old('deskripsi', $item->deskripsi) }}</textarea>
                </div>

                {{-- Foto --}}
                <div class="mb-3">
                    <label for="foto">Foto</label>
                    <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror">
                    @if ($item->foto)
                        <div class="mt-2">
                            <img src="{{ asset('storage/' . $item->foto) }}" alt="Foto" width="100" height="100">
                        </div>
                    @endif
                    @error('foto')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
            </form>

        </div>

        {{-- ===================== TAB 2: KONFIGURASI THRESHOLD (form baru, controller & route terpisah) ===================== --}}
        <div class="tab-pane fade" id="konfigurasi-threshold" role="tabpanel">

            @php
                // Ambil konfigurasi threshold yang sudah tersimpan untuk item ini
                // (kalau belum pernah dikonfigurasi, akan null dan form memakai
                // nilai default sesuai calculateAndSaveThreshold()).
                $thresholdConfig = \Illuminate\Support\Facades\DB::table('stock_thresholds')
                    ->where('item_id', $item->kode_barang)
                    ->first();
                $currentAdc = $thresholdConfig->adc ?? 0;
                $currentLead = old('lead_time_days', $thresholdConfig->lead_time_days ?? 3);
                $currentSafety = old('safety_stock_days', $thresholdConfig->safety_stock_days ?? 1);
                $currentResponse = old('response_time_days', $thresholdConfig->response_time_days ?? 1);

                // Stok aktual saat ini (barang masuk - barang keluar), dihitung
                // dengan cara yang sama persis seperti AdaptiveThresholdService::getCurrentStock().
                // Dihitung langsung di sini (bukan lewat service) supaya tidak perlu
                // mengubah ItemController@edit milik Bayu.
                $totalMasuk = \Illuminate\Support\Facades\DB::table('barang_masuks')
                    ->where('kode_barang', $item->kode_barang)->sum('jumlah');
                $totalKeluar = \Illuminate\Support\Facades\DB::table('barang_keluars')
                    ->where('kode_barang', $item->kode_barang)->sum('jumlah_keluar');
                $currentStock = $totalMasuk - $totalKeluar;
            @endphp

            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

            <style>
                .tc-wrap { font-family: 'Inter', sans-serif; color: #000; }

                .tc-intro {
                    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
                    border-radius: 20px;
                    padding: 1.25rem 1.5rem;
                    color: #fff;
                    margin-bottom: 1.5rem;
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                }

                .tc-intro .tc-intro-icon {
                    width: 44px;
                    height: 44px;
                    border-radius: 50%;
                    background: rgba(249,115,22,0.2);
                    color: #f97316;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.2rem;
                    flex-shrink: 0;
                }

                .tc-intro .tc-intro-title {
                    font-weight: 700;
                    margin-bottom: 0.15rem;
                }

                .tc-intro .tc-intro-desc {
                    color: #cbd5e1;
                    font-size: 0.86rem;
                    margin: 0;
                }

                .tc-stats {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
                    gap: 1rem;
                    margin-bottom: 1.75rem;
                }

                .tc-stat-card {
                    background: #fff;
                    border-radius: 18px;
                    padding: 1.1rem 1.3rem;
                    box-shadow: 0 3px 12px rgba(15,23,42,0.06);
                    display: flex;
                    align-items: center;
                    gap: 0.85rem;
                    transition: transform 0.15s ease;
                }

                .tc-stat-card:hover { transform: translateY(-2px); }

                .tc-stat-icon {
                    width: 44px;
                    height: 44px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    font-size: 1.05rem;
                    background: var(--tc-soft, #eee);
                    color: var(--tc-accent, #999);
                    flex-shrink: 0;
                }

                .tc-stat-value {
                    font-size: 1.4rem;
                    font-weight: 700;
                    color: #0f172a;
                    line-height: 1.1;
                    transition: color 0.15s ease;
                }

                .tc-stat-label {
                    font-size: 0.78rem;
                    color: #64748b;
                    font-weight: 600;
                }

                .tc-form-group { margin-bottom: 1.35rem; }

                .tc-label {
                    display: block;
                    font-weight: 600;
                    font-size: 0.9rem;
                    color: #0f172a;
                    margin-bottom: 0.4rem;
                }

                .tc-input {
                    width: 100%;
                    border: 1.5px solid #e2e8f0;
                    border-radius: 12px;
                    padding: 0.65rem 1rem;
                    font-size: 0.95rem;
                    font-family: 'Inter', sans-serif;
                    transition: border-color 0.15s ease, box-shadow 0.15s ease;
                }

                .tc-input:focus {
                    outline: none;
                    border-color: #f97316;
                    box-shadow: 0 0 0 4px rgba(249,115,22,0.12);
                }

                .tc-help {
                    display: block;
                    font-size: 0.8rem;
                    color: #94a3b8;
                    margin-top: 0.35rem;
                }

                .tc-meta {
                    font-size: 0.82rem;
                    color: #94a3b8;
                    margin-bottom: 1.25rem;
                }

                .tc-submit {
                    background: #f97316;
                    color: #fff;
                    border: none;
                    border-radius: 12px;
                    padding: 0.7rem 1.6rem;
                    font-size: 0.95rem;
                    font-weight: 700;
                    box-shadow: 0 6px 16px rgba(249,115,22,0.3);
                    transition: transform 0.15s ease, background 0.15s ease;
                }

                .tc-submit:hover {
                    background: #ea6a0c;
                    color: #fff;
                    transform: translateY(-1px);
                }

                .tc-preview-note {
                    font-size: 0.78rem;
                    color: #f97316;
                    font-weight: 600;
                    margin-top: 0.3rem;
                }

                .tc-section-title {
                    font-weight: 700;
                    font-size: 1rem;
                    color: #0f172a;
                    margin-bottom: 0.75rem;
                }

                .tc-gauge-box {
                    background: #fff;
                    border-radius: 18px;
                    padding: 1.25rem 1.5rem;
                    box-shadow: 0 3px 12px rgba(15,23,42,0.06);
                    margin-bottom: 1.5rem;
                }

                .tc-gauge-current {
                    font-size: 0.88rem;
                    color: #475569;
                    margin-bottom: 0.75rem;
                }

                .tc-gauge-track {
                    position: relative;
                    height: 16px;
                    border-radius: 999px;
                    overflow: visible;
                    display: flex;
                    background: #f1f5f9;
                    margin-bottom: 2rem;
                }

                .tc-gauge-zone {
                    height: 100%;
                    transition: width 0.2s ease;
                }

                .tc-gauge-zone:first-child { border-radius: 999px 0 0 999px; }
                .tc-gauge-zone:last-child { border-radius: 0 999px 999px 0; }

                .tc-gauge-marker {
                    position: absolute;
                    top: -4px;
                    width: 2px;
                    height: 24px;
                    background: #0f172a;
                    transition: left 0.2s ease;
                }

                .tc-gauge-marker-label {
                    position: absolute;
                    top: 24px;
                    font-size: 0.72rem;
                    font-weight: 700;
                    color: #0f172a;
                    white-space: nowrap;
                    transition: left 0.2s ease;
                }

                .tc-gauge-legend {
                    display: flex;
                    gap: 1.25rem;
                    margin-top: 1.25rem;
                    flex-wrap: wrap;
                }

                .tc-gauge-legend span {
                    font-size: 0.78rem;
                    color: #64748b;
                    display: flex;
                    align-items: center;
                    gap: 0.4rem;
                }

                .tc-gauge-legend i {
                    width: 10px;
                    height: 10px;
                    border-radius: 50%;
                    display: inline-block;
                }

                .tc-formula-box {
                    background: #fff;
                    border-radius: 18px;
                    padding: 1.25rem 1.5rem;
                    box-shadow: 0 3px 12px rgba(15,23,42,0.06);
                    margin-bottom: 1.75rem;
                }

                .tc-formula-item {
                    padding: 0.85rem 0;
                    border-bottom: 1px dashed #e2e8f0;
                }

                .tc-formula-item:last-child { border-bottom: none; padding-bottom: 0; }
                .tc-formula-item:first-of-type { padding-top: 0; }

                .tc-formula-name {
                    font-weight: 600;
                    font-size: 0.88rem;
                    color: #0f172a;
                    margin-bottom: 0.3rem;
                }

                .tc-formula-calc {
                    font-family: 'Courier New', monospace;
                    font-size: 0.92rem;
                    color: #f97316;
                    font-weight: 700;
                    background: #fff7ed;
                    padding: 0.5rem 0.85rem;
                    border-radius: 10px;
                    display: inline-block;
                }

                .tc-citation {
                    font-size: 0.76rem;
                    color: #94a3b8;
                    font-style: italic;
                    margin-top: 0.5rem;
                }
            </style>

            <div class="tc-wrap">

                <div class="tc-intro">
                    <div class="tc-intro-icon"><i class="fa-solid fa-sliders"></i></div>
                    <div>
                        <div class="tc-intro-title">Konfigurasi Threshold</div>
                        <p class="tc-intro-desc">Atur kapan barang ini dianggap Rendah atau Kritis. Angka di bawah otomatis dihitung ulang tiap kamu mengetik.</p>
                    </div>
                </div>

                {{-- Kartu statistik: live preview, di-update JS saat input berubah --}}
                <div class="tc-stats">
                    <div class="tc-stat-card">
                        <div class="tc-stat-icon" style="--tc-accent:#0dcaf0; --tc-soft:#e0f7fa;"><i class="fa-solid fa-chart-line"></i></div>
                        <div>
                            <div class="tc-stat-value" id="tc-adc-value">{{ number_format($currentAdc, 2) }}</div>
                            <div class="tc-stat-label">ADC / hari</div>
                        </div>
                    </div>
                    <div class="tc-stat-card">
                        <div class="tc-stat-icon" style="--tc-accent:#f97316; --tc-soft:#ffedd5;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                        <div>
                            <div class="tc-stat-value" id="tc-low-value">{{ number_format($thresholdConfig->low_threshold ?? 0, 2) }}</div>
                            <div class="tc-stat-label">Threshold Rendah</div>
                        </div>
                    </div>
                    <div class="tc-stat-card">
                        <div class="tc-stat-icon" style="--tc-accent:#dc3545; --tc-soft:#fee2e2;"><i class="fa-solid fa-box-open"></i></div>
                        <div>
                            <div class="tc-stat-value" id="tc-critical-value">{{ number_format($thresholdConfig->critical_threshold ?? 0, 2) }}</div>
                            <div class="tc-stat-label">Threshold Kritis</div>
                        </div>
                    </div>
                </div>

                @if ($thresholdConfig)
                    <div class="tc-meta">
                        Terakhir dihitung: {{ \Carbon\Carbon::parse($thresholdConfig->calculated_at)->translatedFormat('d M Y, H:i') }}
                    </div>
                @endif

                {{-- Gauge visual posisi stok --}}
                <div class="tc-gauge-box">
                    <div class="tc-section-title">📍 Posisi Stok Sekarang</div>
                    <div class="tc-gauge-current">Stok aktual saat ini: <strong>{{ $currentStock }}</strong> unit</div>
                    <div class="tc-gauge-track" id="tc-gauge-track">
                        <div class="tc-gauge-zone" id="tc-zone-kritis" style="background:#dc3545;"></div>
                        <div class="tc-gauge-zone" id="tc-zone-rendah" style="background:#f59e0b;"></div>
                        <div class="tc-gauge-zone" id="tc-zone-aman" style="background:#16a34a;"></div>
                        <div class="tc-gauge-marker" id="tc-gauge-marker"></div>
                        <div class="tc-gauge-marker-label" id="tc-gauge-marker-label"></div>
                    </div>
                    <div class="tc-gauge-legend">
                        <span><i style="background:#dc3545;"></i> Kritis</span>
                        <span><i style="background:#f59e0b;"></i> Rendah</span>
                        <span><i style="background:#16a34a;"></i> Aman</span>
                    </div>
                </div>

                {{-- Rincian rumus, ikut berubah live saat form diisi --}}
                <div class="tc-formula-box">
                    <div class="tc-section-title">📐 Cara Sistem Menghitung Angka Ini</div>

                    <div class="tc-formula-item">
                        <div class="tc-formula-name">Threshold Rendah = (ADC × Lead Time) + (ADC × Safety Stock)</div>
                        <div class="tc-formula-calc" id="tc-formula-low">Isi form di bawah untuk lihat perhitungannya</div>
                    </div>

                    <div class="tc-formula-item">
                        <div class="tc-formula-name">Threshold Kritis = ADC × Waktu Respons</div>
                        <div class="tc-formula-calc" id="tc-formula-critical">Isi form di bawah untuk lihat perhitungannya</div>
                    </div>

                    <div class="tc-citation">
                        *ADC (Average Daily Consumption) = rata-rata barang keluar per hari. Formula Threshold Rendah mengadaptasi konsep Reorder Point (ROP) dari Afrizal et al. (2025).
                    </div>
                </div>

                <form action="{{ route('item.threshold.update', $item->kode_barang) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="tc-form-group">
                        <label for="tc_lead_time_days" class="tc-label">Lead Time (hari)</label>
                        <input type="number" id="tc_lead_time_days" name="lead_time_days" class="tc-input" min="1"
                               value="{{ $currentLead }}" required>
                        <span class="tc-help">Berapa hari dari pesan barang sampai barangnya tiba di gudang.</span>
                    </div>

                    <div class="tc-form-group">
                        <label for="tc_safety_stock_days" class="tc-label">Safety Stock / Hari Buffer (hari)</label>
                        <input type="number" id="tc_safety_stock_days" name="safety_stock_days" class="tc-input" min="0"
                               value="{{ $currentSafety }}" required>
                        <span class="tc-help">Stok cadangan jaga-jaga kalau pengiriman telat atau pesanan lagi ramai.</span>
                    </div>

                    <div class="tc-form-group">
                        <label for="tc_response_time_days" class="tc-label">Waktu Respons (hari)</label>
                        <input type="number" id="tc_response_time_days" name="response_time_days" class="tc-input" min="1"
                               value="{{ $currentResponse }}" required>
                        <span class="tc-help">Berapa hari kamu butuh buat bertindak begitu stok jadi Kritis.</span>
                        <div class="tc-preview-note" id="tc-preview-note" style="display:none;">
                            ✨ Pratinjau — nilai belum disimpan
                        </div>
                    </div>

                    <button type="submit" class="tc-submit">Simpan Konfigurasi Threshold</button>
                </form>
            </div>

            <script>
                (function () {
                    const adc = {{ (float) $currentAdc }};
                    const currentStock = {{ (float) $currentStock }};
                    const leadInput = document.getElementById('tc_lead_time_days');
                    const safetyInput = document.getElementById('tc_safety_stock_days');
                    const responseInput = document.getElementById('tc_response_time_days');
                    const lowValueEl = document.getElementById('tc-low-value');
                    const criticalValueEl = document.getElementById('tc-critical-value');
                    const previewNote = document.getElementById('tc-preview-note');
                    const formulaLowEl = document.getElementById('tc-formula-low');
                    const formulaCriticalEl = document.getElementById('tc-formula-critical');
                    const zoneKritis = document.getElementById('tc-zone-kritis');
                    const zoneRendah = document.getElementById('tc-zone-rendah');
                    const zoneAman = document.getElementById('tc-zone-aman');
                    const marker = document.getElementById('tc-gauge-marker');
                    const markerLabel = document.getElementById('tc-gauge-marker-label');

                    function updateAll() {
                        const lead = parseFloat(leadInput.value) || 0;
                        const safety = parseFloat(safetyInput.value) || 0;
                        const response = parseFloat(responseInput.value) || 0;

                        const lowThreshold = (adc * lead) + (adc * safety);
                        const criticalThreshold = adc * response;

                        // Kartu statistik
                        lowValueEl.textContent = lowThreshold.toFixed(2);
                        criticalValueEl.textContent = criticalThreshold.toFixed(2);
                        previewNote.style.display = 'block';

                        // Rincian rumus
                        formulaLowEl.textContent =
                            '(' + adc.toFixed(2) + ' × ' + lead + ') + (' + adc.toFixed(2) + ' × ' + safety + ') = ' + lowThreshold.toFixed(2);
                        formulaCriticalEl.textContent =
                            adc.toFixed(2) + ' × ' + response + ' = ' + criticalThreshold.toFixed(2);

                        // Gauge posisi stok -- skala mengikuti nilai terbesar biar proporsional
                        const maxScale = Math.max(currentStock, lowThreshold * 1.3, 1);
                        const criticalPct = Math.min((criticalThreshold / maxScale) * 100, 100);
                        const lowPct = Math.min((lowThreshold / maxScale) * 100, 100);

                        zoneKritis.style.width = criticalPct + '%';
                        zoneRendah.style.width = Math.max(lowPct - criticalPct, 0) + '%';
                        zoneAman.style.width = Math.max(100 - lowPct, 0) + '%';

                        const stockPct = Math.min((currentStock / maxScale) * 100, 100);
                        marker.style.left = 'calc(' + stockPct + '% - 1px)';

                        markerLabel.textContent = 'Stok kamu: ' + currentStock;
                        markerLabel.style.left = stockPct + '%';
                        if (stockPct < 10) {
                            markerLabel.style.transform = 'translateX(0%)';
                        } else if (stockPct > 90) {
                            markerLabel.style.transform = 'translateX(-100%)';
                        } else {
                            markerLabel.style.transform = 'translateX(-50%)';
                        }
                    }

                    [leadInput, safetyInput, responseInput].forEach(function (el) {
                        el.addEventListener('input', updateAll);
                    });

                    // Tampilkan gauge dengan nilai awal begitu halaman dimuat
                    updateAll();
                    previewNote.style.display = 'none';
                })();
            </script>

        </div>

    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (window.location.hash === '#konfigurasi-threshold') {
            var triggerEl = document.getElementById('konfigurasi-threshold-tab');
            if (triggerEl) {
                new bootstrap.Tab(triggerEl).show();
            }
        }
    });
</script>
@endsection