@extends('layouts.app')

@section('content')
<style>
  /* =========================================================
     1. BASE & DESKTOP STYLES
     ========================================================= */
  .detail-pasokan-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 24px 16px;
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
  }

  .detail-header-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px 24px;
    margin-bottom: 20px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
  }

  .detail-header-top {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 16px;
    margin-bottom: 16px;
  }

  .detail-title-group h2 {
    font-size: 20px;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 4px 0;
  }

  .detail-title-group p {
    font-size: 13px;
    color: #64748b;
    margin: 0;
  }

  /* Filter Bar & Input Search */
  .filter-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    padding-top: 16px;
    border-top: 1px solid #f1f5f9;
  }

  .filter-search-group {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    max-width: 500px;
  }

  .search-input-wrap {
    position: relative;
    flex: 1;
  }

  .search-input-wrap input {
    width: 100%;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 10px 16px 10px 40px;
    font-size: 13px;
    color: #334155;
    outline: none;
    box-sizing: border-box;
    background: #ffffff;
    transition: all 0.2s;
  }
  .search-input-wrap input:focus {
    border-color: #0f172a;
    box-shadow: 0 0 0 3px rgba(15, 23, 42, 0.05);
  }

  .search-icon {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 14px;
    color: #94a3b8;
    background: none;
    border: none;
    cursor: pointer;
  }

  /* TOMBOL FILTER BULAT GELAP */
  .btn-filter-icon-round {
    width: 42px;
    height: 42px;
    background: #1e293b;
    color: #ffffff;
    border: none;
    border-radius: 12px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    flex-shrink: 0;
    transition: background 0.2s ease, transform 0.1s ease;
  }
  .btn-filter-icon-round:hover {
    background: #0f172a;
  }
  .btn-filter-icon-round:active {
    transform: scale(0.96);
  }

  .btn-back-dash {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 8px 14px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
  }
  .btn-back-dash:hover { background: #e2e8f0; color: #1e293b; }

  /* Table Card Desktop */
  .table-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    overflow: hidden;
  }

  .pasokan-table {
    width: 100%;
    border-collapse: collapse;
    text-align: left;
    font-size: 13px;
  }

  .pasokan-table th {
    background: #f8fafc;
    color: #475569;
    font-weight: 600;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    padding: 12px 16px;
    border-bottom: 1px solid #e2e8f0;
  }

  .pasokan-table td {
    padding: 14px 16px;
    border-bottom: 1px solid #f1f5f9;
    color: #334155;
    vertical-align: middle;
  }

  .badge-freq {
    background: #eff6ff;
    color: #2563eb;
    padding: 4px 10px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 12px;
    display: inline-block;
  }

  /* Paginasi Desktop */
  .pagination-container {
    padding: 16px 20px;
    border-top: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #ffffff;
  }

  .pagination-links .pagination {
    display: flex;
    align-items: center;
    gap: 4px;
    list-style: none;
    margin: 0;
    padding: 0;
  }

  .pagination-links .page-item .page-link,
  .pagination-links nav span[aria-current="page"] span, 
  .pagination-links nav a {
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
  }

  .pagination-links .page-item.active .page-link, 
  .pagination-links nav span[aria-current="page"] span {
    background-color: #2563eb !important;
    border-color: #2563eb !important;
    color: #ffffff !important;
    font-weight: 600;
  }

  /* =========================================================
     2. MODAL POP-UP STYLES (SOLID BACKGROUND)
     ========================================================= */
  .custom-modal-overlay {
    display: none;
    position: fixed;
    top: 0; 
    left: 0; 
    width: 100vw; 
    height: 100vh;
    background-color: rgba(15, 23, 42, 0.65) !important;
    z-index: 999999 !important;
    align-items: center;
    justify-content: center;
    padding: 16px;
    box-sizing: border-box;
  }

  .custom-modal-overlay.show { 
    display: flex !important; 
  }

  .custom-modal-card {
    background-color: #ffffff !important;
    opacity: 1 !important;
    border-radius: 16px;
    width: 100%;
    max-width: 440px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35) !important;
    overflow: hidden;
    position: relative;
    z-index: 1000000 !important;
    border: 1px solid #e2e8f0;
    animation: modalPop 0.2s ease-out;
  }

  @keyframes modalPop {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
  }

  .modal-head {
    padding: 18px 20px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #ffffff !important;
  }

  .modal-head h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
  }

  .btn-close-modal {
    background: none;
    border: none;
    font-size: 22px;
    color: #94a3b8;
    cursor: pointer;
    line-height: 1;
  }
  .btn-close-modal:hover { color: #334155; }

  .modal-body {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    background: #ffffff !important;
  }

  .form-group-custom {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }

  .form-group-custom label {
    font-size: 12px;
    font-weight: 600;
    color: #475569;
  }

  .form-group-custom input[type="date"],
  .form-group-custom select {
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 9px 12px;
    font-size: 13px;
    color: #1e293b;
    outline: none;
    width: 100%;
    box-sizing: border-box;
    background: #ffffff !important;
  }

  .date-range-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
  }

  .checkbox-group {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #f8fafc;
    padding: 12px;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    cursor: pointer;
  }

  .checkbox-group input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: #2563eb;
    cursor: pointer;
  }

  .checkbox-group span {
    font-size: 12px;
    font-weight: 500;
    color: #334155;
  }

  .modal-footer {
    padding: 14px 20px;
    background: #f8fafc !important;
    border-top: 1px solid #f1f5f9;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
  }

    .btn-pdf-export {
        background-color: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
        border-radius: 20px; /* Bentuk rounded pill elegan */
        padding: 9px 18px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .btn-pdf-export:hover {
        background-color: #dc2626;
        color: #ffffff;
        border-color: #dc2626;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.25);
        transform: translateY(-1px);
    }  

  .btn-reset-filter {
    background: #ffffff;
    color: #64748b;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 9px 16px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    cursor: pointer;
  }
  .btn-reset-filter:hover { background: #f1f5f9; color: #334155; }

  .btn-apply-filter {
    background: #2563eb;
    color: #ffffff;
    border: none;
    border-radius: 8px;
    padding: 9px 18px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
  }
  .btn-apply-filter:hover { background: #1d4ed8; }

  /* =========================================================
     3. CONTROL VISIBILITY & MOBILE STYLES
     ========================================================= */
  .show-on-desktop { display: block; }
  .show-on-mobile { display: none; }

  @media (max-width: 768px) {
    .show-on-desktop { display: none !important; }
    .show-on-mobile { display: flex !important; }

    .detail-pasokan-container { padding: 12px 8px; }
    .detail-header-card { padding: 14px 12px; border-radius: 10px; }

    .detail-header-top {
      flex-direction: column;
      align-items: stretch;
      gap: 12px;
    }

    .btn-back-dash {
      width: 100%;
      box-sizing: border-box;
      justify-content: center;
    }

    .filter-bar {
      flex-direction: column;
      align-items: stretch;
      gap: 10px;
    }

    .filter-search-group {
      max-width: 100%;
      width: 100%;
    }

    /* Mobile Card List */
    .pemasok-m-list {
      flex-direction: column;
      gap: 12px;
      padding: 12px;
    }

    .pemasok-m-card {
      background: #ffffff;
      border: 1px solid #e2e8f0;
      border-radius: 10px;
      padding: 12px;
      display: flex;
      flex-direction: column;
      gap: 10px;
    }

    .pemasok-m-head {
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      border-bottom: 1px solid #f1f5f9;
      padding-bottom: 8px;
    }

    .pemasok-m-body {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px;
      font-size: 12px;
      background: #f8fafc;
      padding: 8px 10px;
      border-radius: 8px;
    }

    .pemasok-m-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #f0fdf4;
      padding: 8px 12px;
      border-radius: 8px;
      border: 1px solid #dcfce7;
    }

    /* Paginasi Mobile Ringkas */
    .pagination-container-mobile {
      padding: 12px;
      border-top: 1px solid #f1f5f9;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 10px;
      background: #ffffff;
    }
  }
</style>

<div class="detail-pasokan-container">
    <!-- Header & Filter Bar -->
    <div class="detail-header-card">
        <div class="detail-header-top">
            <div class="detail-title-group">
                <h2>Laporan Detail Pasokan Pemasok</h2>
                <p>Monitoring kontribusi barang masuk per pemasok periode <b>{{ $formattedDateFrom }}</b> s/d <b>{{ $formattedDateTo }}</b></p>
            </div>
            <a href="{{ route('dashboard.gudang', ['date_from' => $formattedDateFrom, 'date_to' => $formattedDateTo]) }}" class="btn-back-dash">
                &larr; Kembali ke Dashboard
            </a>
        </div>

        <div class="filter-bar">
            <!-- FORM UTAMA SEARCH & FILTER -->
            <form action="{{ route('pemasok.detail-pasokan') }}" method="GET" id="mainSearchForm" class="filter-search-group">
                <input type="hidden" name="pemasok_id" value="{{ request('pemasok_id', $selectedPemasokId ?? '') }}">
                <input type="hidden" name="date_from" value="{{ $formattedDateFrom }}">
                <input type="hidden" name="date_to" value="{{ $formattedDateTo }}">
                @if(request('show_all'))
                    <input type="hidden" name="show_all" value="1">
                @endif

                <div class="search-input-wrap">
                    <button type="submit" class="search-icon" title="Cari">🔍</button>
                    <input type="text" name="search" placeholder="Cari nama pemasok, PIC, atau email..." value="{{ request('search') }}">
                </div>

                <button type="button" class="btn-filter-icon-round" onclick="toggleModalFilter()" title="Filter Laporan">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="4" y1="21" x2="4" y2="14"></line>
                        <line x1="4" y1="10" x2="4" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12" y2="3"></line>
                        <line x1="20" y1="21" x2="20" y2="16"></line>
                        <line x1="20" y1="12" x2="20" y2="3"></line>
                        <line x1="1" y1="14" x2="7" y2="14"></line>
                        <line x1="9" y1="8" x2="15" y2="8"></line>
                        <line x1="17" y1="16" x2="23" y2="16"></line>
                    </svg>
                </button>
            </form>

            <a href="{{ route('pemasok.export-pdf', ['pemasok_id' => $selectedPemasokId ?? '', 'date_from' => $formattedDateFrom, 'date_to' => $formattedDateTo, 'search' => request('search'), 'show_all' => request('show_all')]) }}" 
               target="_blank" class="btn-pdf-export">
               <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="12" y1="18" x2="12" y2="12"></line>
                <polyline points="9 15 12 18 15 15"></polyline>
            </svg>
              Export PDF
            </a>
        </div>
    </div>

    <!-- Tabel Data Pemasok -->
    <div class="table-card">
        
        <!-- 1. TAMPILAN DESKTOP -->
        <div class="show-on-desktop" style="overflow-x: auto;">
            <table class="pasokan-table">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center;">No</th>
                        <th>Nama Pemasok</th>
                        <th>Kontak / PIC</th>
                        <th style="text-align: center;">Frekuensi Transaksi</th>
                        <th style="text-align: right;">Total Barang Masuk</th>
                        <th style="text-align: right;">Total Nominal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pemasoks as $index => $pemasok)
                        @php
                            $nomorUrut = method_exists($pemasoks, 'firstItem') ? ($pemasoks->firstItem() + $index) : ($index + 1);
                        @endphp
                        <tr>
                            <td style="text-align: center; font-weight: 600; color: #94a3b8;">{{ $nomorUrut }}</td>
                            <td>
                                <div style="font-weight: 600; color: #0f172a;">{{ $pemasok->nama_pemasok }}</div>
                                <div style="font-size: 12px; color: #64748b;">{{ $pemasok->email ?? '-' }}</div>
                            </td>
                            <td>
                                <div style="font-weight: 500;">{{ $pemasok->nama_pic ?? '-' }}</div>
                                <div style="font-size: 12px; color: #64748b;">{{ $pemasok->no_telepon ?? '-' }}</div>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge-freq">
                                    {{ number_format($pemasok->total_transaksi) }} Kali
                                </span>
                            </td>
                            <td style="text-align: right; font-weight: 700; color: #0f172a;">
                                {{ number_format($pemasok->total_barang_masuk, 0, ',', '.') }} Unit
                            </td>
                            <td style="text-align: right; font-weight: 700; color: #16a34a;">
                                Rp {{ number_format($pemasok->total_nominal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="padding: 32px; text-align: center; color: #94a3b8;">
                                Tidak ada data pasokan barang masuk yang sesuai dengan kata kunci pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Paginasi Desktop -->
            @if(method_exists($pemasoks, 'hasPages') && ($pemasoks->hasPages() || $pemasoks->total() > 0))
                <div class="pagination-container">
                    <div style="font-size: 13px; color: #64748b;">
                        Menampilkan <b>{{ $pemasoks->firstItem() ?? 0 }}</b> - <b>{{ $pemasoks->lastItem() ?? 0 }}</b> dari <b>{{ $pemasoks->total() }}</b> pemasok
                    </div>
                    <div class="pagination-links">
                        {{ $pemasoks->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            @endif
        </div>

        <!-- 2. TAMPILAN MOBILE -->
        <div class="show-on-mobile pemasok-m-list">
            @forelse($pemasoks as $index => $pemasok)
                @php
                    $nomorUrut = method_exists($pemasoks, 'firstItem') ? ($pemasoks->firstItem() + $index) : ($index + 1);
                @endphp
                <div class="pemasok-m-card">
                    <div class="pemasok-m-head">
                        <div>
                            <span style="font-size: 10px; font-weight: 700; color: #94a3b8;">#{{ $nomorUrut }}</span>
                            <strong style="color: #0f172a; font-size: 14px; display: block;">{{ $pemasok->nama_pemasok }}</strong>
                            <span style="font-size: 11px; color: #64748b;">{{ $pemasok->email ?? '-' }}</span>
                        </div>
                        <span class="badge-freq">
                            {{ number_format($pemasok->total_transaksi) }} Transaksi
                        </span>
                    </div>

                    <div class="pemasok-m-body">
                        <div>
                            <span style="color: #64748b; font-size: 10px; display: block;">PIC / KONTAK</span>
                            <strong style="color: #334155;">{{ $pemasok->nama_pic ?? '-' }}</strong>
                            <div style="color: #64748b; font-size: 11px;">{{ $pemasok->no_telepon ?? '-' }}</div>
                        </div>
                        <div>
                            <span style="color: #64748b; font-size: 10px; display: block;">TOTAL PASOKAN</span>
                            <strong style="color: #0f172a; font-size: 13px;">{{ number_format($pemasok->total_barang_masuk, 0, ',', '.') }} Unit</strong>
                        </div>
                    </div>

                    <div class="pemasok-m-footer">
                        <span style="font-size: 11px; color: #166534; font-weight: 700;">TOTAL NOMINAL</span>
                        <strong style="color: #15803d; font-size: 14px;">Rp {{ number_format($pemasok->total_nominal, 0, ',', '.') }}</strong>
                    </div>
                </div>
            @empty
                <div style="padding: 24px 12px; text-align: center; color: #94a3b8; font-size: 13px;">
                    Tidak ada data pasokan barang masuk yang sesuai dengan kata kunci pencarian.
                </div>
            @endforelse

            <!-- Paginasi Mobile -->
            @if(method_exists($pemasoks, 'hasPages') && ($pemasoks->hasPages() || $pemasoks->total() > 0))
                <div class="pagination-container-mobile">
                    <div style="font-size: 12px; color: #64748b;">
                        Halaman <b>{{ $pemasoks->currentPage() }}</b> dari <b>{{ $pemasoks->lastPage() }}</b>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        @if ($pemasoks->onFirstPage())
                            <span style="padding: 6px 12px; background: #f1f5f9; color: #cbd5e1; border-radius: 6px; font-size: 12px; font-weight: 600;">&larr; Prev</span>
                        @else
                            <a href="{{ $pemasoks->appends(request()->query())->previousPageUrl() }}" 
                               style="padding: 6px 12px; background: #ffffff; color: #2563eb; border: 1px solid #cbd5e1; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">
                                &larr; Prev
                            </a>
                        @endif

                        @if ($pemasoks->hasMorePages())
                            <a href="{{ $pemasoks->appends(request()->query())->nextPageUrl() }}" 
                               style="padding: 6px 12px; background: #ffffff; color: #2563eb; border: 1px solid #cbd5e1; border-radius: 6px; text-decoration: none; font-size: 12px; font-weight: 600;">
                                Next &rarr;
                            </a>
                        @else
                            <span style="padding: 6px 12px; background: #f1f5f9; color: #cbd5e1; border-radius: 6px; font-size: 12px; font-weight: 600;">Next &rarr;</span>
                        @endif
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>

<!-- =========================================================
     4. MODAL POP-UP FILTER
     ========================================================= -->
<div class="custom-modal-overlay" id="modalFilterBackdrop" onclick="handleBackdropClick(event)">
    <div class="custom-modal-card">
        <div class="modal-head">
            <h3>Filter Laporan Pasokan</h3>
            <button type="button" class="btn-close-modal" onclick="toggleModalFilter()">&times;</button>
        </div>
        <form action="{{ route('pemasok.detail-pasokan') }}" method="GET" id="modalFilterForm">
            <!-- Simpan nilai kata kunci pencarian saat ini -->
            <input type="hidden" name="search" id="hiddenSearchInput" value="{{ request('search') }}">

            <div class="modal-body">
                <div class="form-group-custom">
                    <label>Pemasok Spesifik:</label>
                    <select name="pemasok_id">
                        <option value="">-- Semua Pemasok --</option>
                        @foreach($listPemasok as $p)
                            <option value="{{ $p->id }}" {{ ($selectedPemasokId ?? '') == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_pemasok }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group-custom">
                    <label>Rentang Tanggal:</label>
                    <div class="date-range-grid">
                        <input type="date" name="date_from" value="{{ $formattedDateFrom }}">
                        <input type="date" name="date_to" value="{{ $formattedDateTo }}">
                    </div>
                </div>

                <label class="checkbox-group">
                    <input type="checkbox" name="show_all" value="1" {{ request('show_all') ? 'checked' : '' }}>
                    <span>Tampilkan semua pemasok (termasuk 0 transaksi)</span>
                </label>
            </div>

            <div class="modal-footer">
                <a href="{{ route('pemasok.detail-pasokan') }}" class="btn-reset-filter">Reset Filter</a>
                <button type="submit" class="btn-apply-filter">Terapkan Filter</button>
            </div>
        </form>
    </div>
</div>

<script>
  function toggleModalFilter() {
    const backdrop = document.getElementById('modalFilterBackdrop');
    backdrop.classList.toggle('show');
  }

  function handleBackdropClick(event) {
    if (event.target.id === 'modalFilterBackdrop') {
      toggleModalFilter();
    }
  }

  // Sinkronkan isi input pencarian sebelum form disubmit
  document.getElementById('modalFilterForm')?.addEventListener('submit', function() {
    const mainSearchValue = document.querySelector('input[name="search"]')?.value || '';
    document.getElementById('hiddenSearchInput').value = mainSearchValue;
  });
</script>
@endsection