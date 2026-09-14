@extends('layouts.app')

@section('content')
<style>
    /* ===== Token dasar (di-scope ke .pc- supaya tidak bentrok sama style global) ===== */
    .pc {
        --pc-ink: #111827;
        --pc-body: #374151;
        --pc-muted: #6b7280;
        --pc-faint: #9ca3af;
        --pc-border: #e5e7eb;
        --pc-surface: #ffffff;
        --pc-surface-alt: #f9fafb;
        --pc-primary: #2563eb;
        --pc-primary-dark: #1d4ed8;
        --pc-primary-tint: #eff6ff;
        --pc-success: #15803d;
        --pc-success-tint: #f0fdf4;
        --pc-warning: #b45309;
        --pc-warning-tint: #fffbeb;
        --pc-danger: #b91c1c;
        --pc-danger-tint: #fef2f2;
        --pc-radius: 10px;
        --pc-radius-sm: 7px;
        max-width: 900px;
        margin: 0 auto;
        color: var(--pc-body);
        font-size: 0.9375rem;
    }
    .pc h4.pc-title {
        font-size: 1.375rem;
        font-weight: 700;
        color: var(--pc-ink);
        margin: 0 0 4px;
        letter-spacing: -0.01em;
    }
    .pc-back {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.8125rem;
        color: var(--pc-muted);
        text-decoration: none;
        margin-bottom: 22px;
    }
    .pc-back:hover { color: var(--pc-primary); }

    /* ===== Stepper ===== */
    .pc-stepper {
        display: flex;
        align-items: center;
        margin-bottom: 28px;
    }
    .pc-step {
        display: flex;
        align-items: center;
        gap: 9px;
        flex-shrink: 0;
    }
    .pc-step-dot {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
        flex-shrink: 0;
        border: 1.5px solid var(--pc-border);
        color: var(--pc-faint);
        background: var(--pc-surface);
    }
    .pc-step-label {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--pc-faint);
        white-space: nowrap;
    }
    .pc-step--current .pc-step-dot {
        border-color: var(--pc-primary);
        color: var(--pc-primary);
        background: var(--pc-primary-tint);
    }
    .pc-step--current .pc-step-label { color: var(--pc-ink); }
    .pc-step--done .pc-step-dot {
        border-color: var(--pc-primary);
        background: var(--pc-primary);
        color: #fff;
    }
    .pc-step--done .pc-step-label { color: var(--pc-body); }
    .pc-step-line {
        flex: 1;
        height: 1.5px;
        background: var(--pc-border);
        margin: 0 12px;
        min-width: 24px;
    }
    .pc-step-line--done { background: var(--pc-primary); }

    /* ===== Item search ===== */
    .pc-search-label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--pc-ink);
        margin-bottom: 7px;
    }
    .item-search-wrapper { position: relative; }
    .item-search-wrapper input[type="text"] {
        padding-left: 40px;
        padding-right: 36px;
        height: 44px;
        font-size: 0.9375rem;
        border-radius: var(--pc-radius);
        border: 1.5px solid var(--pc-border);
    }
    .item-search-wrapper input[type="text"]:focus {
        border-color: var(--pc-primary);
        box-shadow: 0 0 0 3px var(--pc-primary-tint);
    }
    .item-search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--pc-faint);
        pointer-events: none;
        font-size: 0.875rem;
    }
    .item-search-results {
        display: none;
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        right: 0;
        z-index: 50;
        background: var(--pc-surface);
        border: 1px solid var(--pc-border);
        border-radius: var(--pc-radius);
        max-height: 280px;
        overflow-y: auto;
        box-shadow: 0 12px 24px -8px rgba(17, 24, 39, 0.14);
    }
    .item-search-results.show { display: block; }
    .item-search-result {
        padding: 10px 14px;
        cursor: pointer;
        border-bottom: 1px solid var(--pc-surface-alt);
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .item-search-result:last-child { border-bottom: none; }
    .item-search-result:hover,
    .item-search-result.active { background: var(--pc-primary-tint); }
    .item-search-result .item-kode {
        font-family: 'Courier New', monospace;
        font-size: 0.6875rem;
        color: var(--pc-muted);
        background: var(--pc-surface-alt);
        padding: 3px 7px;
        border-radius: 5px;
        flex-shrink: 0;
    }
    .item-search-result .item-nama { font-size: 0.875rem; color: var(--pc-ink); }
    .item-search-result mark { background: #fef08a; padding: 0; font-weight: 600; }
    .item-search-empty { padding: 16px; text-align: center; color: var(--pc-faint); font-size: 0.8125rem; }
    .item-search-clear {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: var(--pc-faint);
        font-size: 18px;
        cursor: pointer;
        display: none;
        line-height: 1;
        padding: 4px;
    }
    .item-search-clear.show { display: block; }

    /* ===== Empty state ===== */
    .pc-empty {
        text-align: center;
        padding: 44px 24px;
        background: var(--pc-surface-alt);
        border: 1px dashed var(--pc-border);
        border-radius: 12px;
        margin-top: 16px;
    }
    .pc-empty-icon { font-size: 34px; margin-bottom: 12px; }
    .pc-empty h5 { font-weight: 700; font-size: 1rem; color: var(--pc-ink); margin-bottom: 6px; }
    .pc-empty p { color: var(--pc-muted); max-width: 420px; margin: 0 auto 26px; font-size: 0.875rem; }
    .pc-flow {
        display: flex;
        justify-content: center;
        gap: 0;
        max-width: 640px;
        margin: 0 auto;
    }
    .pc-flow-step {
        flex: 1;
        max-width: 140px;
        padding: 0 8px;
    }
    .pc-flow-num {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: var(--pc-surface);
        border: 1.5px solid var(--pc-border);
        color: var(--pc-muted);
        font-size: 0.6875rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 8px;
    }
    .pc-flow-title { font-weight: 600; font-size: 0.75rem; color: var(--pc-ink); margin-bottom: 3px; }
    .pc-flow-desc { font-size: 0.6875rem; color: var(--pc-muted); line-height: 1.4; }

    /* ===== Card umum ===== */
    .pc-card {
        background: var(--pc-surface);
        border: 1px solid var(--pc-border);
        border-radius: 12px;
        margin-bottom: 20px;
        overflow: hidden;
    }
    .pc-card-header {
        padding: 14px 20px;
        border-bottom: 1px solid var(--pc-border);
        font-size: 0.875rem;
        color: var(--pc-body);
    }
    .pc-card-header strong { color: var(--pc-ink); }
    .pc-card-header .pc-meta { color: var(--pc-muted); font-size: 0.8125rem; }
    .pc-card-body { padding: 20px; }

    /* ===== Form tanggal ===== */
    .pc-date-row {
        display: flex;
        align-items: flex-end;
        gap: 16px;
        flex-wrap: wrap;
    }
    .pc-field { flex: 1 1 200px; min-width: 180px; }
    .pc-field label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--pc-ink);
        margin-bottom: 6px;
    }
    .pc-field input[type="date"] {
        height: 42px;
        border-radius: var(--pc-radius-sm);
        border: 1.5px solid var(--pc-border);
        font-size: 0.875rem;
    }
    .pc-field input[type="date"]:focus {
        border-color: var(--pc-primary);
        box-shadow: 0 0 0 3px var(--pc-primary-tint);
    }
    .pc-field-hint { font-size: 0.75rem; color: var(--pc-muted); margin-top: 6px; line-height: 1.4; }
    .pc-btn-primary {
        height: 42px;
        padding: 0 20px;
        border-radius: var(--pc-radius-sm);
        background: var(--pc-primary);
        border: none;
        color: #fff;
        font-weight: 600;
        font-size: 0.875rem;
        white-space: nowrap;
    }
    .pc-btn-primary:hover { background: var(--pc-primary-dark); color: #fff; }

    /* ===== Ringkasan tanggal terkunci ===== */
    .pc-locked {
        background: var(--pc-primary-tint);
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        padding: 13px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
        font-size: 0.8438rem;
    }
    .pc-locked strong { color: var(--pc-ink); }
    .pc-badge-backtest {
        display: inline-flex;
        align-items: center;
        background: var(--pc-warning-tint);
        color: var(--pc-warning);
        font-size: 0.6875rem;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 999px;
        margin-left: 8px;
        border: 1px solid #fde68a;
    }
    .pc-link-btn {
        font-size: 0.8125rem;
        font-weight: 600;
        color: var(--pc-primary);
        text-decoration: none;
        white-space: nowrap;
    }
    .pc-link-btn:hover { text-decoration: underline; }

    /* ===== Alert ringkas ===== */
    .pc-note {
        border-radius: 10px;
        padding: 13px 16px;
        font-size: 0.8438rem;
        line-height: 1.5;
        margin-bottom: 20px;
    }
    .pc-note--warning { background: var(--pc-warning-tint); color: #92400e; border: 1px solid #fde68a; }
    .pc-note--danger { background: var(--pc-danger-tint); color: #991b1b; border: 1px solid #fecaca; }

    /* ===== Tabel model ===== */
    .pc-table { width: 100%; border-collapse: collapse; font-size: 0.875rem; }
    .pc-table th {
        text-align: left;
        font-size: 0.75rem;
        text-transform: none;
        font-weight: 600;
        color: var(--pc-muted);
        background: var(--pc-surface-alt);
        padding: 10px 14px;
        border-bottom: 1px solid var(--pc-border);
    }
    .pc-table td {
        padding: 12px 14px;
        border-bottom: 1px solid var(--pc-surface-alt);
        vertical-align: middle;
    }
    .pc-table tr:last-child td { border-bottom: none; }
    .pc-table tr.pc-row-recommended { background: var(--pc-success-tint); }
    .pc-table input[type="radio"] { width: 16px; height: 16px; accent-color: var(--pc-primary); }
    .pc-badge-rec {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--pc-success-tint);
        color: var(--pc-success);
        border: 1px solid #bbf7d0;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 3px 9px;
        border-radius: 999px;
    }
    .pc-table-foot {
        padding: 12px 20px;
        border-top: 1px solid var(--pc-border);
        background: var(--pc-surface-alt);
        color: var(--pc-muted);
        font-size: 0.75rem;
        line-height: 1.5;
    }

    /* ===== Actions bar (simpan) ===== */
    .pc-actions { display: flex; align-items: center; gap: 10px; margin-top: 4px; }
    .pc-btn-save {
        height: 42px;
        padding: 0 22px;
        border-radius: var(--pc-radius-sm);
        background: var(--pc-primary);
        border: none;
        color: #fff;
        font-weight: 600;
        font-size: 0.875rem;
    }
    .pc-btn-save:hover { background: var(--pc-primary-dark); color: #fff; }
    .pc-btn-cancel {
        height: 42px;
        padding: 0 18px;
        border-radius: var(--pc-radius-sm);
        background: var(--pc-surface);
        border: 1.5px solid var(--pc-border);
        color: var(--pc-body);
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }
    .pc-btn-cancel:hover { border-color: var(--pc-faint); color: var(--pc-ink); }

    /* ===== Loading overlay ===== */
    #calc-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, 0.72);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    #calc-overlay .calc-box {
        background: #fff;
        border-radius: 14px;
        padding: 30px 34px;
        width: 340px;
        text-align: center;
        box-shadow: 0 20px 40px -12px rgba(0,0,0,0.3);
    }
    #calc-overlay .spinner {
        width: 42px;
        height: 42px;
        border: 3.5px solid #e5e7eb;
        border-top-color: var(--pc-primary);
        border-radius: 50%;
        margin: 0 auto 16px;
        animation: calc-spin 0.8s linear infinite;
    }
    @keyframes calc-spin { to { transform: rotate(360deg); } }
    #calc-overlay .calc-phase {
        font-weight: 600;
        font-size: 0.9375rem;
        color: var(--pc-ink);
        min-height: 22px;
        transition: opacity 0.2s ease;
    }
    #calc-overlay .calc-subtext { font-size: 0.75rem; color: var(--pc-faint); margin-top: 5px; }
    #calc-overlay .calc-progress {
        margin-top: 15px;
        height: 5px;
        border-radius: 4px;
        background: #e5e7eb;
        overflow: hidden;
    }
    #calc-overlay .calc-progress-bar {
        height: 100%;
        background: var(--pc-primary);
        width: 0%;
        transition: width 0.4s ease;
    }

    @media (max-width: 576px) {
        .pc-stepper { overflow-x: auto; }
        .pc-step-label { display: none; }
        .pc-flow { flex-wrap: wrap; }
        .pc-flow-step { flex: 1 1 45%; max-width: none; margin-bottom: 16px; }
    }
</style>

<div class="container pc">
    <a href="{{ route('prediksi.index') }}" class="pc-back">← Kembali ke Riwayat Prediksi</a>
    <h4 class="pc-title">Buat Prediksi Barang Keluar</h4>

    @if(session('error'))
        <div class="pc-note pc-note--danger" style="margin-top:16px;">{{ session('error') }}</div>
    @endif

    @php
        $datesLocked = !$dateError && $tanggalMulai && $tanggalAkhir
            && request()->query('tanggal_mulai') && request()->query('tanggal_akhir');
        $stepItemState = $kodeBarang ? 'done' : 'current';
        $stepTanggalState = !$kodeBarang ? 'upcoming' : ($datesLocked ? 'done' : 'current');
        $stepFinalState = $datesLocked ? 'current' : 'upcoming';
    @endphp

    {{-- Stepper: alur ini memang berurutan (pilih item -> pilih tanggal -> baru rekomendasi keluar) --}}
    <div class="pc-stepper">
        <div class="pc-step pc-step--{{ $stepItemState }}">
            <span class="pc-step-dot">{{ $kodeBarang ? '✓' : '1' }}</span>
            <span class="pc-step-label">Pilih Item</span>
        </div>
        <div class="pc-step-line {{ $kodeBarang ? 'pc-step-line--done' : '' }}"></div>
        <div class="pc-step pc-step--{{ $stepTanggalState }}">
            <span class="pc-step-dot">{{ $datesLocked ? '✓' : '2' }}</span>
            <span class="pc-step-label">Pilih Tanggal</span>
        </div>
        <div class="pc-step-line {{ $datesLocked ? 'pc-step-line--done' : '' }}"></div>
        <div class="pc-step pc-step--{{ $stepFinalState }}">
            <span class="pc-step-dot">3</span>
            <span class="pc-step-label">Bandingkan &amp; Simpan</span>
        </div>
    </div>

    {{-- STEP 1: Pilih Item (search/autocomplete, bukan dropdown panjang) --}}
    <form method="GET" action="{{ route('prediksi.create') }}" class="mb-4" id="form-pilih-item">
        <label for="item-search" class="pc-search-label">Pilih Item yang Akan Diprediksi</label>
        <div class="item-search-wrapper">
            <span class="item-search-icon">🔍︎</span>
            <input type="text" id="item-search" class="form-control" autocomplete="off"
                   placeholder="Ketik nama atau kode item..."
                   value="{{ $selectedItem ? $selectedItem->kode_barang . ' - ' . $selectedItem->nama_barang : '' }}">
            <button type="button" class="item-search-clear" id="item-search-clear">&times;</button>
            <div class="item-search-results" id="item-search-results"></div>
        </div>
        <input type="hidden" name="kode_barang" id="kode_barang" value="{{ $kodeBarang }}">
    </form>

    @if (!$kodeBarang)
        {{-- EMPTY STATE: belum pilih item, jelaskan alur ke user biar nggak bingung --}}
        <div class="pc-empty">
            <div class="pc-empty-icon">📦</div>
            <h5>Belum ada item yang dipilih</h5>
            <p>Pilih salah satu item di atas untuk mulai membuat prediksi kebutuhan stok.</p>
            <div class="pc-flow">
                <div class="pc-flow-step">
                    <div class="pc-flow-num">1</div>
                    <div class="pc-flow-title">Pilih Item</div>
                    <div class="pc-flow-desc">Sistem ambil histori barang keluar item tersebut</div>
                </div>
                <div class="pc-flow-step">
                    <div class="pc-flow-num">2</div>
                    <div class="pc-flow-title">Pilih Tanggal</div>
                    <div class="pc-flow-desc">Tentukan rentang tanggal yang mau diprediksi</div>
                </div>
                <div class="pc-flow-step">
                    <div class="pc-flow-num">3</div>
                    <div class="pc-flow-title">Uji 3 Model</div>
                    <div class="pc-flow-desc">SES, ARIMA, HWES diuji pakai data sebelum tanggal itu</div>
                </div>
                <div class="pc-flow-step">
                    <div class="pc-flow-num">4</div>
                    <div class="pc-flow-title">Pilih &amp; Simpan</div>
                    <div class="pc-flow-desc">Model terbaik, hasil otomatis masuk history</div>
                </div>
            </div>
        </div>
    @endif

    @if ($kodeBarang && $selectedItem)

        @php
            $dataEndCarbon = $dataEnd ? \Carbon\Carbon::parse($dataEnd) : null;
            $isBacktest = $tanggalMulai && $dataEndCarbon && \Carbon\Carbon::parse($tanggalMulai)->lte($dataEndCarbon);
        @endphp

        {{-- STEP 2: Pilih rentang tanggal. Rekomendasi model BELUM ditampilkan
             sebelum step ini disubmit -- karena data histori yang dipakai buat
             melatih model bergantung ke tanggal mulai yang dipilih di sini. --}}
        <div class="pc-card">
            <div class="pc-card-header">
                Item terpilih: <strong>{{ $selectedItem->nama_barang }}</strong>
                @if ($dataPoints > 0)
                    <span class="pc-meta">
                        &middot; total histori {{ $dataPoints }} hari
                        ({{ \Carbon\Carbon::parse($dataStart)->format('d M Y') }} – {{ \Carbon\Carbon::parse($dataEnd)->format('d M Y') }})
                    </span>
                @endif
            </div>
            <div class="pc-card-body">
                @if ($dataPoints === 0)
                    <div class="pc-note pc-note--warning" style="margin-bottom:0;">
                        Belum ada data histori "barang keluar" untuk item ini sama sekali. Lengkapi data terlebih dahulu sebelum bisa membuat prediksi.
                    </div>
                @else
                    <form method="GET" action="{{ route('prediksi.create') }}" id="form-pilih-tanggal">
                        <input type="hidden" name="kode_barang" value="{{ $kodeBarang }}">
                        <div class="pc-date-row">
                            <div class="pc-field">
                                <label for="tanggal_mulai">Tanggal Mulai Prediksi</label>
                                <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control"
                                       value="{{ $tanggalMulai }}" required>
                            </div>
                            <div class="pc-field">
                                <label for="tanggal_akhir">Tanggal Akhir Prediksi</label>
                                <input type="date" name="tanggal_akhir" id="tanggal_akhir" class="form-control"
                                       value="{{ $tanggalAkhir }}" min="{{ $tanggalMulai }}" required>
                            </div>
                            <button type="submit" class="pc-btn-primary">Lihat Rekomendasi Model</button>
                        </div>
                        <div class="pc-field-hint">
                            Boleh dipilih bebas, termasuk tanggal yang sudah ada datanya (mode backtest) untuk membandingkan prediksi vs data aktual.
                        </div>
                    </form>

                    @if ($dateError)
                        <div class="pc-note pc-note--danger" style="margin-top:16px; margin-bottom:0;">{{ $dateError }}</div>
                    @endif
                @endif
            </div>
        </div>

        @if ($datesLocked)

            {{-- Ringkasan tanggal yang sudah dikunci dari step 2 --}}
            <div class="pc-locked">
                <div>
                    Rentang prediksi: <strong>{{ \Carbon\Carbon::parse($tanggalMulai)->format('d M Y') }}</strong>
                    s.d. <strong>{{ \Carbon\Carbon::parse($tanggalAkhir)->format('d M Y') }}</strong>
                    ({{ $horizonHari }} hari)
                    @if ($isBacktest)
                        <span class="pc-badge-backtest">Mode Backtest</span>
                    @endif
                </div>
                <a href="{{ route('prediksi.create', ['kode_barang' => $kodeBarang]) }}" class="pc-link-btn">Ubah Tanggal</a>
            </div>

            {{-- Sistem mengecek kelayakan data SEBELUM tanggal_mulai --}}
            @if (!$feasible)
                <div class="pc-note pc-note--warning">
                    Data histori "barang keluar" untuk <strong>{{ $selectedItem->nama_barang }}</strong>
                    SEBELUM tanggal mulai yang dipilih baru <strong>{{ $trainingDataPoints }} hari</strong>.
                    Minimal <strong>7 hari</strong> data diperlukan agar prediksi bisa dijalankan.
                    Silakan pilih tanggal mulai yang lebih jauh ke depan, pilih item lain, atau lengkapi data terlebih dahulu.
                </div>
            @else

                @if ($isThin)
                    <div class="pc-note pc-note--warning">
                        ⚠️ Data histori sebelum tanggal mulai baru <strong>{{ $trainingDataPoints }} hari</strong> (di bawah rekomendasi 30 hari).
                        Prediksi tetap bisa dijalankan, tapi akurasinya kurang reliabel — terutama untuk model
                        <strong>HWES</strong> yang idealnya butuh minimal 2 siklus mingguan (14 hari) untuk
                        mengenali pola musiman dengan baik. Semakin banyak data historis, semakin akurat hasilnya.
                    </div>
                @endif

                {{--
                    STEP 3: Sistem menguji model & menampilkan rekomendasi.
                    Model dilatih HANYA pakai data sebelum tanggal_mulai yang dipilih di step 2,
                    supaya kalau tanggalnya overlap dengan data historis (mode backtest), model
                    tidak "mengintip" data yang seharusnya belum diketahui.
                    Di halaman ini rekomendasi HANYA berdasarkan RMSE (metrik seleksi model).
                    MAPE dan MAE sengaja tidak ditampilkan di sini — keduanya baru relevan
                    dan ditampilkan nanti di halaman hasil (show), sebagai indikator interpretasi
                    akurasi setelah prediksi disimpan.
                --}}
                <div class="pc-card">
                    <div class="pc-card-header">
                        Perbandingan Model untuk <strong>{{ $selectedItem->nama_barang }}</strong>
                        <span class="pc-meta">({{ $trainingDataPoints }} hari data historis sebelum {{ \Carbon\Carbon::parse($tanggalMulai)->format('d M Y') }}, split 80/20)</span>
                    </div>
                    <div style="overflow-x:auto;">
                        <table class="pc-table">
                            <thead>
                                <tr>
                                    <th style="width:56px;">Pilih</th>
                                    <th>Model</th>
                                    <th>RMSE</th>
                                    <th>Rekomendasi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($evaluations as $ev)
                                    <tr class="{{ $ev['is_recommended'] ? 'pc-row-recommended' : '' }}">
                                        <td>
                                            <input type="radio" name="model_pick" value="{{ $loop->index }}"
                                                   form="form-simpan-prediksi"
                                                   {{ $ev['is_recommended'] ? 'checked' : '' }} required>
                                        </td>
                                        <td style="font-weight:600; color:var(--pc-ink);">{{ $ev['model'] }}</td>
                                        <td>{{ number_format($ev['rmse'], 3) }}</td>
                                        <td>
                                            @if ($ev['is_recommended'])
                                                <span class="pc-badge-rec">✔ RMSE terkecil</span>
                                            @else
                                                <span style="color:var(--pc-faint);">–</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="pc-table-foot">
                        RMSE dipakai sebagai satu-satunya dasar rekomendasi otomatis di tahap ini, karena
                        tetap stabil dihitung meskipun ada nilai nol pada data histori. Kamu tetap bisa
                        memilih model lain di luar rekomendasi untuk dibandingkan. Metrik akurasi lain
                        (MAPE, MAE) akan muncul setelah prediksi disimpan, di halaman hasil.
                    </div>
                </div>

                {{-- STEP 4: Simpan prediksi final (otomatis masuk history).
                     Tanggal SUDAH dikunci dari step 2 (dikirim lewat hidden input),
                     tidak bisa diubah lagi di sini supaya konsisten dengan data yang
                     dipakai untuk tabel rekomendasi di atas. --}}
                <form id="form-simpan-prediksi" method="POST" action="{{ route('prediksi.store') }}"
                      onsubmit="return confirmSimpanPrediksi();">
                    @csrf
                    <input type="hidden" name="kode_barang" value="{{ $kodeBarang }}">
                    <input type="hidden" name="tanggal_mulai" value="{{ $tanggalMulai }}">
                    <input type="hidden" name="tanggal_akhir" value="{{ $tanggalAkhir }}">

                    @foreach ($evaluations as $i => $ev)
                        <input type="hidden" class="model-name" data-index="{{ $i }}" value="{{ $ev['model'] }}">
                        <input type="hidden" class="model-params" data-index="{{ $i }}" value='@json($ev['params'])'>
                    @endforeach
                    <input type="hidden" name="model" id="input-model">
                    <input type="hidden" name="params" id="input-params">

                    <div class="pc-actions">
                        <button type="submit" class="pc-btn-save" id="btn-submit-prediksi">Simpan Prediksi</button>
                        <a href="{{ route('prediksi.create') }}" class="pc-btn-cancel">Batal</a>
                    </div>
                </form>

            @endif
        @endif
    @endif
</div>

{{-- Overlay proses perhitungan sistem --}}
<div id="calc-overlay">
    <div class="calc-box">
        <div class="spinner"></div>
        <div class="calc-phase" id="calc-phase-text">Memulai proses...</div>
        <div class="calc-subtext" id="calc-subtext">Mohon tunggu sebentar</div>
        <div class="calc-progress"><div class="calc-progress-bar" id="calc-progress-bar"></div></div>
    </div>
</div>

<script>
    // Fase yang ditampilkan saat MEMILIH TANGGAL (GET -> sistem potong data sebelum
    // tanggal_mulai, cek kelayakan, lalu uji 3 model pakai data yang sudah dipotong itu)
    const fasesPilihTanggal = [
        { text: 'Mengambil data histori barang keluar...', sub: 'Menyusun deret waktu harian' },
        { text: 'Memotong data sebelum tanggal mulai...', sub: 'Supaya model tidak mengintip data setelahnya' },
        { text: 'Mengecek kelayakan data...', sub: 'Minimal 7 hari data diperlukan' },
        { text: 'Menguji model SES...', sub: 'Mencari parameter alpha terbaik' },
        { text: 'Menguji model ARIMA...', sub: 'Differencing & estimasi parameter AR' },
        { text: 'Menguji model HWES (Holt-Winters)...', sub: 'Mendeteksi pola musiman mingguan' },
        { text: 'Menghitung RMSE...', sub: 'Menentukan model dengan error terkecil' },
        { text: 'Menyiapkan rekomendasi...', sub: 'Hampir selesai' },
    ];

    // Fase yang ditampilkan saat SIMPAN PREDIKSI (POST -> forecast final + simpan ke history)
    const fasesSimpan = [
        { text: 'Menghitung forecast final...', sub: 'Menggunakan model yang kamu pilih' },
        { text: 'Menghitung interval kepercayaan...', sub: 'Batas atas & batas bawah prediksi' },
        { text: 'Menyimpan hasil evaluasi model...', sub: 'forecast_model_results' },
        { text: 'Menyimpan hasil prediksi...', sub: 'forecast_values' },
        { text: 'Menyelesaikan & mencatat ke history...', sub: 'Hampir selesai' },
    ];

    function runOverlay(fases, totalDurationMs) {
        const overlay = document.getElementById('calc-overlay');
        const phaseText = document.getElementById('calc-phase-text');
        const subtext = document.getElementById('calc-subtext');
        const progressBar = document.getElementById('calc-progress-bar');

        overlay.style.display = 'flex';

        const stepDuration = totalDurationMs / fases.length;
        let i = 0;

        function showStep() {
            if (i >= fases.length) return;
            phaseText.style.opacity = 0;
            setTimeout(() => {
                phaseText.textContent = fases[i].text;
                subtext.textContent = fases[i].sub;
                phaseText.style.opacity = 1;
                progressBar.style.width = Math.round(((i + 1) / fases.length) * 100) + '%';
            }, 150);
            i++;
            if (i < fases.length) setTimeout(showStep, stepDuration);
        }
        showStep();
    }

    // Data item buat search/autocomplete (nama + kode)
    let allItems = [];
    try {
        allItems = @json($items->map(fn($i) => ['kode' => $i->kode_barang, 'nama' => $i->nama_barang])->values());
        console.log('[item-search] Total item dimuat:', allItems.length, allItems.slice(0, 3));
    } catch (err) {
        console.error('[item-search] Gagal parse data item:', err);
    }

    const searchInput = document.getElementById('item-search');
    const resultsBox = document.getElementById('item-search-results');
    const kodeBarangInput = document.getElementById('kode_barang');
    const clearBtn = document.getElementById('item-search-clear');

    if (!searchInput || !resultsBox || !kodeBarangInput || !clearBtn) {
        console.error('[item-search] Ada elemen yang tidak ditemukan:', {
            searchInput: !!searchInput, resultsBox: !!resultsBox,
            kodeBarangInput: !!kodeBarangInput, clearBtn: !!clearBtn
        });
    }

    function escapeHtml(str) {
        return String(str).replace(/[&<>"']/g, (c) => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[c]));
    }

    function highlightMatch(text, query) {
    return escapeHtml(text);
    }

    function selectItem(item) {
        searchInput.value = `${item.kode} - ${item.nama}`;
        kodeBarangInput.value = item.kode;
        resultsBox.classList.remove('show');
        clearBtn.classList.add('show');
        // Item baru dipilih: belum ada perhitungan model, cukup submit ringan
        // (tanggal & rekomendasi baru muncul di step berikutnya).
        document.getElementById('form-pilih-item').submit();
    }

    function renderResults(query) {
        const q = query.trim().toLowerCase();
        console.log('[item-search] renderResults dipanggil, query:', q, 'total allItems:', allItems.length);

        if (!q) {
            resultsBox.classList.remove('show');
            return;
        }

        if (!allItems.length) {
            resultsBox.innerHTML = '<div class="item-search-empty">Data item kosong / gagal dimuat. Cek console (F12).</div>';
            resultsBox.classList.add('show');
            return;
        }

        const matches = allItems.filter(item =>
            (item.nama || '').toLowerCase().includes(q) || String(item.kode).toLowerCase().includes(q)
        ).slice(0, 8);

        console.log('[item-search] matches ditemukan:', matches.length);

        if (matches.length === 0) {
            resultsBox.innerHTML = '<div class="item-search-empty">Tidak ada item yang cocok</div>';
            resultsBox.classList.add('show');
            return;
        }

        resultsBox.innerHTML = matches.map(item => `
            <div class="item-search-result" data-kode="${escapeHtml(item.kode)}">
                <span class="item-kode">${highlightMatch(String(item.kode), q)}</span>
                <span class="item-nama">${highlightMatch(item.nama, q)}</span>
            </div>
        `).join('');

        resultsBox.classList.add('show');

        resultsBox.querySelectorAll('.item-search-result').forEach(el => {
            el.addEventListener('click', () => {
                const kode = el.dataset.kode;
                const item = allItems.find(i => String(i.kode) === String(kode));
                if (item) selectItem(item);
            });
        });
    }

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            console.log('[item-search] input event, value:', this.value);
            clearBtn.classList.toggle('show', this.value.length > 0);
            kodeBarangInput.value = '';
            renderResults(this.value);
        });

        searchInput.addEventListener('focus', function () {
            if (this.value.trim()) renderResults(this.value);
        });

        searchInput.addEventListener('keydown', function (e) {
            const items = resultsBox.querySelectorAll('.item-search-result');
            if (!items.length || !resultsBox.classList.contains('show')) return;

            let activeIndex = Array.from(items).findIndex(el => el.classList.contains('active'));

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (activeIndex >= 0) items[activeIndex].classList.remove('active');
                activeIndex = (activeIndex + 1) % items.length;
                items[activeIndex].classList.add('active');
                items[activeIndex].scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                if (activeIndex >= 0) items[activeIndex].classList.remove('active');
                activeIndex = (activeIndex - 1 + items.length) % items.length;
                items[activeIndex].classList.add('active');
                items[activeIndex].scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (activeIndex >= 0) items[activeIndex].click();
            } else if (e.key === 'Escape') {
                resultsBox.classList.remove('show');
            }
        });
    }

    document.addEventListener('click', function (e) {
        if (resultsBox && !e.target.closest('.item-search-wrapper')) {
            resultsBox.classList.remove('show');
        }
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            searchInput.value = '';
            kodeBarangInput.value = '';
            clearBtn.classList.remove('show');
            resultsBox.classList.remove('show');
            searchInput.focus();
        });
    }

    // Step 2: submit form pilih tanggal -> jalankan overlay (halaman akan reload
    // dengan tanggal_mulai/tanggal_akhir di query string, dan controller yang
    // menghitung rekomendasinya).
    const formPilihTanggal = document.getElementById('form-pilih-tanggal');
    if (formPilihTanggal) {
        const tanggalMulaiEl = document.getElementById('tanggal_mulai');
        const tanggalAkhirEl = document.getElementById('tanggal_akhir');

        // tanggal akhir tidak boleh sebelum tanggal mulai
        if (tanggalMulaiEl && tanggalAkhirEl) {
            tanggalMulaiEl.addEventListener('change', function () {
                tanggalAkhirEl.min = this.value;
                if (tanggalAkhirEl.value && tanggalAkhirEl.value < this.value) {
                    tanggalAkhirEl.value = this.value;
                }
            });
        }

        formPilihTanggal.addEventListener('submit', function () {
            runOverlay(fasesPilihTanggal, 3000);
        });
    }

    function confirmSimpanPrediksi() {
        const picked = document.querySelector('input[name="model_pick"]:checked');
        if (!picked) {
            alert('Silakan pilih salah satu model terlebih dahulu.');
            return false;
        }

        const index = picked.value;
        document.getElementById('input-model').value =
            document.querySelector(`.model-name[data-index="${index}"]`).value;
        document.getElementById('input-params').value =
            document.querySelector(`.model-params[data-index="${index}"]`).value;

        if (!confirm("Simpan prediksi dengan model terpilih? Hasil akan otomatis masuk ke history.")) {
            return false;
        }

        runOverlay(fasesSimpan, 2500);
        return true;
    }
</script>
@endsection