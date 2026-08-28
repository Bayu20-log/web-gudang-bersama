@extends('layouts.app')

@section('content')
<style>
    .empty-state {
        text-align: center;
        padding: 50px 20px;
        background: #f9fafb;
        border: 1px dashed #d1d5db;
        border-radius: 12px;
        margin-top: 10px;
    }
    .empty-state .empty-icon {
        font-size: 48px;
        margin-bottom: 10px;
    }
    .empty-state h5 {
        font-weight: 600;
        margin-bottom: 6px;
    }
    .empty-state p {
        color: #6b7280;
        max-width: 480px;
        margin: 0 auto 25px;
    }
    .flow-steps {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 16px;
        max-width: 720px;
        margin: 0 auto;
    }
    .flow-step {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        padding: 16px 14px;
        width: 150px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
    }
    .flow-step .flow-icon {
        font-size: 26px;
        margin-bottom: 8px;
    }
    .flow-step .flow-title {
        font-weight: 600;
        font-size: 13px;
        margin-bottom: 4px;
    }
    .flow-step .flow-desc {
        font-size: 12px;
        color: #6b7280;
    }
    .flow-arrow {
        align-self: center;
        color: #9ca3af;
        font-size: 20px;
    }

    /* Loading overlay untuk proses perhitungan sistem */
    #calc-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, 0.75);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    #calc-overlay .calc-box {
        background: white;
        border-radius: 14px;
        padding: 32px 36px;
        width: 360px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.25);
    }
    #calc-overlay .spinner {
        width: 46px;
        height: 46px;
        border: 4px solid #e5e7eb;
        border-top-color: #3b82f6;
        border-radius: 50%;
        margin: 0 auto 18px;
        animation: calc-spin 0.8s linear infinite;
    }
    @keyframes calc-spin { to { transform: rotate(360deg); } }
    #calc-overlay .calc-phase {
        font-weight: 600;
        font-size: 15px;
        color: #111827;
        min-height: 22px;
        transition: opacity 0.2s ease;
    }
    #calc-overlay .calc-subtext {
        font-size: 12px;
        color: #9ca3af;
        margin-top: 6px;
    }
    #calc-overlay .calc-progress {
        margin-top: 16px;
        height: 6px;
        border-radius: 4px;
        background: #e5e7eb;
        overflow: hidden;
    }
    #calc-overlay .calc-progress-bar {
        height: 100%;
        background: #3b82f6;
        width: 0%;
        transition: width 0.4s ease;
    }
    /* Search/autocomplete pilih item */
    .item-search-wrapper {
        position: relative;
    }
    .item-search-wrapper input[type="text"] {
        padding-left: 38px;
    }
    .item-search-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        pointer-events: none;
    }
    .item-search-results {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 50;
        background: white;
        border: 1px solid #d1d5db;
        border-top: none;
        border-radius: 0 0 8px 8px;
        max-height: 280px;
        overflow-y: auto;
        box-shadow: 0 8px 16px rgba(0,0,0,0.08);
    }
    .item-search-results.show { display: block; }
    .item-search-result {
        padding: 10px 14px;
        cursor: pointer;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .item-search-result:last-child { border-bottom: none; }
    .item-search-result:hover,
    .item-search-result.active {
        background: #eff6ff;
    }
    .item-search-result .item-kode {
        font-family: 'Courier New', monospace;
        font-size: 12px;
        color: #6b7280;
        background: #f3f4f6;
        padding: 2px 7px;
        border-radius: 5px;
        flex-shrink: 0;
    }
    .item-search-result .item-nama {
        font-size: 14px;
        color: #111827;
    }
    .item-search-result mark {
        background: #fef08a;
        padding: 0;
        font-weight: 600;
    }
    .item-search-empty {
        padding: 14px;
        text-align: center;
        color: #9ca3af;
        font-size: 13px;
    }
    .item-search-clear {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #9ca3af;
        font-size: 18px;
        cursor: pointer;
        display: none;
        line-height: 1;
        padding: 4px;
    }
    .item-search-clear.show { display: block; }
</style>

<div class="container">
    <h4>Buat Prediksi Barang Keluar</h4>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <a href="{{ route('prediksi.index') }}" class="text-decoration-none d-inline-block mb-3">← Kembali ke Riwayat Prediksi</a>

    {{-- STEP 1: Pilih Item (search/autocomplete, bukan dropdown panjang) --}}
    <form method="GET" action="{{ route('prediksi.create') }}" class="mb-4" id="form-pilih-item">
        <div class="mb-3">
            <label for="item-search">Pilih Item yang Akan Diprediksi</label>
            <div class="item-search-wrapper">
                <span class="item-search-icon">🔍︎</span>
                <input type="text" id="item-search" class="form-control" autocomplete="off"
                       placeholder="Ketik nama atau kode item..."
                       value="{{ $selectedItem ? $selectedItem->kode_barang . ' - ' . $selectedItem->nama_barang : '' }}">
                <button type="button" class="item-search-clear" id="item-search-clear">&times;</button>
                <div class="item-search-results" id="item-search-results"></div>
            </div>
            <input type="hidden" name="kode_barang" id="kode_barang" value="{{ $kodeBarang }}">
        </div>
    </form>

    @if (!$kodeBarang)
        {{-- EMPTY STATE: belum pilih item, jelaskan alur ke user biar nggak bingung --}}
        <div class="empty-state">
            <div class="empty-icon">📦</div>
            <h5>Belum ada item yang dipilih</h5>
            <p>Pilih salah satu item di dropdown atas untuk mulai membuat prediksi kebutuhan stok. Begini alur singkatnya:</p>
            <div class="flow-steps">
                <div class="flow-step">
                    <div class="flow-icon">1️</div>
                    <div class="flow-title">Pilih Item</div>
                    <div class="flow-desc">Sistem ambil histori barang keluar item tersebut</div>
                </div>
                <div class="flow-arrow">→</div>
                <div class="flow-step">
                    <div class="flow-icon">2</div>
                    <div class="flow-title">Uji 3 Model</div>
                    <div class="flow-desc">SES, ARIMA, HWES diuji & dibandingkan RMSE-nya</div>
                </div>
                <div class="flow-arrow">→</div>
                <div class="flow-step">
                    <div class="flow-icon">3</div>
                    <div class="flow-title">Pilih & Simpan</div>
                    <div class="flow-desc">Pilih model terbaik, hasil otomatis masuk history</div>
                </div>
            </div>
        </div>
    @endif

    @if ($kodeBarang && $selectedItem)

        {{-- Sistem mengecek kelayakan data --}}
        @if (!$feasible)
            <div class="alert alert-warning">
                Data histori "barang keluar" untuk <strong>{{ $selectedItem->nama_barang }}</strong> baru
                <strong>{{ $dataPoints }} hari</strong>. Minimal <strong>7 hari</strong> data diperlukan
                agar prediksi bisa dijalankan. Silakan pilih item lain atau lengkapi data terlebih dahulu.
            </div>
        @else

            @if ($isThin)
                <div class="alert alert-warning">
                    ⚠️ Data histori baru <strong>{{ $dataPoints }} hari</strong> (di bawah rekomendasi 30 hari).
                    Prediksi tetap bisa dijalankan, tapi akurasinya kurang reliabel — terutama untuk model
                    <strong>HWES</strong> yang idealnya butuh minimal 2 siklus mingguan (14 hari) untuk
                    mengenali pola musiman dengan baik. Semakin banyak data historis, semakin akurat hasilnya.
                </div>
            @endif

            {{-- STEP 2: Sistem menguji model & menampilkan rekomendasi --}}
            <div class="card mb-4">
                <div class="card-header">
                    Perbandingan Model untuk <strong>{{ $selectedItem->nama_barang }}</strong>
                    <span class="text-muted">({{ $dataPoints }} hari data historis, split 80/20)</span>
                </div>
                <div class="card-body">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Pilih</th>
                                <th>Model</th>
                                <th>RMSE</th>
                                <th>MAPE</th>
                                <th>MAE</th>
                                <th>Rekomendasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($evaluations as $ev)
                                <tr class="{{ $ev['is_recommended'] ? 'table-success' : '' }}">
                                    <td>
                                        <input type="radio" name="model_pick" value="{{ $loop->index }}"
                                               form="form-simpan-prediksi"
                                               {{ $ev['is_recommended'] ? 'checked' : '' }} required>
                                    </td>
                                    <td>{{ $ev['model'] }}</td>
                                    <td>{{ number_format($ev['rmse'], 3) }}</td>
                                    <td>{{ number_format($ev['mape'], 2) }}%</td>
                                    <td>{{ number_format($ev['mae'], 3) }}</td>
                                    <td>
                                        @if ($ev['is_recommended'])
                                            <span class="badge bg-success">✔ Direkomendasikan (RMSE terkecil)</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <small class="text-muted">
                        RMSE dipakai sebagai dasar rekomendasi otomatis. MAPE dan MAE ditampilkan sebagai
                        pembanding tambahan. Kamu tetap bisa memilih model lain di luar rekomendasi untuk diuji.
                    </small>
                </div>
            </div>

            {{-- STEP 3: Simpan prediksi final (otomatis masuk history) --}}
            <form id="form-simpan-prediksi" method="POST" action="{{ route('prediksi.store') }}"
                  onsubmit="return confirmSimpanPrediksi();">
                @csrf
                <input type="hidden" name="kode_barang" value="{{ $kodeBarang }}">

                @foreach ($evaluations as $i => $ev)
                    <input type="hidden" class="model-name" data-index="{{ $i }}" value="{{ $ev['model'] }}">
                    <input type="hidden" class="model-params" data-index="{{ $i }}" value='@json($ev['params'])'>
                @endforeach
                <input type="hidden" name="model" id="input-model">
                <input type="hidden" name="params" id="input-params">
                <input type="hidden" name="horizon" id="input-horizon">

                @php
                    $tanggalMulaiDefault = \Carbon\Carbon::parse($dataEnd)->addDay();
                    $tanggalAkhirDefault = $tanggalMulaiDefault->copy()->addDays(6); // default 7 hari prediksi
                @endphp

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="tanggal_mulai">Tanggal Mulai Prediksi</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control"
                               value="{{ $tanggalMulaiDefault->format('Y-m-d') }}"
                               data-start="{{ \Carbon\Carbon::parse($dataStart)->format('Y-m-d') }}"
                               data-min-days="{{ $minDataPoints }}"
                               required>
                        <small class="text-muted" id="tanggal-mulai-info">Bebas dipilih. Data histori terakhir: {{ \Carbon\Carbon::parse($dataEnd)->format('d M Y') }}</small>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="tanggal_akhir">Tanggal Akhir Prediksi</label>
                        <input type="date" id="tanggal_akhir" class="form-control"
                               value="{{ $tanggalAkhirDefault->format('Y-m-d') }}"
                               required>
                        <small class="text-muted" id="horizon-info">= 7 hari prediksi</small>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" id="btn-submit-prediksi">Simpan Prediksi</button>
                <a href="{{ route('prediksi.create') }}" class="btn btn-secondary">Batal</a>
            </form>

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
    // Fase yang ditampilkan saat MEMILIH ITEM (GET -> sistem cek kelayakan + uji 3 model)
    const fasesPilihItem = [
        { text: 'Mengambil data histori barang keluar...', sub: 'Menyusun deret waktu harian' },
        { text: 'Mengecek kelayakan data...', sub: 'Minimal 14 hari data diperlukan' },
        { text: 'Menguji model SES...', sub: 'Mencari parameter alpha terbaik' },
        { text: 'Menguji model ARIMA...', sub: 'Differencing & estimasi parameter AR' },
        { text: 'Menguji model HWES (Holt-Winters)...', sub: 'Mendeteksi pola musiman mingguan' },
        { text: 'Menghitung RMSE, MAE, MAPE...', sub: 'Menentukan model dengan error terkecil' },
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
        runOverlay(fasesPilihItem, 3000);
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

    // Hitung horizon (jumlah hari) otomatis dari selisih tanggal mulai - tanggal akhir yang dipilih user.
    // Juga validasi: data historis SEBELUM tanggal mulai yang dipilih minimal harus data-min-days hari.
    function updateHorizonFromDate() {
        const tanggalMulaiInput = document.getElementById('tanggal_mulai');
        const tanggalAkhirInput = document.getElementById('tanggal_akhir');
        if (!tanggalMulaiInput || !tanggalAkhirInput) return;

        // tanggal akhir tidak boleh sebelum tanggal mulai
        tanggalAkhirInput.min = tanggalMulaiInput.value;

        const horizonInfo = document.getElementById('horizon-info');
        const inputHorizon = document.getElementById('input-horizon');
        const tanggalMulaiInfo = document.getElementById('tanggal-mulai-info');
        const btnSubmit = document.getElementById('btn-submit-prediksi');

        // Validasi data historis sebelum tanggal mulai (data-start & data-min-days dari server)
        const dataStart = new Date(tanggalMulaiInput.dataset.start + 'T00:00:00');
        const minDays = parseInt(tanggalMulaiInput.dataset.minDays, 10) || 7;
        const mulai = new Date(tanggalMulaiInput.value + 'T00:00:00');
        const daysBefore = Math.round((mulai - dataStart) / (1000 * 60 * 60 * 24));

        if (daysBefore < minDays) {
            tanggalMulaiInfo.textContent = `⚠️ Cuma ada ${Math.max(daysBefore, 0)} hari data historis sebelum tanggal ini. Minimal ${minDays} hari dibutuhkan — pilih tanggal mulai yang lebih jauh ke depan.`;
            tanggalMulaiInfo.classList.add('text-danger');
            if (btnSubmit) btnSubmit.disabled = true;
        } else {
            tanggalMulaiInfo.classList.remove('text-danger');
            tanggalMulaiInfo.textContent = `Data historis terakhir: {{ \Carbon\Carbon::parse($dataEnd)->format('d M Y') }} (${daysBefore} hari data tersedia sebelum tanggal ini)`;
            if (btnSubmit) btnSubmit.disabled = false;
        }

        const mulaiForHorizon = new Date(tanggalMulaiInput.value + 'T00:00:00');
        const akhir = new Date(tanggalAkhirInput.value + 'T00:00:00');
        const selisihHari = Math.round((akhir - mulaiForHorizon) / (1000 * 60 * 60 * 24)) + 1;

        if (isNaN(selisihHari) || selisihHari < 1) {
            horizonInfo.textContent = 'Tanggal akhir harus setelah atau sama dengan tanggal mulai';
            horizonInfo.classList.add('text-danger');
            inputHorizon.value = '';
            return;
        }

        if (selisihHari > 90) {
            horizonInfo.textContent = `= ${selisihHari} hari (maksimal 90 hari, kurangi rentang tanggal)`;
            horizonInfo.classList.add('text-danger');
            inputHorizon.value = '';
            return;
        }

        horizonInfo.classList.remove('text-danger');
        horizonInfo.textContent = `= ${selisihHari} hari prediksi`;
        inputHorizon.value = selisihHari;
    }

    const tanggalMulaiEl = document.getElementById('tanggal_mulai');
    const tanggalAkhirEl = document.getElementById('tanggal_akhir');
    if (tanggalMulaiEl && tanggalAkhirEl) {
        tanggalMulaiEl.addEventListener('change', updateHorizonFromDate);
        tanggalAkhirEl.addEventListener('change', updateHorizonFromDate);
        updateHorizonFromDate(); // hitung sekali di awal (untuk nilai default)
    }

    function confirmSimpanPrediksi() {
        const picked = document.querySelector('input[name="model_pick"]:checked');
        if (!picked) {
            alert('Silakan pilih salah satu model terlebih dahulu.');
            return false;
        }

        const btnSubmit = document.getElementById('btn-submit-prediksi');
        if (btnSubmit && btnSubmit.disabled) {
            alert('Tanggal mulai prediksi belum valid (data historis sebelumnya kurang). Perbaiki dulu sebelum menyimpan.');
            return false;
        }

        const inputHorizon = document.getElementById('input-horizon');
        if (!inputHorizon.value || parseInt(inputHorizon.value) < 1) {
            alert('Rentang tanggal prediksi belum valid. Pastikan tanggal akhir tidak sebelum tanggal mulai, dan rentangnya maksimal 90 hari.');
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