@extends('layouts.app')

@section('content')
<style>
  /* --- BASE DESKTOP STYLE --- */
  .pagehead {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    margin-top: 12px;
    margin-bottom: 24px;
  }

  .datepicker-dropdown-wrapper { position: relative; display: inline-block; }
  .btn-datepicker-trigger {
    background: #ffffff; border: 1px solid #d1d5db; border-radius: 8px; padding: 8px 14px;
    font-size: 13px; font-weight: 600; color: #374151; display: flex; align-items: center;
    gap: 8px; cursor: pointer; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05); transition: all 0.2s ease;
  }
  .btn-datepicker-trigger:hover { background: #f9fafb; border-color: #9ca3af; }

  .datepicker-popover {
    display: none; position: absolute; top: calc(100% + 8px); right: 0; width: 320px;
    background: #ffffff; border: 1px solid #e5e7eb; border-radius: 12px;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1); z-index: 1000; padding: 12px;
  }
  .datepicker-popover.show { display: block; }

  .preset-list { display: flex; flex-direction: column; gap: 2px; margin-bottom: 12px; }
  .preset-item {
    background: none; border: none; text-align: left; padding: 8px 12px; font-size: 12px;
    font-weight: 600; color: #4b5563; border-radius: 6px; cursor: pointer; transition: all 0.15s ease;
  }
  .preset-item:hover { background: #f3f4f6; color: #111827; }
  .preset-item.active { background: #2563eb; color: #ffffff; }

  .custom-range-box { border-top: 1px solid #f3f4f6; padding-top: 12px; display: flex; flex-direction: column; gap: 8px; }
  .custom-range-inputs { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
  .custom-range-inputs label { font-size: 11px; color: #6b7280; font-weight: 600; display: flex; flex-direction: column; gap: 4px; }
  .custom-range-inputs input[type="date"] {
    width: 100%; font-size: 12px; padding: 5px 8px; border: 1px solid #d1d5db;
    border-radius: 6px; outline: none; color: #1f2937; background: #f9fafb; box-sizing: border-box;
  }

  .popover-actions { display: flex; justify-content: flex-end; gap: 8px; margin-top: 4px; }
  .btn-pop-cancel { background: #f3f4f6; color: #4b5563; border: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; }
  .btn-pop-apply { background: #f5921b; color: #ffffff; border: none; padding: 6px 14px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; }
  .btn-pop-apply:hover { background: #d97706; }
  .btn-restok-action { background-color: #2563eb; color: #ffffff; border: none; padding: 5px 14px; border-radius: 20px; font-size: 12px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; transition: all 0.2s ease; }    
  .btn-restok-action:hover {background-color: #1d4ed8; color: #ffffff; transform: translateY(-1px); box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3);}

  .dbg-wrap { max-width: 1200px; margin: 0 auto; padding: 32px 16px 24px; background-color: #f4f6f9; font-family: 'Inter', system-ui, -apple-system, sans-serif; }
  .dbg-title { font-size: 24px; font-weight: 800; margin: 0 0 2px; color: #1f2430; }
  .dbg-subtitle { font-size: 13px; color: #8a8f9c; margin-bottom: 22px; }

  .dbg-card {
      background: #fff; border: 1px solid #e7e9ee; border-radius: 12px; padding: 20px 22px; box-shadow: 0 2px 4px rgba(0,0,0,0.02);
  }
  .dbg-row { display: grid; gap: 20px; margin-bottom: 20px; }
  .dbg-row-2 { grid-template-columns: 1fr 1fr; }

  .dbg-kpi-label { font-size: 12px; color: #8a8f9c; letter-spacing: .5px; font-weight: 700; text-transform: uppercase; }
  .dbg-kpi-value-wrap { display: flex; align-items: center; gap: 10px; margin: 8px 0 4px; }
  .dbg-kpi-badge { width: 28px; height: 28px; border-radius: 6px; display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: bold; }
  .dbg-kpi-badge.green { background: #e6f4ea; color: #2e9e5b; }
  .dbg-kpi-badge.red { background: #fdeaea; color: #d64545; }
  .dbg-kpi-value { font-size: 32px; font-weight: 800; line-height: 1; }
  .dbg-kpi-value.green { color: #2e9e5b; }
  .dbg-kpi-value.red { color: #d64545; }

  .dbg-card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; flex-wrap: wrap; gap: 10px; }
  .dbg-card-title { font-size: 15px; font-weight: 700; margin: 0 0 2px; color: #1f2430; }
  .dbg-card-sub { font-size: 12px; color: #8a8f9c; margin: 0; }

  .dbg-select { padding: 5px 10px; border-radius: 8px; font-size: 12px; border: 1px solid #e7e9ee; background: #fff; color: #444; }
  .dbg-btn-export { background: #ef4444; color: #fff; border: none; padding: 7px 16px; border-radius: 8px; font-size: 12px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; }

  .dbg-badge { padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; display: inline-block; }
  .dbg-badge.habis { background: #fdeaea; color: #d64545; }
  .dbg-badge.kritis { background: #fff4e0; color: #c07d10; }
  .dbg-badge.rendah { background: #eef2ff; color: #4f46e5; }
  .dbg-badge.aman { background: #e6f4ea; color: #2e9e5b; }

  .dbg-prio-row { display: flex; align-items: center; padding: 10px 4px; border-bottom: 1px solid #f5f6f8; font-size: 13px; }
  .dbg-prio-num { width: 26px; font-weight: 700; color: #a3a8b3; }
  .dbg-prio-name { flex: 1; font-weight: 600; color: #1f2430; }
  .dbg-prio-note { color: #8a8f9c; font-size: 12px; margin-left: 12px; }

  #prioTableContainer .pagination-wrapper {
      display: flex; align-items: center; justify-content: space-between; padding-top: 16px; margin-top: 12px; border-top: 1px solid #f1f5f9; font-size: 13px; color: #64748b;
  }
  #prioTableContainer .pagination { display: flex; align-items: center; gap: 4px; list-style: none; margin: 0; padding: 0; }
  #prioTableContainer .pagination .page-item .page-link,
  #prioTableContainer nav span[aria-current="page"] span, #prioTableContainer nav a {
      display: flex; align-items: center; justify-content: center; min-width: 32px; height: 32px; padding: 0 10px; border-radius: 8px !important; border: 1px solid #e2e8f0; background: #ffffff; color: #475569; font-size: 13px; font-weight: 500; text-decoration: none;
  }
  #prioTableContainer .pagination .page-item.active .page-link, #prioTableContainer nav span[aria-current="page"] span {
      background-color: #2563eb !important; border-color: #2563eb !important; color: #ffffff !important; font-weight: 600;
  }
  .chart-container { position: relative; width: 100%; height: 250px; }

  /* Element Mobile Card (Hanya muncul di HP) */
  .m-card-list { display: none; flex-direction: column; gap: 10px; }
  .m-card-item { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px; font-size: 13px; }
  .m-card-head { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 6px; }
  .m-card-body { display: flex; flex-direction: column; gap: 4px; color: #475569; font-size: 12px; }

  /* Helper Style Responsif untuk Paginasi */
  .m-pagination-box {
    margin-top: 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 12px;
    color: #64748b;
    flex-wrap: wrap;
    gap: 8px;
  }

  /* Custom Pagination Mobile-Friendly khusus Prioritas Tindakan */
  .prio-mobile-pagination {
      display: none;
      align-items: center;
      justify-content: center;
      gap: 8px;
  }

  /* --- 📱 OPTIMASI MOBILE SPESIFIK & RAPI (MAX-WIDTH: 768px) --- */
  @media (max-width: 768px) {
    .dbg-wrap { padding: 12px 8px !important; }
    
    .pagehead { flex-direction: column !important; align-items: stretch !important; gap: 10px !important; }
    .datepicker-dropdown-wrapper { width: 100% !important; }
    .btn-datepicker-trigger { width: 100% !important; justify-content: space-between !important; box-sizing: border-box !important; }
    .datepicker-popover { width: 100% !important; left: 0 !important; right: 0 !important; box-sizing: border-box !important; }

    .dbg-row-2, .dbg-row {
      display: flex !important;
      flex-direction: column !important;
      gap: 14px !important;
      margin-bottom: 14px !important;
    }

    .dbg-card { padding: 14px 12px !important; border-radius: 10px !important; }

    .dbg-card-header {
      flex-direction: column !important;
      align-items: stretch !important;
      gap: 10px !important;
    }
    .dbg-card-header > div:last-child, .dbg-card-header form {
      width: 100% !important;
      display: flex !important;
      gap: 8px !important;
    }
    .dbg-select, .dbg-btn-export, .btn-danger {
      flex: 1 !important;
      text-align: center !important;
      justify-content: center !important;
    }

    /* Chart khusus HP */
    .chart-container { height: 200px !important; }

    /* UBAH TABEL & ROW BIAYA JADI KARTU DI MOBILE */
    .desktop-table, .desktop-prio-list { display: none !important; }
    .m-card-list { display: flex !important; }

    /* Layout khusus Prioritas Tindakan di Mobile Card */
    .m-prio-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      padding: 12px;
      display: flex;
      flex-direction: column;
      gap: 8px;
    }
    .m-prio-top {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 8px;
    }
    .m-prio-title-wrap {
      display: flex;
      align-items: center;
      gap: 8px;
      min-width: 0;
    }
    .m-prio-bottom {
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-top: 1px dashed #f1f5f9;
      padding-top: 8px;
      margin-top: 2px;
    }

    /* OPTIMASI PAGINASI DI HP (Sembunyikan deretan angka panjang agar tidak offside) */
    .m-pagination-box {
      flex-direction: column !important;
      align-items: center !important;
      justify-content: center !important;
      gap: 10px !important;
      text-align: center !important;
    }

    #prioTableContainer .pagination-wrapper {
      flex-direction: column !important;
      align-items: center !important;
      justify-content: center !important;
      gap: 8px !important;
    }

    #prioTableContainer .pagination-links .pagination {
      display: none !important;
    }

    .prio-mobile-pagination {
      display: flex !important;
    }
  }
</style>

<div class="pagehead">
    <div>
      <h1 style="margin: 0; font-size: 24px; font-weight: 800; color: #1f2430;">Dashboard Gudang</h1>
      <p style="margin: 4px 0 0; font-size: 13px; color: #6b7280;">Update terakhir: Hari ini, {{ date('H:i') }} WITA</p>
    </div>

    <!-- DATE RANGE PICKER DROPDOWN POPOVER -->
    <div class="datepicker-dropdown-wrapper" id="datePickerWrapper">
        <button type="button" class="btn-datepicker-trigger" id="btnTogglePopover">
            <span style="font-size: 14px;">📅</span>
            <span id="labelSelectedRange">
                {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} - {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }}
            </span>
            <span style="font-size: 10px; color: #9ca3af; margin-left: 4px;">▼</span>
        </button>

        <div class="datepicker-popover" id="popoverDatePicker">
            <form action="{{ route('dashboard.gudang') }}" method="GET" id="dateFilterForm">
                <div class="preset-list">
                    <button type="button" class="preset-item" onclick="selectPreset('today')">Today</button>
                    <button type="button" class="preset-item" onclick="selectPreset('this_month')">This Month</button>
                    <button type="button" class="preset-item" onclick="selectPreset('last_data', '{{ $lastDataFrom ?? '' }}', '{{ $lastDataTo ?? '' }}')">
                        {{ $lastDataLabel ?? 'Data Terakhir' }}
                    </button>
                </div>

                <div class="custom-range-box" id="boxCustomRange">
                    <div class="custom-range-inputs">
                        <label>
                            <span>Dari:</span>
                            <input type="date" id="input_date_from" name="date_from" value="{{ is_object($dateFrom) ? $dateFrom->format('Y-m-d') : $dateFrom }}">
                        </label>
                        <label>
                            <span>Sampai:</span>
                            <input type="date" id="input_date_to" name="date_to" value="{{ is_object($dateTo) ? $dateTo->format('Y-m-d') : $dateTo }}">
                        </label>
                    </div>
                    <div class="popover-actions">
                        <button type="button" class="btn-pop-cancel" onclick="closePopover()">Batal</button>
                        <button type="submit" class="btn-pop-apply">Apply</button>
                    </div>
                </div>
            </form>
        </div>
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
            <div class="dbg-kpi-sub" style="font-size:12px; color:#8a8f9c;">
                Periode: {{ $kpi['periode'] }}
            </div>        
        </div>
        <div class="dbg-card">
            <div class="dbg-kpi-label">TOTAL BARANG KELUAR</div>
            <div class="dbg-kpi-value-wrap">
                <div class="dbg-kpi-badge red">&uarr;</div>
                <div class="dbg-kpi-value red">{{ number_format($kpi['total_keluar'] ?? 0) }}</div>
            </div>
            <div class="dbg-kpi-sub" style="font-size:12px; color:#8a8f9c;">
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
                Total Keluar: <strong>{{ $kpi['total_keluar'] }} unit</strong>
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
    <div class="dbg-row dbg-row-2">
        
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
                <div class="dbg-card-header">
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

                <div class="dbg-card-body">
                    @if (isset($kadaluarsa) && $kadaluarsa->count() > 0)
                        <!-- DESKTOP TABLE -->
                        <div class="desktop-table">
                            <table class="table" style="width: 100%; font-size: 13px; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid #e2e8f0; color: #64748b; text-align: left;">
                                        <th style="padding: 8px; width: 35px; text-align: center;">No</th>
                                        <th style="padding: 8px;">Nama Barang</th>
                                        <th style="padding: 8px; text-align: center;">Stok Batch</th>
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
                                                <a href="{{ route('barang-keluar.create', ['kode_barang' => $item->kode_barang, 'id_lokasi' => $item->id_lokasi, 'id_kondisi' => $item->id_kondisi]) }}"                                                   
                                                   style="padding: 4px 8px; background: #ef4444; color: #ffffff; border-radius: 6px; text-decoration: none; font-size: 11px; font-weight: 600; white-space: nowrap; display: inline-block;">
                                                    Keluar &rarr;
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach                
                                </tbody>
                            </table>
                        </div>

                        <!-- MOBILE CARD FORMAT -->
                        <div class="m-card-list">
                            @foreach($kadaluarsa as $index => $item)
                                <div class="m-card-item">
                                    <div class="m-card-head">
                                        <strong style="color: #1e293b; font-size: 13px;">{{ $kadaluarsa->firstItem() + $index }}. {{ $item->nama_barang }}</strong>
                                        <a href="{{ route('barang-keluar.create', ['kode_barang' => $item->kode_barang, 'id_lokasi' => $item->id_lokasi, 'id_kondisi' => $item->id_kondisi]) }}" 
                                           style="padding: 4px 10px; background: #ef4444; color: #fff; border-radius: 6px; text-decoration: none; font-size: 11px; font-weight: 600;">
                                            Keluar &rarr;
                                        </a>
                                    </div>
                                    <div class="m-card-body">
                                        <div>Jumlah Stok: <strong>{{ number_format($item->jumlah) }}</strong></div>
                                        <div>Kadaluarsa: <strong style="color:#ef4444;">{{ \Carbon\Carbon::parse($item->tanggal_kadaluarsa)->format('d/m/Y') }}</strong></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div style="text-align: center; padding: 24px; color: #94a3b8; font-size: 13px;">
                            Tidak ada data barang yang mendekati kadaluarsa.
                        </div>
                    @endif
                </div>
            </div>

            @if(isset($kadaluarsa) && $kadaluarsa->hasPages())
                <div class="m-pagination-box">
                    <div>
                        Menampilkan <b>{{ $kadaluarsa->firstItem() }}</b> - <b>{{ $kadaluarsa->lastItem() }}</b> dari <b>{{ $kadaluarsa->total() }}</b>
                    </div>
                    <div style="display: flex; gap: 4px; align-items: center;">
                        @if ($kadaluarsa->onFirstPage())
                            <span style="padding: 4px 8px; background: #f1f5f9; color: #cbd5e1; border-radius: 6px; cursor: not-allowed; font-weight: 600;">&larr; Prev</span>
                        @else
                            <a href="{{ $kadaluarsa->appends(request()->query())->previousPageUrl() }}" style="padding: 4px 8px; background: #ffffff; color: #2563eb; border: 1px solid #cbd5e1; border-radius: 6px; text-decoration: none; font-weight: 600;">&larr; Prev</a>                        
                        @endif

                        <span style="padding: 4px 8px; background: #f8fafc; color: #334155; border: 1px solid #e2e8f0; border-radius: 6px; font-weight: 600;">
                            {{ $kadaluarsa->currentPage() }} / {{ $kadaluarsa->lastPage() }}
                        </span>

                        @if ($kadaluarsa->hasMorePages())
                            <a href="{{ $kadaluarsa->appends(request()->query())->nextPageUrl() }}" style="padding: 4px 8px; background: #ffffff; color: #2563eb; border: 1px solid #cbd5e1; border-radius: 6px; text-decoration: none; font-weight: 600;">Next &rarr;</a>                        
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
                    <div class="dbg-card-sub">Monitoring tingkat ketersediaan stok periode ({{ $dateFrom }} s/d {{ $dateTo }})</div>
                </div>
                <div style="display:flex; gap:8px; flex-wrap: wrap;">
                    <select id="filterStatusPrio" class="dbg-select">
                    <option value="semua" {{ request('filter_status') == 'semua' ? 'selected' : '' }}>Filter: Semua Status</option>
                    <option value="habis" {{ request('filter_status') == 'habis' ? 'selected' : '' }}>Status: Habis</option>
                    <option value="kritis" {{ request('filter_status') == 'kritis' ? 'selected' : '' }}>Status: Kritis</option>
                    <option value="rendah" {{ request('filter_status') == 'rendah' ? 'selected' : '' }}>Status: Rendah</option>
                    <option value="aman" {{ request('filter_status') == 'aman' ? 'selected' : '' }}>Status: Aman</option>
                    </select>

                    <a href="{{ route('prioritas-tindakan.export-pdf', ['filter_status' => request('filter_status', 'semua'), 'date_from' => request('date_from', date('Y-m-01')), 'date_to' => request('date_to', date('Y-m-d'))]) }}" target="_blank" class="dbg-btn-export">
                        ↓ Export PDF
                    </a>
                </div>
            </div>

            <div id="prioTableContainer">
                <!-- DESKTOP PRIORITAS LIST -->
                <div class="desktop-prio-list">
                    @forelse($prioritasTindakan as $i => $item)
                        @php 
                            $tag = $item['tag'] ?? 'Aman';
                            $tagClass = strtolower($tag);
                            $nomorUrut = ($prioritasTindakan->currentPage() - 1) * $prioritasTindakan->perPage() + $i + 1;
                        @endphp
                        <div class="dbg-prio-row prio-item">            
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1; min-width: 0;">
                                <span class="dbg-prio-num">{{ $nomorUrut }}</span>
                                <span class="dbg-prio-name">{{ $item['nama'] }}</span>
                            </div>

                            <div style="display: flex; align-items: center; gap: 12px; flex-shrink: 0;">
                                <span class="dbg-badge {{ $tagClass }}">{{ $tag }}</span>
                                <span class="dbg-prio-note">{{ $item['keterangan'] }}</span>

                                @if(in_array($tag, ['Habis', 'Kritis', 'Rendah']))
                                    <a href="{{ route('barang-masuk.create', ['kode_barang' => $item['kode_barang']]) }}" class="btn-restok-action" style="text-decoration: none; padding: 4px 10px; font-size: 11px; height: auto;">
                                        + Restok
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p style="color:#8a8f9c; font-size:13px;">Semua stok barang dalam kondisi aman.</p>
                    @endforelse
                </div>

                <!-- MOBILE PRIORITAS CARD FORMAT (TAMPIL KHUSUS DI HP) -->
                <div class="m-card-list">
                    @forelse($prioritasTindakan as $i => $item)
                        @php 
                            $tag = $item['tag'] ?? 'Aman';
                            $tagClass = strtolower($tag);
                            $nomorUrut = ($prioritasTindakan->currentPage() - 1) * $prioritasTindakan->perPage() + $i + 1;
                        @endphp
                        <div class="m-prio-card">
                            <div class="m-prio-top">
                                <div class="m-prio-title-wrap">
                                    <span style="font-weight: 700; color: #94a3b8; font-size: 12px;">#{{ $nomorUrut }}</span>
                                    <strong style="color: #1e293b; font-size: 13px;">{{ $item['nama'] }}</strong>
                                </div>
                                <span class="dbg-badge {{ $tagClass }}">{{ $tag }}</span>
                            </div>
                            <div class="m-prio-bottom">
                                <span style="font-size: 12px; color: #64748b;">{{ $item['keterangan'] }}</span>
                                @if(in_array($tag, ['Habis', 'Kritis', 'Rendah']))
                                    <a href="{{ route('barang-masuk.create', ['kode_barang' => $item['kode_barang']]) }}" 
                                       style="padding: 4px 10px; background: #2563eb; color: #ffffff; border-radius: 6px; text-decoration: none; font-size: 11px; font-weight: 600;">
                                        + Restok
                                    </a>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p style="color:#8a8f9c; font-size:13px; text-align: center; padding: 12px;">Semua stok barang dalam kondisi aman.</p>
                    @endforelse
                </div>

                <p id="emptyFilterMsg" style="display:none; color:#8a8f9c; font-size:13px; padding: 10px 4px;">
                    Tidak ada barang dengan status yang dipilih.
                </p>

                @if($prioritasTindakan->hasPages() || $prioritasTindakan->total() > 0)
                    <div class="pagination-wrapper">
                        <div class="pagination-info" style="text-align: center; font-size: 12px; color: #64748b;">
                            Menampilkan <b>{{ $prioritasTindakan->firstItem() ?? 0 }}</b> - <b>{{ $prioritasTindakan->lastItem() ?? 0 }}</b> dari <b>{{ $prioritasTindakan->total() }}</b> data
                        </div>

                        <!-- PAGINATION DESKTOP (Nomor Lengkap) -->
                        <div class="pagination-links">
                            {{ $prioritasTindakan->links('pagination::bootstrap-4') }}
                        </div>

                        <!-- PAGINATION MOBILE (Ringkas & Pas di HP) -->
                        <div class="prio-mobile-pagination">
                            @if ($prioritasTindakan->onFirstPage())
                                <span style="padding: 6px 12px; background: #f1f5f9; color: #cbd5e1; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: not-allowed;">&larr; Prev</span>
                            @else
                                <a href="{{ $prioritasTindakan->appends(request()->query())->previousPageUrl() }}" 
                                   style="padding: 6px 12px; background: #ffffff; color: #2563eb; border: 1px solid #cbd5e1; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">
                                    &larr; Prev
                                </a>
                            @endif

                            <span style="padding: 6px 12px; background: #f8fafc; color: #334155; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 12px; font-weight: 600;">
                                {{ $prioritasTindakan->currentPage() }} / {{ $prioritasTindakan->lastPage() }}
                            </span>

                            @if ($prioritasTindakan->hasMorePages())
                                <a href="{{ $prioritasTindakan->appends(request()->query())->nextPageUrl() }}" 
                                   style="padding: 6px 12px; background: #ffffff; color: #2563eb; border: 1px solid #cbd5e1; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">
                                    Next &rarr;
                                </a>
                            @else
                                <span style="padding: 6px 12px; background: #f1f5f9; color: #cbd5e1; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: not-allowed;">Next &rarr;</span>
                            @endif
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
                
                <div style="display:flex; gap:8px; align-items:center; flex-wrap: wrap;">
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

                    <a href="{{ route('idle-stock.export-pdf', array_merge(request()->all(), ['idle_sort' => request('idle_sort', 'desc')])) }}" target="_blank" class="btn btn-danger btn-sm" style="font-size: 12px; padding: 5px 10px; border-radius: 6px; text-decoration: none;">
                        &darr; Export PDF
                    </a>               
                </div>
            </div>

            <!-- DESKTOP TABLE -->
            <div class="desktop-table">
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
                            <td style="padding: 10px 8px; color: #94a3b8; text-align: center; font-weight: 600;">
                                {{ $idleStock->firstItem() + $index }}
                            </td>
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
                                    if ($hari >= 90) { $statusText = 'Idle'; $dotColor = '#ef4444'; $bgColor = '#fef2f2'; }
                                    elseif ($hari >= 30) { $statusText = 'Perlu Perhatian'; $dotColor = '#f59e0b'; $bgColor = '#fffbe2'; }
                                    else { $statusText = 'Aktif'; $dotColor = '#10b981'; $bgColor = '#ecfdf5'; }
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

            <!-- MOBILE CARD FORMAT -->
            <div class="m-card-list">
                @forelse($idleStock as $index => $item)
                    @php
                        $hari = $item['hari_idle_num'] ?? 0;
                        if ($hari >= 90) { $statusText = 'Idle'; $dotColor = '#ef4444'; $bgColor = '#fef2f2'; }
                        elseif ($hari >= 30) { $statusText = 'Perlu Perhatian'; $dotColor = '#f59e0b'; $bgColor = '#fffbe2'; }
                        else { $statusText = 'Aktif'; $dotColor = '#10b981'; $bgColor = '#ecfdf5'; }
                    @endphp
                    <div class="m-card-item">
                        <div class="m-card-head">
                            <div>
                                <strong style="color: #1e293b; font-size: 13px;">{{ $idleStock->firstItem() + $index }}. {{ $item['nama_barang'] ?? '-' }}</strong>
                                @if(!empty($item['kode_barang']))
                                    <div style="font-size: 11px; color: #94a3b8;">Kode: {{ $item['kode_barang'] }}</div>
                                @endif
                            </div>
                            <span style="display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 12px; font-size: 11px; font-weight: 600; background: {{ $bgColor }}; color: #334155;">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background-color: {{ $dotColor }};"></span>
                                {{ $statusText }}
                            </span>
                        </div>
                        <div class="m-card-body">
                            <div style="display: flex; justify-content: space-between;">
                                <span>Stok Tersedia: <strong>{{ number_format($item['stok'] ?? 0) }}</strong></span>
                                <span>Lama Idle: <strong style="color: #1e293b;">{{ $item['hari_idle_num'] ?? 0 }} Hari</strong></span>
                            </div>
                            <div>Terakhir Keluar: <strong>{{ $item['terakhir_bergerak'] ?? '-' }}</strong></div>
                        </div>
                    </div>
                @empty
                    <div style="text-align: center; padding: 16px; color: #94a3b8; font-size: 12px;">Tidak ada data barang idle.</div>
                @endforelse
            </div>

            @if($idleStock->hasPages())
                <div class="m-pagination-box">
                    <div>
                        Menampilkan <b>{{ $idleStock->firstItem() }}</b> - <b>{{ $idleStock->lastItem() }}</b> dari <b>{{ $idleStock->total() }}</b>
                    </div>
                    <div style="display: flex; gap: 6px; align-items: center;">
                        @if ($idleStock->onFirstPage())
                            <span style="padding: 5px 10px; background: #f1f5f9; color: #cbd5e1; border-radius: 6px; cursor: not-allowed; font-weight: 600;">&larr; Prev</span>
                        @else
                            <a href="{{ $idleStock->appends(request()->query())->previousPageUrl() }}" style="padding: 5px 10px; background: #ffffff; color: #2563eb; border: 1px solid #cbd5e1; border-radius: 6px; text-decoration: none; font-weight: 600;">&larr; Prev</a>
                        @endif

                        <span style="padding: 5px 10px; background: #f8fafc; color: #334155; border: 1px solid #e2e8f0; border-radius: 6px; font-weight: 600;">
                            {{ $idleStock->currentPage() }} / {{ $idleStock->lastPage() }}
                        </span>

                        @if ($idleStock->hasMorePages())
                            <a href="{{ $idleStock->appends(request()->query())->nextPageUrl() }}" style="padding: 5px 10px; background: #ffffff; color: #2563eb; border: 1px solid #cbd5e1; border-radius: 6px; text-decoration: none; font-weight: 600;">Next &rarr;</a>
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
  let chartTrenInstance = null;
  let chartTopKeluarInstance = null;
  let chartPemasokInstance = null;

  // FUNCTION LOGIKA POPOVER FILTER TANGGAL
  const popover = document.getElementById('popoverDatePicker');
  const btnToggle = document.getElementById('btnTogglePopover');
  const boxCustom = document.getElementById('boxCustomRange');

  if (btnToggle) {
    btnToggle.addEventListener('click', function (e) {
      e.stopPropagation();
      popover.classList.toggle('show');
    });
  }

  document.addEventListener('click', function (e) {
    const wrapper = document.getElementById('datePickerWrapper');
    if (wrapper && !wrapper.contains(e.target) && popover) {
      closePopover();
    }
  });

  function closePopover() { if(popover) popover.classList.remove('show'); }
  function toggleCustomRange() { if(boxCustom) boxCustom.style.display = 'flex'; }

  function selectPreset(type, customFrom = '', customTo = '') {
    const formatDate = (d) => {
      const year = d.getFullYear();
      const month = String(d.getMonth() + 1).padStart(2, '0');
      const day = String(d.getDate()).padStart(2, '0');
      return `${year}-${month}-${day}`;
    };

    let from = ''; let to = ''; const now = new Date();

    if (type === 'today') { 
      from = formatDate(now); 
      to = formatDate(now); 
    }
    else if (type === 'this_month') {
      const year = now.getFullYear(); const month = String(now.getMonth() + 1).padStart(2, '0');
      from = `${year}-${month}-01`;
      const lastDayObj = new Date(year, now.getMonth() + 1, 0);
      to = `${year}-${month}-${String(lastDayObj.getDate()).padStart(2, '0')}`;
    } 
    else if (type === 'last_data') { 
      from = customFrom; 
      to = customTo; 
    }

    document.getElementById('input_date_from').value = from;
    document.getElementById('input_date_to').value = to;
    document.getElementById('dateFilterForm').submit();
  }

  function renderDashboardCharts() {
    const isMobile = window.innerWidth <= 768;
    
    // 1. Chart Tren Transaksi
    const trenCanvas = document.getElementById('trenChart');
    if (trenCanvas) {
      if (chartTrenInstance) chartTrenInstance.destroy();
      const trenData = @json($trenTransaksi ?? []);
      chartTrenInstance = new Chart(trenCanvas, {
        type: 'line',
        data: {
          labels: trenData.map(d => d.label),
          datasets: [
            { label: 'Barang Masuk', data: trenData.map(d => d.masuk), borderColor: '#2e9e5b', backgroundColor: '#2e9e5b', tension: .3, pointRadius: 3 },
            { label: 'Barang Keluar', data: trenData.map(d => d.keluar), borderColor: '#d64545', backgroundColor: '#d64545', tension: .3, pointRadius: 3 }
          ]
        },
        options: { 
          responsive: true, maintainAspectRatio: false,
          plugins: { datalabels: { display: false } },
          scales: { y: { beginAtZero: true, min: 0, ticks: { precision: 0 } } }
        }
      });
    }

    // 2. Chart Top Keluar
    const topKeluarCanvas = document.getElementById('topKeluarChart');
    if (topKeluarCanvas) {
      if (chartTopKeluarInstance) chartTopKeluarInstance.destroy();
      const topKeluarData = @json($topKeluar ?? []);
      chartTopKeluarInstance = new Chart(topKeluarCanvas, {
        type: 'bar',
        data: {
          labels: topKeluarData.map(b => b.nama_barang),
          datasets: [{
            label: 'Total Barang Keluar',
            data: topKeluarData.map(b => b.total ?? 0),
            backgroundColor: '#f97316', borderRadius: 4 
          }]
        },
        options: { 
          indexAxis: 'y', responsive: true, maintainAspectRatio: false,
          plugins: { datalabels: { display: false } },
          scales: { x: { beginAtZero: true, ticks: { precision: 0, stepSize: 1 } } }
        }
      });
    }

    // 3. Chart Top Pemasok
    const pemasokCanvas = document.getElementById('pemasokChart');
    if (pemasokCanvas) {
      if (chartPemasokInstance) chartPemasokInstance.destroy();
      const pemasokData = @json($topPemasok ?? []);
      const totals = pemasokData.map(s => Number(s.total_pasokan ?? s.total_jumlah ?? s.jumlah ?? 0));
      const totalSemua = totals.reduce((a, b) => a + b, 0);

      chartPemasokInstance = new Chart(pemasokCanvas, {
        type: 'doughnut',
        plugins: typeof ChartDataLabels !== 'undefined' ? [ChartDataLabels] : [], 
        data: {
          labels: pemasokData.map(s => s.nama_pemasok ?? s.nama ?? 'Unknown'),
          datasets: [{
            data: totals,
            backgroundColor: ['#f97316', '#2563eb', '#16a34a', '#eab308', '#a855f7']
          }]
        },
        options: { 
          responsive: true, maintainAspectRatio: false, 
          plugins: { 
            legend: { 
              position: isMobile ? 'bottom' : 'right',
              labels: { boxWidth: 10, font: { size: 11 } }
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
              font: { weight: 'bold', size: 10 },
              formatter: (value) => {
                if (totalSemua === 0) return '';
                const pct = (value * 100 / totalSemua).toFixed(1);
                return pct > 3 ? pct + '%' : ''; 
              }
            }
          } 
        }
      });
    }
  }

  document.addEventListener('DOMContentLoaded', function () {
    renderDashboardCharts();

    // 4. Filter Interaktif Prioritas Tindakan
    const filterPrioSelect = document.getElementById('filterStatusPrio');
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
  });

  // Handler khusus tombol Back Browser
  window.addEventListener('pageshow', function (event) {
    if (event.persisted) {
      renderDashboardCharts();
    }
  });
</script>
@endsection