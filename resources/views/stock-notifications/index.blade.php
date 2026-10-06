@extends('layouts.app')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<style>
    .sn-wrap { font-family: 'Inter', sans-serif; color: #000; }

    .sn-header {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 20px;
        padding: 1.5rem 1.75rem;
        color: #fff;
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .sn-header-title { font-weight: 700; font-size: 1.4rem; margin-bottom: 0.2rem; }
    .sn-header-subtitle { color: #94a3b8; font-size: 0.86rem; }

    .sn-btn-primary {
        background: #f97316;
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 0.55rem 1.25rem;
        font-size: 0.88rem;
        font-weight: 600;
        white-space: nowrap;
        box-shadow: 0 6px 16px rgba(249,115,22,0.3);
    }

    .sn-btn-primary:hover { background: #ea6a0c; color: #fff; }

    .sn-legend {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1.25rem;
    }

    .sn-legend-pills { display: flex; gap: 0.5rem; flex-wrap: wrap; }

    .sn-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.76rem;
        font-weight: 700;
        padding: 0.3rem 0.75rem;
        border-radius: 999px;
    }

    .sn-hint { font-size: 0.8rem; color: #64748b; }

    .sn-item-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 3px 12px rgba(15,23,42,0.05);
        padding: 1.1rem 1.4rem;
        margin-bottom: 0.85rem;
        transition: box-shadow 0.15s ease;
    }

    .sn-item-card:hover { box-shadow: 0 8px 20px rgba(15,23,42,0.08); }

    .sn-item-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 0.75rem;
    }

    .sn-item-name { font-weight: 700; font-size: 1rem; color: #0f172a; margin-bottom: 0.3rem; }

    .sn-chips { display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 0.5rem; }

    .sn-chip {
        background: #f8fafc;
        border-radius: 10px;
        padding: 0.4rem 0.75rem;
        font-size: 0.78rem;
        color: #475569;
    }

    .sn-chip strong { color: #0f172a; }

    .sn-item-actions { display: flex; align-items: center; gap: 0.6rem; flex-shrink: 0; }

    .sn-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .sn-summary-card {
        background: #fff;
        border-radius: 16px;
        padding: 1rem 1.2rem;
        box-shadow: 0 3px 12px rgba(15,23,42,0.05);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: transform 0.15s ease;
    }

    .sn-summary-card:hover { transform: translateY(-2px); }

    .sn-summary-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .sn-summary-count { font-size: 1.4rem; font-weight: 700; color: #0f172a; line-height: 1; }
    .sn-summary-label { font-size: 0.76rem; color: #64748b; font-weight: 600; }

    .sn-status-hint { font-size: 0.8rem; color: #64748b; margin-top: 0.5rem; }

    .sn-filter-pill {
        border: none;
        cursor: pointer;
        transition: all 0.15s ease;
        outline: none;
    }

    .sn-filter-pill:focus {
        outline: none;
    }

    .sn-filter-pill:hover { transform: translateY(-1px); filter: brightness(0.95); }

    .sn-filter-pill.active {
        box-shadow: 0 0 0 2px currentColor inset;
        font-weight: 700;
    }

    .sn-btn-ghost .sn-chevron {
        transition: transform 0.2s ease;
        display: inline-block;
    }

    .sn-btn-ghost[aria-expanded="true"] .sn-chevron {
        transform: rotate(180deg);
    }

    .sn-btn-ghost {
        background: #f1f5f9;
        color: #334155;
        border: none;
        border-radius: 10px;
        padding: 0.45rem 1rem;
        font-size: 0.8rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        cursor: pointer;
        transition: background 0.15s ease, transform 0.15s ease;
    }

    .sn-btn-ghost:hover { background: #e2e8f0; color: #0f172a; transform: translateY(-1px); }
    .sn-btn-ghost:active { transform: translateY(0); }

    .sn-btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        transition: background 0.15s ease, transform 0.15s ease;
    }

    .sn-btn-primary:active { transform: translateY(0) scale(0.98); }

    .sn-breakdown {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px dashed #e2e8f0;
        font-size: 0.86rem;
        color: #334155;
    }

    .sn-breakdown-step { margin-bottom: 0.9rem; }
    .sn-breakdown-step:last-child { margin-bottom: 0; }

    .sn-breakdown-title { font-weight: 700; color: #0f172a; margin-bottom: 0.35rem; }

    .sn-formula-calc {
        font-family: 'Courier New', monospace;
        font-size: 0.86rem;
        color: #f97316;
        font-weight: 700;
        background: #fff7ed;
        padding: 0.4rem 0.75rem;
        border-radius: 8px;
        display: inline-block;
        margin-top: 0.3rem;
    }

    .sn-adc-zero-note {
        margin-top: 0.75rem;
        background: #fef3c7;
        color: #92400e;
        border-radius: 10px;
        padding: 0.6rem 0.9rem;
        font-size: 0.82rem;
    }

    .sn-section-title {
        font-weight: 700;
        font-size: 1.05rem;
        color: #0f172a;
        margin-bottom: 1rem;
    }

    .sn-history-box {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 3px 12px rgba(15,23,42,0.05);
        padding: 1.25rem 1.5rem;
    }

    .sn-history-item {
        border-radius: 14px;
        background: #f8fafc;
        padding: 0.85rem 1rem;
        margin-bottom: 0.6rem;
        display: flex;
        justify-content: space-between;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .sn-history-item:last-child { margin-bottom: 0; }
    .sn-history-title { font-weight: 700; color: #0f172a; font-size: 0.9rem; }
    .sn-history-message { font-size: 0.82rem; color: #64748b; margin-top: 0.15rem; }
    .sn-history-time { font-size: 0.76rem; color: #94a3b8; white-space: nowrap; }

    .sn-empty {
        text-align: center;
        padding: 2.5rem 1rem;
        color: #94a3b8;
    }
</style>

<div class="sn-wrap">
<div class="container py-4">

    <div class="sn-header">
        <div>
            <div class="sn-header-title">Monitoring &amp; Notifikasi Stok</div>
            <div class="sn-header-subtitle">Evaluasi kondisi stok seluruh barang berdasarkan batas minimum otomatis</div>
        </div>
        <form method="POST" action="{{ route('stock-notifications.evaluate') }}">
            @csrf
            <button type="submit" class="sn-btn-primary"><i class="fa-solid fa-arrows-rotate"></i> Evaluasi Sekarang</button>
        </form>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @php
        // Skema warna mengikuti standar manajemen risiko:
        // Aman = biru, Rendah = hijau, Kritis = kuning, Habis = merah
        $statusColors = [
            'aman'   => ['bg' => '#dbeafe', 'text' => '#1d4ed8', 'label' => 'Aman', 'icon' => 'fa-circle-check'],
            'rendah' => ['bg' => '#dcfce7', 'text' => '#15803d', 'label' => 'Rendah', 'icon' => 'fa-circle-info'],
            'kritis' => ['bg' => '#fef3c7', 'text' => '#92400e', 'label' => 'Kritis', 'icon' => 'fa-triangle-exclamation'],
            'habis'  => ['bg' => '#fee2e2', 'text' => '#b91c1c', 'label' => 'Habis', 'icon' => 'fa-box-open'],
        ];
        $statusHint = [
            'aman'   => 'Stok mencukupi, belum perlu tindakan.',
            'rendah' => 'Mulai mendekati batas, pantau terus.',
            'kritis' => 'Segera pesan ulang, stok hampir menipis.',
            'habis'  => 'Stok kosong, restock sekarang juga.',
        ];
        $statusCounts = $itemStatuses->countBy('status');
    @endphp

    {{-- Ringkasan jumlah item per status --}}
    <div class="sn-summary">
        @foreach ($statusColors as $key => $c)
            <div class="sn-summary-card">
                <div class="sn-summary-icon" style="background:{{ $c['bg'] }}; color:{{ $c['text'] }};">
                    <i class="fa-solid {{ $c['icon'] }}"></i>
                </div>
                <div>
                    <div class="sn-summary-count">{{ $statusCounts[$key] ?? 0 }}</div>
                    <div class="sn-summary-label">{{ $c['label'] }}</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="sn-legend">
        <div class="sn-legend-pills" id="sn-filter-bar">
            <button type="button" class="sn-pill sn-filter-pill active" data-filter="semua" style="background:#e2e8f0; color:#0f172a;">Semua</button>
            @foreach ($statusColors as $key => $c)
                <button type="button" class="sn-pill sn-filter-pill" data-filter="{{ $key }}" style="background:{{ $c['bg'] }}; color:{{ $c['text'] }};">{{ $c['label'] }}</button>
            @endforeach
        </div>
        <div class="sn-hint">💡 Klik "Lihat Perhitungan" untuk melihat rincian rumus Rata-rata Keluar per Hari dan Batas Minimum</div>
    </div>

    <div id="sn-item-list">
    @forelse ($itemStatuses as $item)
        @php $sc = $statusColors[$item->status] ?? ['bg' => '#f1f5f9', 'text' => '#475569', 'label' => ucfirst($item->status)]; @endphp
        <div class="sn-item-card" data-status="{{ $item->status }}">
            <div class="sn-item-top">
                <div>
                    <div class="sn-item-name">{{ $item->nama_barang }}</div>
                    <span class="sn-pill" style="background:{{ $sc['bg'] }}; color:{{ $sc['text'] }};">{{ $sc['label'] }}</span>
                    <div class="sn-status-hint">{{ $statusHint[$item->status] ?? '' }}</div>
                    <div class="sn-chips">
                        <span class="sn-chip">Stok: <strong>{{ $item->stok }}</strong></span>
                        <span class="sn-chip">Rata-rata Keluar per Hari: <strong>{{ $item->adc !== null ? ceil($item->adc) : '-' }}</strong></span>
                        <span class="sn-chip">Batas Minimum Rendah: <strong>{{ $item->low_threshold !== null ? ceil($item->low_threshold) : '-' }}</strong></span>
                        <span class="sn-chip">Batas Minimum Kritis: <strong>{{ $item->critical_threshold !== null ? ceil($item->critical_threshold) : '-' }}</strong></span>
                    </div>
                </div>
                <div class="sn-item-actions">
                    <a href="{{ route('item.edit', $item->kode_barang) }}?from=stock-notifications#konfigurasi-threshold"
                       class="sn-btn-ghost" style="text-decoration:none;">
                        <i class="fa-solid fa-sliders"></i> Edit Threshold
                    </a>
                    <button class="sn-btn-ghost" type="button" data-bs-toggle="collapse"
                            data-bs-target="#detail-{{ $item->kode_barang }}" aria-expanded="false">
                        Lihat Perhitungan <i class="fa-solid fa-chevron-down sn-chevron"></i>
                    </button>
                </div>
            </div>

            <div class="collapse" id="detail-{{ $item->kode_barang }}">
                @php $b = $item->breakdown; @endphp
                <div class="sn-breakdown">
                    <div class="sn-breakdown-step">
                        <div class="sn-breakdown-title">1. Menghitung Rata-rata Keluar per Hari</div>
                        <div>Total barang keluar 30 hari terakhir: <strong>{{ $b['total_outflow'] }} unit</strong></div>
                        <div>Dibagi jumlah hari valid (maks. 30 hari): <strong>{{ $b['valid_days'] }} hari</strong></div>
                        <div class="sn-formula-calc">
                            Rata-rata Keluar per Hari = {{ $b['total_outflow'] }} ÷ {{ $b['valid_days'] }} = {{ $b['adc'] }} unit/hari
                        </div>
                    </div>

                    <div class="sn-breakdown-step">
                        <div class="sn-breakdown-title">2. Menghitung Batas Minimum Rendah</div>
                        <div>Safety Stock = Rata-rata Keluar per Hari × Hari Buffer = {{ $b['adc'] }} × {{ $b['safety_stock_days'] }} = <strong>{{ $b['safety_stock'] }}</strong></div>
                        <div class="sn-formula-calc">
                            Batas Minimum Rendah = ({{ $b['adc'] }} × {{ $b['lead_time_days'] }}) + {{ $b['safety_stock'] }} = {{ $b['low_threshold'] }}
                        </div>
                    </div>

                    <div class="sn-breakdown-step">
                        <div class="sn-breakdown-title">3. Menghitung Batas Minimum Kritis</div>
                        <div class="sn-formula-calc">
                            Batas Minimum Kritis = {{ $b['adc'] }} × {{ $b['response_time_days'] }} = {{ $b['critical_threshold'] }}
                        </div>
                    </div>

                    @if ((float) $b['adc'] === 0.0)
                        <div class="sn-adc-zero-note">
                            Rata-rata Keluar per Hari masih 0 karena barang ini belum pernah ada transaksi keluar. Sistem otomatis menganggap status minimal <strong>Rendah</strong> (bukan Aman) untuk kondisi ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="sn-item-card sn-empty">Belum ada data barang.</div>
    @endforelse
    </div>

    <div class="sn-history-box mt-4">
        <div class="sn-section-title">📋 Riwayat Notifikasi Terbaru</div>
        @php
            $historyBorder = [
                'info'     => '#0dcaf0',
                'warning'  => '#f59e0b',
                'critical' => '#dc3545',
            ];
        @endphp
        @forelse ($notifications as $notif)
            @php
                $displayTitle = str_replace('item #' . $notif->item_id, $notif->nama_barang, $notif->title);
                $displayMessage = str_replace('item #' . $notif->item_id, $notif->nama_barang, $notif->message);
                $borderColor = $historyBorder[$notif->level] ?? '#94a3b8';
            @endphp
            <div class="sn-history-item" style="border-left: 4px solid {{ $borderColor }};">
                <div>
                    <div class="sn-history-title">{{ $displayTitle }}</div>
                    <div class="sn-history-message">{{ $displayMessage }}</div>
                </div>
                <div class="sn-history-time">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</div>
            </div>
        @empty
            <p class="text-muted mb-0">Belum ada notifikasi.</p>
        @endforelse
    </div>

</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterButtons = document.querySelectorAll('.sn-filter-pill');
        const itemCards = document.querySelectorAll('#sn-item-list > .sn-item-card');

        filterButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                filterButtons.forEach(function (b) { b.classList.remove('active'); });
                btn.classList.add('active');

                const filter = btn.getAttribute('data-filter');

                itemCards.forEach(function (card) {
                    if (filter === 'semua' || card.getAttribute('data-status') === filter) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    });
</script>
@endsection