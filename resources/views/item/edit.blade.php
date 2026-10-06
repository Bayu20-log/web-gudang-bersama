@extends('layouts.app')
@section('content')

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<style>
    .btn-orange { background-color: #f97316; color: #fff; border: none; transition: 0.3s; }
    .btn-orange:hover { background-color: #ea580c; color: #fff; }
    .bg-dark-header { background-color: #1e293b !important; color: #fff; border-bottom: 2px solid #f97316; }
    
    .preview-box {
        width: 100%;
        height: 150px;
        border: 2px dashed #cbd5e1;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8fafc;
        overflow: hidden;
        margin-top: 10px;
    }
    .preview-box img { max-width: 100%; max-height: 100%; object-fit: contain; }
    .form-label { font-weight: 500; color: #4b5563; }

    .select2-container--bootstrap-5 .select2-selection {
        background-color: #f8fafc;
        border: 1px solid #dee2e6;
        padding: 0.375rem 0.75rem;
        min-height: 38px;
    }
</style>

<div class="container mt-2 mb-5">
    <h3 class="fw-bold mb-4" style="color: #374151;">Edit Produk</h3>
    
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
                Pengaturan Batas Minimum
            </button>
        </li>
    </ul>

    <div class="tab-content" id="editItemTabContent">

        {{-- ===================== TAB 1: INFO BARANG (form asli, tidak diubah) ===================== --}}
        <div class="tab-pane fade show active" id="info-barang" role="tabpanel">
    <form action="{{ route('item.update', $item->kode_barang) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row g-4 form-grid-row">
            <!-- KOLOM KIRI -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="nama_barang" class="form-label">Nama Produk <span class="text-danger">*</span></label>
                    <input type="text" name="nama_barang" id="nama_barang" class="form-control bg-light" value="{{ old('nama_barang', $item->nama_barang) }}" required>
                </div>
                
                <div class="mb-3">
                    <label for="id_kategori" class="form-label">Kategori <span class="text-danger">*</span></label>
                    <select name="id_kategori" id="id_kategori" class="form-select select2-custom" data-placeholder="Pilih atau cari kategori..." data-modal="#modalKategori" required>
                        <option value=""></option>
                        <option value="ADD_NEW">Buat Kategori Baru</option>
                        @foreach ($kategori as $k)
                            <option value="{{ $k->id }}" {{ old('id_kategori', $item->id_kategori) == $k->id ? 'selected' : '' }}>
                                {{ $k->kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="foto" class="form-label">Foto Produk</label>
                    <div class="d-flex gap-3 align-items-start flex-column flex-sm-row">
                        <div class="preview-box w-100" id="previewBox" style="max-width: 300px;">
                            @if ($item->foto)
                                <img id="imagePreview" src="{{ asset($item->foto) }}" style="display: block;">
                                <span id="previewText" class="text-muted fw-medium" style="display: none;"><i class="bi bi-image me-1"></i>Preview Foto</span>
                            @else
                                <img id="imagePreview" style="display: none;">
                                <span id="previewText" class="text-muted fw-medium"><i class="bi bi-image me-1"></i>Preview Foto</span>
                            @endif
                        </div>
                        <div class="w-100 mt-sm-auto mb-sm-auto text-start">
                            <input type="file" name="foto" id="foto" class="d-none" accept="image/*" onchange="previewFile(event)">
                            <button type="button" class="btn btn-outline-secondary bg-white px-4 fw-medium" onclick="document.getElementById('foto').click()">Ganti Foto</button>
                            <div class="text-muted mt-2" style="font-size: 0.85em;">Biarkan kosong jika tidak ingin mengubah foto.</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN -->
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="harga_dasar" class="form-label">Harga Dasar <span class="text-danger">*</span></label>
                    <input type="number" name="harga_dasar" id="harga_dasar" class="form-control bg-light" value="{{ old('harga_dasar', $item->harga_dasar) }}" required>
                </div>
                
                <div class="mb-3">
                    <label for="id_satuan" class="form-label">Satuan <span class="text-danger">*</span></label>
                    <select name="id_satuan" id="id_satuan" class="form-select select2-custom" data-placeholder="Pilih atau cari satuan..." data-modal="#modalSatuan" required>
                        <option value=""></option>
                        <option value="ADD_NEW">Buat Satuan Baru</option>
                        @foreach ($satuan as $s)
                            <option value="{{ $s->id }}" {{ old('id_satuan', $item->id_satuan) == $s->id ? 'selected' : '' }}>
                                {{ $s->nama_satuan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Catatan / Deskripsi</label>
                    <textarea name="deskripsi" id="deskripsi" class="form-control bg-light" rows="5">{{ old('deskripsi', $item->deskripsi) }}</textarea>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top form-btn-row">
            <a href="{{ route('item.index') }}" class="btn btn-outline-secondary px-4 fw-medium">Batal</a>
            <button type="submit" class="btn btn-orange px-5 fw-bold"><i class="bi bi-save me-2"></i>Perbarui</button>
        </div>
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
            
                /* ===== Kartu angka berwarna (desktop dan HP), tanpa lingkaran ikon ===== */
                .tc-stats { grid-template-columns: repeat(3, 1fr); }
                .tc-stats .tc-stat-card {
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    text-align: center;
                    padding: 1.1rem 0.75rem;
                }
                .tc-stats .tc-stat-icon { display: none; }
                .tc-stats .tc-stat-card:nth-child(1) { background: #e0f7fa; }
                .tc-stats .tc-stat-card:nth-child(2) { background: #ffedd5; }
                .tc-stats .tc-stat-card:nth-child(3) { background: #fee2e2; }
                .tc-stats .tc-stat-card:nth-child(1) .tc-stat-value,
                .tc-stats .tc-stat-card:nth-child(1) .tc-stat-label { color: #0e7490; }
                .tc-stats .tc-stat-card:nth-child(2) .tc-stat-value,
                .tc-stats .tc-stat-card:nth-child(2) .tc-stat-label { color: #9a3412; }
                .tc-stats .tc-stat-card:nth-child(3) .tc-stat-value,
                .tc-stats .tc-stat-card:nth-child(3) .tc-stat-label { color: #b91c1c; }

                /* ===== Tampilan HP ===== */
                @media (max-width: 576px) {
                    #editItemTab {
                        flex-wrap: nowrap;
                        overflow-x: auto;
                        overflow-y: hidden;
                        white-space: nowrap;
                        -webkit-overflow-scrolling: touch;
                    }
                    #editItemTab .nav-item { flex: 0 0 auto; }
                    #editItemTab .nav-link { font-size: 0.85rem; padding: 0.5rem 0.9rem; }

                    .tc-stats { gap: 0.6rem; }
                    .tc-stats .tc-stat-card { padding: 0.85rem 0.35rem; border-radius: 16px; }
                    .tc-stats .tc-stat-value { font-size: 1.35rem; }
                    .tc-stats .tc-stat-label { font-size: 0.68rem; line-height: 1.25; }

                    .tc-submit { width: 100%; }
                }
</style>

            <div class="tc-wrap">

                <div class="tc-intro">
                    <div class="tc-intro-icon"><i class="fa-solid fa-sliders"></i></div>
                    <div>
                        <div class="tc-intro-title">Pengaturan Batas Minimum</div>
                        <p class="tc-intro-desc">Atur kapan barang ini dianggap Rendah atau Kritis. Angka di bawah otomatis dihitung ulang tiap kamu mengetik.</p>
                    </div>
                </div>

                {{-- Kartu statistik: live preview, di-update JS saat input berubah --}}
                <div class="tc-stats">
                    <div class="tc-stat-card">
                        <div class="tc-stat-icon" style="--tc-accent:#0dcaf0; --tc-soft:#e0f7fa;"><i class="fa-solid fa-chart-line"></i></div>
                        <div>
                            <div class="tc-stat-value" id="tc-adc-value">{{ number_format($currentAdc, 2) }}</div>
                            <div class="tc-stat-label">Rata-rata Keluar per Hari</div>
                        </div>
                    </div>
                    <div class="tc-stat-card">
                        <div class="tc-stat-icon" style="--tc-accent:#f97316; --tc-soft:#ffedd5;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                        <div>
                            <div class="tc-stat-value" id="tc-low-value">{{ number_format($thresholdConfig->low_threshold ?? 0, 2) }}</div>
                            <div class="tc-stat-label">Batas Minimum Rendah</div>
                        </div>
                    </div>
                    <div class="tc-stat-card">
                        <div class="tc-stat-icon" style="--tc-accent:#dc3545; --tc-soft:#fee2e2;"><i class="fa-solid fa-box-open"></i></div>
                        <div>
                            <div class="tc-stat-value" id="tc-critical-value">{{ number_format($thresholdConfig->critical_threshold ?? 0, 2) }}</div>
                            <div class="tc-stat-label">Batas Minimum Kritis</div>
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
                        <div class="tc-formula-name">Batas Minimum Rendah = (Rata-rata Keluar per Hari × Lead Time) + (Rata-rata Keluar per Hari × Safety Stock)</div>
                        <div class="tc-formula-calc" id="tc-formula-low">Isi form di bawah untuk lihat perhitungannya</div>
                    </div>

                    <div class="tc-formula-item">
                        <div class="tc-formula-name">Batas Minimum Kritis = Rata-rata Keluar per Hari × Waktu Respons</div>
                        <div class="tc-formula-calc" id="tc-formula-critical">Isi form di bawah untuk lihat perhitungannya</div>
                    </div>

                    <div class="tc-citation">
                        *Rata-rata Keluar per Hari (ADC/Average Daily Consumption) = rata-rata barang keluar per hari. Formula Batas Minimum Rendah mengadaptasi konsep Reorder Point (ROP) dari Afrizal et al. (2025).
                    </div>
                </div>

                <form action="{{ route('item.threshold.update', $item->kode_barang) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="from" value="{{ request('from') }}">

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

                    <button type="submit" class="tc-submit">Simpan Batas Minimum</button>
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

<!-- MODAL KATEGORI & SATUAN -->
<div class="modal fade" id="modalKategori" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-dark-header">
        <h5 class="modal-title fw-bold">Tambah Kategori Baru</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
         <div class="mb-3">
             <label class="form-label fw-medium text-secondary">Nama Kategori <span class="text-danger">*</span></label>
             <input type="text" id="input_kategori_baru" class="form-control" placeholder="Masukkan nama kategori">
             <div id="error_kategori" class="text-danger mt-1 fw-medium" style="display: none; font-size: 0.875em;"></div>
         </div>
         <div class="mb-2">
             <label class="form-label fw-medium text-secondary">Deskripsi <span class="text-muted fw-normal">(Opsional)</span></label>
             <textarea id="input_deskripsi_kategori" class="form-control" rows="2" placeholder="Penjelasan singkat kategori..."></textarea>
         </div>
      </div>
      <div class="modal-footer border-0 pb-4 pe-4">
        <button type="button" class="btn btn-outline-secondary px-4 fw-medium" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-orange px-4 fw-bold" onclick="simpanKategori()" id="btnSaveKategori">Simpan</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="modalSatuan" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header bg-dark-header">
        <h5 class="modal-title fw-bold">Tambah Satuan Baru</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
         <div class="mb-2">
             <label class="form-label fw-medium text-secondary">Nama Satuan <span class="text-danger">*</span></label>
             <input type="text" id="input_satuan_baru" class="form-control" placeholder="Contoh: Pcs, Kg, Box">
             <div id="error_satuan" class="text-danger mt-1 fw-medium" style="display: none; font-size: 0.875em;"></div>
         </div>
      </div>
      <div class="modal-footer border-0 pb-4 pe-4">
        <button type="button" class="btn btn-outline-secondary px-4 fw-medium" data-bs-dismiss="modal">Batal</button>
        <button type="button" class="btn btn-orange px-4 fw-bold" onclick="simpanSatuan()" id="btnSaveSatuan">Simpan</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-custom').select2({
            theme: 'bootstrap-5',
            placeholder: function(){ return $(this).data('placeholder'); },
            templateResult: formatOpsiSelect
        }).on('select2:select', function (e) {
            if (e.params.data.id === 'ADD_NEW') {
                $(this).val('').trigger('change');
                let targetModal = $(this).data('modal');
                let modal = new bootstrap.Modal(document.querySelector(targetModal));
                modal.show();
            }
        });

        function formatOpsiSelect (opsi) {
            if (!opsi.id) { return opsi.text; }
            if (opsi.id === 'ADD_NEW') {
                return $('<span style="color: #f97316; font-weight: bold;"><i class="fa-solid fa-plus me-1"></i> ' + opsi.text + '</span>');
            }
            return opsi.text;
        }
    });

    function previewFile(event) {
        const file = event.target.files[0];
        const previewImg = document.getElementById('imagePreview');
        const previewText = document.getElementById('previewText');
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
                previewText.style.display = 'none';
            }
            reader.readAsDataURL(file);
        }
    }

    async function simpanKategori() {
        const inputNama = document.getElementById('input_kategori_baru').value;
        const inputDeskripsi = document.getElementById('input_deskripsi_kategori').value;
        const errorDiv = document.getElementById('error_kategori');
        const btnSave = document.getElementById('btnSaveKategori');
        errorDiv.style.display = 'none';
        btnSave.disabled = true;
        btnSave.innerText = 'Menyimpan...';
        try {
            const response = await fetch("{{ route('kategori.storeAjax') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ kategori: inputNama, deskripsi: inputDeskripsi })
            });
            const data = await response.json();
            if (response.ok) {
                var newOption = new Option(data.data.kategori, data.data.id, true, true);
                $('#id_kategori').append(newOption).trigger('change');
                bootstrap.Modal.getInstance(document.getElementById('modalKategori')).hide();
                document.getElementById('input_kategori_baru').value = '';
                document.getElementById('input_deskripsi_kategori').value = '';
            } else {
                errorDiv.innerText = data.message || 'Gagal menyimpan data';
                errorDiv.style.display = 'block';
            }
        } catch (error) {
            errorDiv.innerText = 'Terjadi kesalahan pada sistem.';
            errorDiv.style.display = 'block';
        } finally { btnSave.disabled = false; btnSave.innerText = 'Simpan'; }
    }

    async function simpanSatuan() {
        const inputVal = document.getElementById('input_satuan_baru').value;
        const errorDiv = document.getElementById('error_satuan');
        const btnSave = document.getElementById('btnSaveSatuan');
        errorDiv.style.display = 'none';
        btnSave.disabled = true;
        btnSave.innerText = 'Menyimpan...';
        try {
            const response = await fetch("{{ route('satuan.storeAjax') }}", {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ nama_satuan: inputVal })
            });
            const data = await response.json();
            if (response.ok) {
                var newOption = new Option(data.data.nama_satuan, data.data.id, true, true);
                $('#id_satuan').append(newOption).trigger('change');
                bootstrap.Modal.getInstance(document.getElementById('modalSatuan')).hide();
                document.getElementById('input_satuan_baru').value = '';
            } else {
                errorDiv.innerText = data.message || 'Gagal menyimpan data';
                errorDiv.style.display = 'block';
            }
        } catch (error) {
            errorDiv.innerText = 'Terjadi kesalahan pada sistem.';
            errorDiv.style.display = 'block';
        } finally { btnSave.disabled = false; btnSave.innerText = 'Simpan'; }
    }
</script>

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
