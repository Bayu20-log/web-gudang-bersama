@extends('layouts.app')
@section('content')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --raksakti-navy: #1e293b;
        --raksakti-orange: #f97316;
        --raksakti-bg: #f8fafc;
    }

    @keyframes notifFadeIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .notif-page-wrapper {
        background: var(--raksakti-bg);
        font-family: 'Inter', sans-serif;
        color: #000;
        margin: -1.5rem -0.75rem;
        padding: 1.5rem 0.75rem;
    }

    .notif-page-header {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 24px;
        padding: 1.75rem 2rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
        animation: notifFadeIn 0.4s ease-out;
    }

    .notif-page-header::after {
        content: '';
        position: absolute;
        top: -60px;
        right: -60px;
        width: 180px;
        height: 180px;
        background: radial-gradient(circle, rgba(249,115,22,0.35) 0%, rgba(249,115,22,0) 70%);
        border-radius: 50%;
    }

    .notif-page-title {
        font-weight: 700;
        font-size: 1.5rem;
        color: #fff;
        margin-bottom: 0.2rem;
        position: relative;
    }

    .notif-page-subtitle {
        color: #94a3b8;
        font-size: 0.88rem;
        position: relative;
    }

    .notif-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .notif-summary-card {
        background: #fff;
        border-radius: 20px;
        padding: 1.25rem 1.4rem;
        box-shadow: 0 4px 16px rgba(15, 23, 42, 0.06);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 0.75rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        animation: notifFadeIn 0.4s ease-out;
    }

    .notif-summary-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.1);
    }

    .notif-summary-card .count {
        font-size: 2rem;
        font-weight: 700;
        color: #0f172a;
        line-height: 1;
        letter-spacing: -0.02em;
    }

    .notif-summary-card .label {
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 0.2rem;
        font-weight: 600;
    }

    .notif-summary-card .icon-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        background: var(--accent-soft, #eee);
        color: var(--accent, #ccc);
        flex-shrink: 0;
    }

    .notif-filter-bar {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }

    .notif-filter-pill {
        border: none;
        background: #fff;
        color: #475569;
        border-radius: 999px;
        padding: 0.5rem 1.15rem;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        text-decoration: none;
        display: inline-block;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.05);
    }

    .notif-filter-pill:hover {
        background: #fff1e6;
        color: var(--raksakti-orange);
        text-decoration: none;
        transform: translateY(-1px);
    }

    .notif-filter-pill.active {
        background: var(--raksakti-orange);
        color: #fff;
        box-shadow: 0 6px 16px rgba(249, 115, 22, 0.35);
    }

    .notif-card-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .notif-item {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 3px 12px rgba(15, 23, 42, 0.05);
        padding: 1.1rem 1.4rem;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        flex-wrap: wrap;
        gap: 0.75rem;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
        border: 1px solid transparent;
        animation: notifFadeIn 0.35s ease-out;
    }

    .notif-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 24px rgba(15, 23, 42, 0.09);
        border-color: var(--accent, #ccc);
    }

    .notif-item.is-read {
        opacity: 0.55;
    }

    .notif-item-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        padding: 0.25rem 0.7rem;
        border-radius: 999px;
        color: var(--accent-text, #333);
        background: var(--accent-soft, #eee);
        margin-bottom: 0.5rem;
    }

    .notif-item-badge::before {
        content: '●';
        font-size: 0.55rem;
        color: var(--accent, #ccc);
    }

    .notif-item-title {
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.2rem;
        font-size: 0.98rem;
    }

    .notif-item-message {
        font-size: 0.87rem;
        color: #475569;
        margin-bottom: 0.3rem;
        line-height: 1.4;
    }

    .notif-item-time {
        font-size: 0.76rem;
        color: #94a3b8;
        font-weight: 500;
    }

    .notif-item-actions {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
        align-items: flex-end;
    }

    .notif-item-actions form {
        margin: 0;
    }

    .notif-btn-primary {
        background: var(--raksakti-orange);
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 0.4rem 1rem;
        font-size: 0.8rem;
        font-weight: 600;
        white-space: nowrap;
        box-shadow: 0 4px 12px rgba(249, 115, 22, 0.3);
        transition: transform 0.15s ease;
    }

    .notif-btn-primary:hover {
        background: #ea6a0c;
        color: #fff;
        transform: translateY(-1px);
    }

    .notif-btn-ghost {
        background: #f1f5f9;
        color: #334155;
        border: none;
        border-radius: 10px;
        padding: 0.35rem 0.85rem;
        font-size: 0.76rem;
        font-weight: 600;
        transition: all 0.15s ease;
    }

    .notif-btn-ghost:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .notif-empty {
        text-align: center;
        padding: 4rem 1rem;
        color: #64748b;
        background: #fff;
        border-radius: 20px;
        animation: notifFadeIn 0.4s ease-out;
    }

    .notif-empty .icon-circle {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        background: #f1f5f9;
        color: #cbd5e1;
        font-size: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
    }

    .notif-empty .empty-title {
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 0.25rem;
    }
</style>

<div class="notif-page-wrapper">
<div class="container py-4">

    <div class="notif-page-header">
        <div>
            <h4 class="notif-page-title mb-0">Notifikasi Stok</h4>
            <div class="notif-page-subtitle">Pantau status stok yang perlu perhatian</div>
        </div>
        @if($stockNotifications->isNotEmpty())
            <form method="POST" action="{{ route('stock-notifications.markAllRead') }}">
                @csrf
                <button type="submit" class="notif-btn-primary">Tandai Semua Dibaca</button>
            </form>
        @endif
    </div>

    {{-- Ringkasan jumlah per status --}}
    <div class="notif-summary">
        <div class="notif-summary-card" style="--accent:#0dcaf0; --accent-soft:#e0f7fa; --accent-text:#0e7490;">
            <div>
                <div class="count">{{ $levelCounts['info'] ?? 0 }}</div>
                <div class="label">Rendah</div>
            </div>
            <div class="icon-circle"><i class="fa-solid fa-circle-info"></i></div>
        </div>
        <div class="notif-summary-card" style="--accent:#f5b301; --accent-soft:#fef3c7; --accent-text:#92400e;">
            <div>
                <div class="count">{{ $levelCounts['warning'] ?? 0 }}</div>
                <div class="label">Kritis</div>
            </div>
            <div class="icon-circle"><i class="fa-solid fa-triangle-exclamation"></i></div>
        </div>
        <div class="notif-summary-card" style="--accent:#dc3545; --accent-soft:#fee2e2; --accent-text:#b91c1c;">
            <div>
                <div class="count">{{ $levelCounts['critical'] ?? 0 }}</div>
                <div class="label">Habis</div>
            </div>
            <div class="icon-circle"><i class="fa-solid fa-box-open"></i></div>
        </div>
    </div>

    {{-- Filter pill --}}
    <div class="notif-filter-bar">
        <a href="{{ route('notifications.index') }}" class="notif-filter-pill {{ !$statusFilter ? 'active' : '' }}">Semua</a>
        <a href="{{ route('notifications.index', ['status' => 'info']) }}" class="notif-filter-pill {{ $statusFilter === 'info' ? 'active' : '' }}">Rendah</a>
        <a href="{{ route('notifications.index', ['status' => 'warning']) }}" class="notif-filter-pill {{ $statusFilter === 'warning' ? 'active' : '' }}">Kritis</a>
        <a href="{{ route('notifications.index', ['status' => 'critical']) }}" class="notif-filter-pill {{ $statusFilter === 'critical' ? 'active' : '' }}">Habis</a>
    </div>

    {{-- Daftar notifikasi --}}
    @if($stockNotifications->isEmpty())
        <div class="notif-empty">
            <div class="icon-circle"><i class="fa-solid fa-champagne-glasses"></i></div>
            <div class="empty-title">Semua stok aman terkendali!</div>
            <div>Tidak ada notifikasi untuk filter ini.</div>
        </div>
    @else
        <div class="notif-card-list">
            @foreach($stockNotifications as $notif)
                @php
                    $colors = match($notif->level) {
                        'critical' => ['accent' => '#dc3545', 'soft' => '#fee2e2', 'text' => '#b91c1c'],
                        'warning'  => ['accent' => '#f5b301', 'soft' => '#fef3c7', 'text' => '#92400e'],
                        default    => ['accent' => '#0dcaf0', 'soft' => '#e0f7fa', 'text' => '#0e7490'],
                    };
                    $levelLabel = match($notif->level) {
                        'critical' => 'Habis',
                        'warning'  => 'Kritis',
                        default    => 'Rendah',
                    };

                    // Notifikasi lama disimpan dengan teks "item #{kode}" -- ganti
                    // jadi nama barang asli untuk tampilan, tanpa mengubah data
                    // tersimpan (yang tetap dipakai apa adanya oleh logic lain).
                    $needle = 'item #' . $notif->item_id;
                    $displayTitle = str_replace($needle, $notif->nama_barang, $notif->title);
                    $displayMessage = str_replace($needle, $notif->nama_barang, $notif->message);
                @endphp
                <div class="notif-item {{ $notif->is_read ? 'is-read' : '' }}" style="--accent: {{ $colors['accent'] }}; --accent-soft: {{ $colors['soft'] }}; --accent-text: {{ $colors['text'] }};">
                    <div>
                        <span class="notif-item-badge">{{ $levelLabel }}</span>
                        <div class="notif-item-title">{{ $displayTitle }}</div>
                        <div class="notif-item-message">{{ $displayMessage }}</div>
                        <div class="notif-item-time">{{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}</div>
                    </div>
                    <div class="notif-item-actions">
                        @if(in_array($notif->level, ['warning', 'critical']))
                            <a href="{{ route('barang-masuk.create', ['kode_barang' => $notif->item_id]) }}" class="notif-btn-primary text-decoration-none">
                                Tambah Stok
                            </a>
                        @else
                            <a href="{{ route('item.show', $notif->item_id) }}" class="notif-btn-ghost text-decoration-none">
                                Lihat Detail
                            </a>
                        @endif

                        @if(!$notif->is_read)
                            <form method="POST" action="{{ route('stock-notifications.markRead', $notif->id) }}">
                                @csrf
                                <button type="submit" class="notif-btn-ghost">Tandai Dibaca</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('stock-notifications.markUnread', $notif->id) }}">
                                @csrf
                                <button type="submit" class="notif-btn-ghost">Tandai Belum Dibaca</button>
                            </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const savedScroll = sessionStorage.getItem('notif_scroll_pos');
        if (savedScroll !== null) {
            window.scrollTo(0, parseInt(savedScroll, 10));
            sessionStorage.removeItem('notif_scroll_pos');
        }

        document.querySelectorAll('form').forEach(function (form) {
            form.addEventListener('submit', function () {
                sessionStorage.setItem('notif_scroll_pos', window.scrollY);
            });
        });
    });
</script>
@endsection