@extends('layouts.app')

@section('content')
<style>

    .pagehead {
      display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 24px;
  }

  /* Kotak Filter Tanggal */
  .global-date-filter {
    background: #ffffff;
    border: 1px solid #e2e6ec;
    border-radius: 10px;
    padding: 8px 14px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
  }

  .filter-inputs {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0;
  }

  .filter-inputs label {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 500;
    color: #4b5563;
  }

  .filter-inputs input[type="date"] {
    font-size: 12px;
    padding: 6px 10px;
    border-radius: 6px;
    border: 1px solid #d1d5db;
    color: #1f2430;
    background: #f9fafb;
    outline: none;
  }

  /* Tombol Terapkan Filter */
  .btn-apply-date {
    background: #f5921b;
    color: #ffffff;
    border: none;
    border-radius: 6px;
    padding: 7px 14px;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
  }

  .btn-apply-date:hover {
    background: #d97706;
  }

    .dbg-wrap { max-width: 1200px; margin: 0 auto; padding: 24px 16px; background-color: #f4f6f9; font-family: 'Inter', system-ui, -apple-system, sans-serif; }
    .dbg-title { font-size: 24px; font-weight: 800; margin: 0 0 2px; color: #1f2430; }
    .dbg-subtitle { font-size: 13px; color: #8a8f9c; margin-bottom: 22px; }

    .dbg-card {
        background: #fff;
        border: 1px solid #e7e9ee;
        border-radius: 12px;
        padding: 20px 22px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .dbg-row { display: grid; gap: 20px; margin-bottom: 20px; }
    .dbg-row-2 { grid-template-columns: 1fr 1fr; }
    @media (max-width: 991px) { .dbg-row-2 { grid-template-columns: 1fr; } }

    .dbg-kpi-label { font-size: 12px; color: #8a8f9c; letter-spacing: .5px; font-weight: 700; text-transform: uppercase; }
    .dbg-kpi-value-wrap { display: flex; align-items: center; gap: 10px; margin: 8px 0 4px; }
    .dbg-kpi-badge { width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: bold; }
    .dbg-kpi-badge.green { background: #e6f4ea; color: #2e9e5b; }
    .dbg-kpi-badge.red { background: #fdeaea; color: #d64545; }
    .dbg-kpi-value { font-size: 32px; font-weight: 800; line-height: 1; }
    .dbg-kpi-value.green { color: #2e9e5b; }
    .dbg-kpi-value.red { color: #d64545; }
    .dbg-kpi-period { font-size: 10px; color: #8a8f9c; }

    .dbg-card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; flex-wrap: wrap; gap: 10px; }
    .dbg-card-title { font-size: 15px; font-weight: 700; margin: 0 0 2px; color: #1f2430; }
    .dbg-card-sub { font-size: 12px; color: #8a8f9c; margin: 0; }
    .dbg-link { color: #3d6bff; font-size: 12px; text-decoration: none; font-weight: 600; }
    .dbg-link:hover { text-decoration: underline; }

    .dbg-filter-btn { padding: 5px 12px; border-radius: 20px; font-size: 12px; border: 1px solid #e7e9ee; background: #fff; color: #555; cursor: pointer; }
    .dbg-filter-btn.active { background: #f97316; color: #fff; border-color: #f97316; font-weight: 600; }
    .dbg-select { padding: 5px 10px; border-radius: 8px; font-size: 12px; border: 1px solid #e7e9ee; background: #fff; color: #444; }

    .dbg-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .dbg-table th { text-align: left; color: #8a8f9c; font-weight: 600; font-size: 11px; padding: 8px 6px; border-bottom: 1px solid #eee; text-transform: uppercase; }
    .dbg-table td { padding: 12px 6px; border-bottom: 1px solid #f5f6f8; vertical-align: middle; }

    .dbg-badge { padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; display: inline-block; }
    .dbg-badge.habis { background: #fdeaea; color: #d64545; }
    .dbg-badge.kritis { background: #fff4e0; color: #c07d10; }
    .dbg-badge.rendah { background: #eef2ff; color: #4f46e5; }
    .dbg-badge.aman { background: #e6f4ea; color: #2e9e5b; }

    .dbg-exp-item { border-left: 4px solid #f97316; padding: 10px 12px; margin-bottom: 10px; border-radius: 4px; background: #fafafa; }
    .dbg-exp-item.urgent { border-left-color: #d64545; }
    .dbg-exp-item .nm { font-weight: 600; font-size: 13px; color: #1f2430; }
    .dbg-exp-item .meta { font-size: 11px; color: #8a8f9c; margin-top: 2px; }
    .dbg-exp-item .days { float: right; color: #d64545; font-weight: 700; font-size: 12px; }

    .dbg-prio-row { display: flex; align-items: center; padding: 10px 4px; border-bottom: 1px solid #f5f6f8; font-size: 13px; }
    .dbg-prio-num { width: 26px; font-weight: 700; color: #a3a8b3; }
    .dbg-prio-name { flex: 1; font-weight: 600; color: #1f2430; }
    .dbg-prio-note { color: #8a8f9c; font-size: 12px; margin-left: 12px; }

    #prioTableContainer .pagination-wrapper {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 16px;
        margin-top: 12px;
        border-top: 1px solid #f1f5f9;
        font-size: 13px;
        color: #64748b;
    }

    #prioTableContainer .pagination {
       display: flex;
        align-items: center;
        gap: 4px;
        list-style: none;
        margin: 0;
        padding: 0; 
    }

    #prioTableContainer .pagination .page-item .page-link,
    #prioTableContainer nav span[aria-current="page"] span,
    #prioTableContainer nav a {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 32px;
        height: 32px;
        padding: 0 10px;
        border-radius: 8px !important;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #475569;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease-in-out;
    }

    #prioTableContainer .pagination .page-item.active .page-link,
    #prioTableContainer nav span[aria-current="page"] span {
        background-color: #2563eb !important;
        border-color: #2563eb !important;
        color: #ffffff !important;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
    }

    #prioTableContainer .pagination .page-item .page-link:hover,
    #prioTableContainer nav a:hover {
        background-color: #f8fafc;
        border-color: #cbd5e1;
        color: #1e293b;
    }

    #prioTableContainer nav svg {
        width: 14px;
        height: 14px;
    }

    .dbg-btn-export { background: #ef4444; color: #fff; border: none; padding: 7px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }
    .chart-container { position: relative; width: 100%; height: 250px; }
</style>

<div class="pagehead">
    <div>
      <h1 style="margin: 0; font-size: 24px; font-weight: 700;">Dashboard Gudang</h1>
      <p style="margin: 4px 0 0; font-size: 13px; color: #6b7280;">Update terakhir: Hari ini, {{ date('H:i') }} WITA</p>
    </div>

    <div class="global-date-filter">
        <form action="{{ route('dashboard.gudang') }}" method="GET" class="filter-inputs">
            <label>
                <span>Dari:</span>
                <input type="date" name="date_from" value="{{ is_object($dateFrom) ? $dateFrom->format('Y-m-d') : $dateFrom }}">            </label>
            <label>
                <span>Sampai:</span>
                <input type="date" name="date_to" value="{{ is_object($dateTo) ? $dateTo->format('Y-m-d') : $dateTo }}">            </label>
            <button type="submit" class="btn primary">Terapkan Filter</button>
        </form>
    </div>
    </div>

    {{-- Baris 1: KPI --}}
    <div class="dbg-row dbg-row-2">
        <div class="dbg-card">
            <div class="dbg-kpi-label">TOTAL BARANG MASUK</div>
            <div class="dbg-kpi-value-wrap">
                <div class="dbg-kpi-badge green">&darr;</div>
                <div class="dbg-kpi-value green">{{ number_format($kpi['total_masuk'] ?? 0) }}</div>
            </div>
            <div class="dbg-kpi-sub">
                Periode: {{ $kpi['periode'] }}
            </div>        
        </div>
        <div class="dbg-card">
            <div class="dbg-kpi-label">TOTAL BARANG KELUAR</div>
            <div class="dbg-kpi-value-wrap">
                <div class="dbg-kpi-badge red">&uarr;</div>
                <div class="dbg-kpi-value red">{{ number_format($kpi['total_keluar'] ?? 0) }}</div>
            </div>
            <div class="dbg-kpi-sub">
                Periode: {{ $kpi['periode'] }}
            </div>
       </div>
    </div>

    {{-- Baris 2: Grafik Tren Transaksi --}}
    <div class="dbg-row">
        <div class="dbg-card">
            <div class="dbg-card-header">
                <div>
                    <div class="dbg-card-title">Grafik Tren Transaksi</div>
                    <div class="dbg-card-sub">Barang masuk vs keluar periode ({{ $dateFrom }} s/d {{ $dateTo }})</div>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="trenChart"></canvas>
            </div>
            <div style="font-size:12px; color:#666; margin-top:10px;">
                Total Masuk: <strong>{{ $kpi['total_masuk'] }} unit</strong> &nbsp;|&nbsp; 
                Total Keluar: <strong>{{ $kpi['total_keluar'] }} unit</strong> &nbsp;|&nbsp; 
            </div>
        </div>
    </div>

    {{-- Baris 3: Top 5 Barang Keluar --}}
    <div class="dbg-row">
        <div class="dbg-card">
            <div class="dbg-card-header">
                <div>
                    <div class="dbg-card-title">Top 5 Barang Keluar</div>
                    <div class="dbg-card-sub">Barang paling sering keluar gudang periode ({{ $dateFrom }} s/d {{ $dateTo }} )</div>
                </div>
            </div>

            @if(!empty($topKeluar) && count($topKeluar) > 0)
                <div class="chart-container">
                    <canvas id="topKeluarChart"></canvas>
                </div>
            @else
                <div style="text-align: center; padding: 40px 10px; color: #94a3b8; font-size: 13px; background: #fafafa; border-radius: 8px; border: 1px dashed #e2e8f0;">
                <div style="font-size: 24px; margin-bottom: 6px;">📦</div>
                Belum ada data barang keluar pada periode ini.
                </div>
            @endif
        </div>
    </div>

    {{-- Baris 4: Top Pemasok + Segera Kadaluarsa --}}
    <div class="dbg-row dbg-row-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; align-items: stretch;">
        
        <!-- CARD LEFT: Top Pemasok -->
        <div class="dbg-card" style="display: flex; flex-direction: column; justify-content: space-between; height: 100%;">
            <div style="display: flex; flex-direction: column; height: 100%;">
                <div class="dbg-card-header" style="margin-bottom: 8px;">
                    <div>
                        <div class="dbg-card-title">Top Pemasok</div>
                        <div class="dbg-card-sub">Proporsi kontribusi pasokan barang periode ({{ is_object($dateFrom) ? $dateFrom->format('Y-m-d') : $dateFrom }} s/d {{ is_object($dateTo) ? $dateTo->format('Y-m-d') : $dateTo }})</div>
                    </div>
                    <a href="{{ route('pemasok.detail-pasokan', ['date_from' => request('date_from', $dateFrom), 'date_to' => request('date_to', $dateTo)]) }}" 
                       style="color: #2563eb; text-decoration: none; font-size: 13px; font-weight: 600;">
                        Detail Pemasok &rarr;
                    </a>            
                </div>

                @if(!empty($topPemasok) && count($topPemasok) > 0)
                    <div class="chart-container" style="height:210px; margin-top: 10px;">
                        <canvas id="pemasokChart"></canvas>
                    </div>

                    @php
                        $totalSemuaPasokan = collect($topPemasok)->sum(function($item) {
                            return is_array($item) ? ($item['total_pasokan'] ?? $item['total_jumlah'] ?? 0) : ($item->total_pasokan ?? $item->total_jumlah ?? 0);
                        });
                        $pemasokUtama = collect($topPemasok)->first();
                        $pasokanUtama = is_array($pemasokUtama) ? ($pemasokUtama['total_pasokan'] ?? $pemasokUtama['total_jumlah'] ?? 0) : ($pemasokUtama->total_pasokan ?? $pemasokUtama->total_jumlah ?? 0);
                        $namaPemasokUtama = is_array($pemasokUtama) ? ($pemasokUtama['nama_pemasok'] ?? 'Unknown') : ($pemasokUtama->nama_pemasok ?? 'Unknown');
                        $persenUtama = $totalSemuaPasokan > 0 ? number_format(($pasokanUtama / $totalSemuaPasokan) * 100, 1) : 0;
                    @endphp
                    <div style="font-size:12px; color:#4b5563; margin-top:auto; background:#f9fafb; padding:8px 12px; border-radius:6px; border: 1px solid #e5e7eb;">
                        <strong>Pemasok Utama:</strong> {{ $namaPemasokUtama }} 
                        (<strong>{{ $persenUtama }}%</strong> dari total pasokan)
                    </div>
                @else
                    <!-- EMPTY STATE OTOMATIS MENGISI SISA TINGGI CARD -->
                    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; flex: 1; min-height: 220px; color: #94a3b8; font-size: 13px; background: #fafafa; border-radius: 8px; border: 1px dashed #e2e8f0; margin-top: 10px; padding: 20px;">
                        <span style="font-size: 32px; margin-bottom: 6px;">🚚</span>
                        <span>Tidak ada pasokan barang pada periode ini.</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- CARD RIGHT: Segera Kadaluarsa -->
        <div class="dbg-card">
            <div>
                <!-- Header & Filter Sort -->
                <div class="dbg-card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                    <div class="dbg-card-title" style="font-size: 16px; font-weight: 700; color: #1e293b;">Segera Kadaluarsa</div>

                    <form method="GET" action="{{ url()->current() }}" style="margin: 0;">
                        @foreach(request()->query() as $key => $value)
                            @if(!in_array($key, ['sort_kadaluarsa', 'kadaluarsa_page']) && is_string($value))
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach

                        <select name="sort_kadaluarsa" onchange="this.form.submit()" 
                                style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 4px 8px; font-size: 12px; color: #334155; outline: none; background-color: #fff; cursor: pointer;">
                            <option value="asc" {{ ($sortKadaluarsa ?? 'asc') == 'asc' ? 'selected' : '' }}>Urutkan: Terdekat (ASC)</option>
                            <option value="desc" {{ ($sortKadaluarsa ?? '') == 'desc' ? 'selected' : '' }}>Urutkan: Terjauh (DESC)</option>
                        </select>
                    </form>
                </div>
                <div class="dbg-card-sub" style="font-size: 13px; color: #64748b; margin-bottom: 12px;">
                    Pengingat barang mendekati tanggal kadaluarsa
                </div>

                <!-- Body Card -->
                <div class="dbg-card-body">
                    @if (isset($kadaluarsa) && $kadaluarsa->count() > 0)
                        <div style="overflow-x: auto;">
                            <table class="table" style="width: 100%; font-size: 13px; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid #e2e8f0; color: #64748b; text-align: left;">
                                        <th style="padding: 8px; width: 35px; text-align: center;">No</th>
                                        <th style="padding: 8px;">Nama Barang</th>
                                        <th style="padding: 8px; text-align: center;">Jumlah</th>
                                        <th style="padding: 8px; text-align: center;">Tgl Kadaluarsa</th>
                                        <th style="padding: 8px; text-align: right; width: 70px;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kadaluarsa as $index => $item)
                                        <tr style="{{ !$loop->last ? 'border-bottom: 1px solid #f1f5f9;' : '' }}">
                                            <td style="padding: 8px; color: #94a3b8; text-align: center; font-weight: 600;">
                                                {{ $kadaluarsa->firstItem() + $index }}
                                            </td>
                                            <td style="padding: 8px; font-weight: 600; color: #1e293b;">
                                                {{ $item->nama_barang }}
                                            </td>
                                            <td style="padding: 8px; text-align: center;">
                                                {{ number_format($item->jumlah) }}
                                            </td>
                                            <td style="padding: 8px; text-align: center; color: #ef4444; font-weight: 600;">
                                                {{ \Carbon\Carbon::parse($item->tanggal_kadaluarsa)->format('d/m/Y') }}
                                            </td>
                                            <td style="padding: 8px; text-align: right;">
                                                <a href="{{ route('barang-keluar.create', ['kode_barang' => $item->kode_barang]) }}" 
                                                   title="Proses Barang Keluar"
                                                   style="padding: 4px 8px; background: #ef4444; color: #ffffff; border-radius: 6px; text-decoration: none; font-size: 11px; font-weight: 600; white-space: nowrap; display: inline-block;">
                                                    Keluar &rarr;
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach                
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div style="text-align: center; padding: 24px; color: #94a3b8; font-size: 13px;">
                            Tidak ada data barang yang mendekati kadaluarsa.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Paginasi (Margin top auto agar selalu di dasar card) -->
            @if(isset($kadaluarsa) && $kadaluarsa->hasPages())
                <div style="margin-top: 12px; display: flex; justify-content: space-between; align-items: center; font-size: 12px; color: #64748b;">
                    <div>
                        Menampilkan <b>{{ $kadaluarsa->firstItem() }}</b> - <b>{{ $kadaluarsa->lastItem() }}</b> dari <b>{{ $kadaluarsa->total() }}</b> barang
                    </div>
                    <div style="display: flex; gap: 4px;">
                        @if ($kadaluarsa->onFirstPage())
                            <span style="padding: 4px 8px; background: #f1f5f9; color: #cbd5e1; border-radius: 6px; cursor: not-allowed; font-weight: 600;">&larr; Prev</span>
                        @else
                            <a href="{{ $kadaluarsa->appends(request()->query())->previousPageUrl() }}" 
                               style="padding: 4px 8px; background: #ffffff; color: #2563eb; border: 1px solid #cbd5e1; border-radius: 6px; text-decoration: none; font-weight: 600;">
                               &larr; Prev
                            </a>                        
                        @endif

                        <span style="padding: 4px 8px; background: #f8fafc; color: #334155; border: 1px solid #e2e8f0; border-radius: 6px; font-weight: 600;">
                            {{ $kadaluarsa->currentPage() }} / {{ $kadaluarsa->lastPage() }}
                        </span>

                        @if ($kadaluarsa->hasMorePages())
                            <a href="{{ $kadaluarsa->appends(request()->query())->nextPageUrl() }}" 
                               style="padding: 4px 8px; background: #ffffff; color: #2563eb; border: 1px solid #cbd5e1; border-radius: 6px; text-decoration: none; font-weight: 600;">
                                Next &rarr;
                            </a>                        
                        @else
                            <span style="padding: 4px 8px; background: #f1f5f9; color: #cbd5e1; border-radius: 6px; cursor: not-allowed; font-weight: 600;">Next &rarr;</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>

    </div>

    {{-- Baris 5: Prioritas Tindakan --}}
    <div class="dbg-row">
        <div class="dbg-card">
            <div class="dbg-card-header">
                <div>
                    <div class="dbg-card-title">Prioritas Tindakan</div>
                    <div class="dbg-card-sub">
                        Monitoring tingkat ketersediaan stok periode ({{ $dateFrom }} s/d {{ $dateTo }})
                    </div>
                </div>
                <div style="display:flex; gap:8px;">
                    <select id="filterStatusPrio" class="dbg-select">
                    <option value="semua" {{ request('filter_status') == 'semua' ? 'selected' : '' }}>Filter: Semua Status</option>
                    <option value="habis" {{ request('filter_status') == 'habis' ? 'selected' : '' }}>Status: Habis</option>
                    <option value="kritis" {{ request('filter_status') == 'kritis' ? 'selected' : '' }}>Status: Kritis</option>
                    <option value="rendah" {{ request('filter_status') == 'rendah' ? 'selected' : '' }}>Status: Rendah</option>
                    <option value="aman" {{ request('filter_status') == 'aman' ? 'selected' : '' }}>Status: Aman</option>
                    </select>

                    <a href="{{ route('prioritas-tindakan.export-pdf', [
                        'filter_status' => request('filter_status', 'semua'),
                        'date_from'     => request('date_from', date('Y-m-01')),
                        'date_to'       => request('date_to', date('Y-m-d'))
                    ]) }}" target="_blank" class="dbg-btn-export">
                        ↓ Export PDF
                    </a>
                </div>
            </div>

            <div id="prioTableContainer">
                @forelse($prioritasTindakan as $i => $item)
                    @php 
                        $tag = $item['tag'] ?? 'Aman';
                        $tagClass = strtolower($tag);
                        $nomorUrut = ($prioritasTindakan->currentPage() - 1) * $prioritasTindakan->perPage() + $i + 1;
                    @endphp
                    <div class="dbg-prio-row prio-item" style="display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 12px 4px; border-bottom: 1px solid #f5f6f8;">            
                        <div style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0;">
                            <span class="dbg-prio-num" style="width: 24px; font-weight: 700; color: #a3a8b3;">{{ $nomorUrut }}</span>
                            <span class="dbg-prio-name" style="font-weight: 600; color: #1f2430;">{{ $item['nama'] }}</span>
                        </div>

                        <div style="display: flex; align-items: center; gap: 12px; flex-shrink: 0;">
                            <span class="dbg-badge {{ $tagClass }}">{{ $tag }}</span>
                            <span class="dbg-prio-note" style="color: #8a8f9c; font-size: 12px; min-width: 110px; text-align: right;">{{ $item['keterangan'] }}</span>

                            @if(in_array($tag, ['Habis', 'Kritis', 'Rendah']))
                                <a href="{{ route('barang-masuk.create', ['kode_barang' => $item['kode_barang']]) }}"
                                    class="btn-apply-date" 
                                    style="text-decoration: none; padding: 4px 10px; font-size: 11px;">
                                    + Restok (Barang Masuk)
                                </a>
                            @endif
                        </div>
                    </div>

                @empty
                    <p style="color:#8a8f9c; font-size:13px;">Semua stok barang dalam kondisi aman.</p>
                @endforelse

                <p id="emptyFilterMsg" style="display:none; color:#8a8f9c; font-size:13px; padding: 10px 4px;">
                    Tidak ada barang dengan status yang dipilih.
                </p>

                @if($prioritasTindakan->hasPages() || $prioritasTindakan->total() > 0)
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Menampilkan <b>{{ $prioritasTindakan->firstItem() ?? 0 }}</b> - <b>{{ $prioritasTindakan->lastItem() ?? 0 }}</b> dari <b>{{ $prioritasTindakan->total() }}</b> data
                        </div>
                        <div class="pagination-links">
                            {{ $prioritasTindakan->links('pagination::bootstrap-4') }}
                        </div>
                    </div>
                @endif
            </div>     
        </div>
    </div>

    {{-- Baris 6: Idle Stock --}}
    <div class="dbg-row">
        <div class="dbg-card">
            <div class="dbg-card-header">
                <div>
                    <div class="dbg-card-title">Idle Stock</div>
                    <div class="dbg-card-sub">Monitoring Perputaran Persediaan Berdasarkan Days Since Last Movement</div>
                </div>
                
                <div style="display:flex; gap:8px; align-items:center;">
                    <form method="GET" action="{{ url()->current() }}" style="margin: 0;">
                        @foreach(request()->query() as $key => $value)
                            @if(!in_array($key, ['idle_sort', 'idle_page']) && is_string($value))
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach     
                        <select name="idle_sort" onchange="this.form.submit()" 
                                style="border: 1px solid #cbd5e1; border-radius: 6px; padding: 5px 10px; font-size: 12px; color: #334155; outline: none; background-color: #fff; cursor: pointer;">
                            <option value="desc" {{ request('idle_sort', 'desc') == 'desc' ? 'selected' : '' }}>Terlama Tidak Bergerak (Desc)</option>
                            <option value="asc" {{ request('idle_sort') == 'asc' ? 'selected' : '' }}>Terbaru Bergerak (Asc)</option>
                        </select>                    
                    </form>     

                    <a href="{{ route('idle-stock.export-pdf', array_merge(request()->all(), ['idle_sort' => request('idle_sort', 'desc')])) }}" 
                    target="_blank" 
                    class="btn btn-danger btn-sm" 
                    style="font-size: 12px; padding: 5px 10px; border-radius: 6px; text-decoration: none;">
                        &darr; Export PDF
                    </a>               
                </div>
            </div>

            <div style="overflow-x: auto;">
                <table class="table" style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="border-bottom: 1px solid #e2e8f0; color: #64748b; text-align: left;">
                            <th style="padding: 10px 8px; width: 35px; text-align: center;">No</th>
                            <th style="padding: 10px 8px;">Nama Barang</th>
                            <th style="padding: 10px 8px; text-align: center;">Stok</th>
                            <th style="padding: 10px 8px; text-align: center;">Terakhir Keluar</th>
                            <th style="padding: 10px 8px; text-align: center;">Tidak Bergerak</th>
                            <th style="padding: 10px 8px; text-align: center;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($idleStock as $index => $item)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            {{-- Nomor Urut Paginasi --}}
                            <td style="padding: 10px 8px; color: #94a3b8; text-align: center; font-weight: 600;">
                                {{ $idleStock->firstItem() + $index }}
                            </td>

                            {{-- Nama Barang + Kode Barang --}}
                            <td style="padding: 10px 8px; font-weight: 600; color: #1e293b;">
                                {{ $item['nama_barang'] ?? '-' }}
                                @if(!empty($item['kode_barang']))
                                    <div style="font-size: 11px; color: #94a3b8; font-weight: normal;">Kode: {{ $item['kode_barang'] }}</div>
                                @endif
                            </td>

                            <td style="padding: 10px 8px; text-align: center; font-weight: 700;">
                                {{ number_format($item['stok'] ?? 0) }}
                            </td>

                            <td style="padding: 10px 8px; text-align: center; color: #64748b;">
                                {{ $item['terakhir_bergerak'] ?? '-' }}
                            </td>

                            <td style="padding: 10px 8px; text-align: center; color: #1e293b; font-weight: 700;">
                                {{ $item['hari_idle_num'] ?? 0 }} hari
                            </td>

                            <td style="padding: 10px 8px; text-align: center;">
                                @php
                                    $hari = $item['hari_idle_num'] ?? 0;
                                    if ($hari >= 90) {
                                        $statusText = 'Idle';
                                        $dotColor = '#ef4444'; // Merah
                                        $bgColor = '#fef2f2';
                                    } elseif ($hari >= 30) {
                                        $statusText = 'Perlu Perhatian';
                                        $dotColor = '#f59e0b'; // Kuning
                                        $bgColor = '#fffbe2';
                                    } else {
                                        $statusText = 'Aktif';
                                        $dotColor = '#10b981'; // Hijau
                                        $bgColor = '#ecfdf5';
                                    }
                                @endphp
                                <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; background: {{ $bgColor }}; color: #334155;">
                                    <span style="width: 8px; height: 8px; border-radius: 50%; background-color: {{ $dotColor }}; display: inline-block;"></span>
                                    {{ $statusText }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: #94a3b8; padding: 20px;">
                                Tidak ada data barang idle.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Paginasi Bersih Tanpa Garis Mengganggu --}}
            @if($idleStock->hasPages())
                <div style="margin-top: 12px; display: flex; justify-content: space-between; align-items: center; font-size: 12px; color: #64748b;">
                    <div>
                        Menampilkan <b>{{ $idleStock->firstItem() }}</b> - <b>{{ $idleStock->lastItem() }}</b> dari <b>{{ $idleStock->total() }}</b> barang
                    </div>
                    <div style="display: flex; gap: 6px;">
                        @if ($idleStock->onFirstPage())
                            <span style="padding: 5px 10px; background: #f1f5f9; color: #cbd5e1; border-radius: 6px; cursor: not-allowed; font-weight: 600;">&larr; Prev</span>
                        @else
                            <a href="{{ $idleStock->appends(request()->query())->previousPageUrl() }}" 
                            style="padding: 5px 10px; background: #ffffff; color: #2563eb; border: 1px solid #cbd5e1; border-radius: 6px; text-decoration: none; font-weight: 600;">
                            &larr; Prev
                            </a>
                        @endif

                        <span style="padding: 5px 10px; background: #f8fafc; color: #334155; border: 1px solid #e2e8f0; border-radius: 6px; font-weight: 600;">
                            {{ $idleStock->currentPage() }} / {{ $idleStock->lastPage() }}
                        </span>

                        @if ($idleStock->hasMorePages())
                            <a href="{{ $idleStock->appends(request()->query())->nextPageUrl() }}" 
                            style="padding: 5px 10px; background: #ffffff; color: #2563eb; border: 1px solid #cbd5e1; border-radius: 6px; text-decoration: none; font-weight: 600;">
                            Next &rarr;
                            </a>
                        @else
                            <span style="padding: 5px 10px; background: #f1f5f9; color: #cbd5e1; border-radius: 6px; cursor: not-allowed; font-weight: 600;">Next &rarr;</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script> 

<script>
  document.addEventListener('DOMContentLoaded', function () {
    
    // 1. Chart Tren Transaksi
    const trenData = @json($trenTransaksi ?? []);
    const trenCanvas = document.getElementById('trenChart');
    if (trenCanvas) {
      new Chart(trenCanvas, {
        type: 'line',
        data: {
          labels: trenData.map(d => d.label),
          datasets: [
            { label: 'Barang Masuk', data: trenData.map(d => d.masuk), borderColor: '#2e9e5b', backgroundColor: '#2e9e5b', tension: .3, pointRadius: 4 },
            { label: 'Barang Keluar', data: trenData.map(d => d.keluar), borderColor: '#d64545', backgroundColor: '#d64545', tension: .3, pointRadius: 4 }
          ]
        },
        options: { 
          responsive: true, 
          maintainAspectRatio: false,
          plugins: {
            datalabels: { display: false } 
          },
            scales: {
                y: {
                beginAtZero: true,
                min: 0, 
                ticks: {
                    precision: 0, 
                    }
                }
            }
        }
    });
}

    // 2. Chart Top Keluar
    const topKeluarData = @json($topKeluar ?? []);
    const topKeluarCanvas = document.getElementById('topKeluarChart');
    if (topKeluarCanvas) {
      new Chart(topKeluarCanvas, {
        type: 'bar',
        data: {
          labels: topKeluarData.map(b => b.nama_barang),
          datasets: [{
            label: 'Frekuensi Transaksi',
            data: topKeluarData.map(b => b.frekuensi ?? b.total_jumlah ?? 0),
            backgroundColor: '#f97316',
            borderRadius: 4 
          }]
        },
        options: { 
          indexAxis: 'y', 
          responsive: true, 
          maintainAspectRatio: false,
          plugins: {
            datalabels: { display: false } 
          },
          scales: {
            x: {
              beginAtZero: true,
              ticks: {
                precision: 0,
                stepSize: 1
              }
            }
          }
        }
      });
    }

    // 3. Chart Top Pemasok (Donut Chart dengan Persentase)
    const pemasokData = @json($topPemasok ?? []);
    const pemasokCanvas = document.getElementById('pemasokChart');
    
    if (pemasokCanvas) {
      const totals = pemasokData.map(s => Number(s.total_pasokan ?? s.total_jumlah ?? s.jumlah ?? 0));
      const totalSemua = totals.reduce((a, b) => a + b, 0);

      new Chart(pemasokCanvas, {
        type: 'doughnut',
        plugins: [ChartDataLabels], 
        data: {
          labels: pemasokData.map(s => s.nama_pemasok ?? s.nama ?? 'Unknown'),
          datasets: [{
            data: totals,
            backgroundColor: ['#f97316', '#2563eb', '#16a34a', '#eab308', '#a855f7']
          }]
        },
        options: { 
          responsive: true, 
          maintainAspectRatio: false, 
          plugins: { 
            legend: { 
              position: 'right' 
            },
            tooltip: {
              callbacks: {
                label: function(context) {
                  const val = context.raw || 0;
                  const pct = totalSemua > 0 ? ((val / totalSemua) * 100).toFixed(1) : 0;
                  return ` ${context.label}: ${val.toLocaleString()} unit (${pct}%)`;
                }
              }
            },

            datalabels: {
              color: '#ffffff',
              font: { weight: 'bold', size: 11 },
              formatter: (value) => {
                if (totalSemua === 0) return '';
                const pct = (value * 100 / totalSemua).toFixed(1);
                return pct > 2 ? pct + '%' : ''; 
              }
            }
          } 
        }
      });
    }

    // Filter Interaktif Prioritas Tindakan
    const filterPrioSelect = document.getElementById('filterStatusPrio');
    const prioContainer = document.getElementById('prioTableContainer');


    if (filterPrioSelect) {
        filterPrioSelect.addEventListener('change', function () {
            const selectedStatus = this.value.toLowerCase();
            let url = new URL(window.location.href);

            if (selectedStatus && selectedStatus !== 'semua') {
                url.searchParams.set('filter_status', selectedStatus);
            } else {
                url.searchParams.delete('filter_status');
            }

            url.searchParams.delete('prio_page');

            window.location.href = url.toString();
        });
    }

    // Filter Interaktif Idle Stock
   function updateIdleExportLink(days) {
        const btn = document.getElementById('btnExportIdle');
        if (btn) {
            const baseUrl = "{{ route('idle-stock.export-pdf') }}";
            btn.href = baseUrl + '?idle_days=' + days;
        }
    }
  });
</script>
@endsection