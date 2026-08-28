@extends('layouts.app')

@section('content')
<style>
    .container-laporan {
        max-width: 1200px;
        margin: auto;
        padding: 30px 20px;
        font-family: 'Segoe UI', sans-serif;
    }

    .header {
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .back-button {
        padding: 8px 14px;
        background-color: #e5e7eb;
        color: #111827;
        border-radius: 6px;
        font-size: 14px;
        text-decoration: none;
        transition: background 0.2s ease;
        text-align: center;
    }
    .back-button:hover { background-color: #d1d5db; }

    .btn-tambah {
        padding: 8px 16px;
        background-color: #3b82f6;
        color: #fff;
        border-radius: 6px;
        font-size: 14px;
        text-decoration: none;
    }
    .btn-tambah:hover { background-color: #2563eb; }

    /* Filter Form */
    .filter-form {
        background-color: #f9fafb;
        border: 1px solid #d1d5db;
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 25px;
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        align-items: flex-end;
    }
    .filter-form .form-group {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-width: 180px;
    }
    .filter-form label {
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 4px;
    }
    .filter-form input,
    .filter-form select {
        padding: 6px 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }
    .filter-form .action-group {
        display: flex;
        flex-direction: row;
        gap: 10px;
        flex-shrink: 0;
    }
    .filter-form button {
        padding: 8px 16px;
        background-color: #3b82f6;
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.2s ease;
    }
    .filter-form button:hover { background-color: #2563eb; }

    /* Table */
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
    }
    th, td {
        padding: 12px;
        background-color: white;
        text-align: center;
    }
    th {
        background-color: #f3f4f6;
        font-weight: 600;
        border-bottom: 1px solid #ddd;
        white-space: nowrap;
    }
    th a { color: inherit; text-decoration: none; }
    th a:hover { text-decoration: underline; }
    tr { box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.05); border-radius: 8px; }

    td.text-left { text-align: left; }

    .kode-item {
        font-family: 'Courier New', monospace;
        font-size: 13px;
        color: #6b7280;
        background: #f3f4f6;
        padding: 2px 8px;
        border-radius: 6px;
    }
    .btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 7px 14px;
    border-radius: 6px;
    border: none;
    font-size: 14px;
    font-weight: 500;
    text-decoration: none !important;
    line-height: 1.2;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
    }

    .btn-action:hover {
        opacity: 0.85;
        transform: translateY(-1px);
        text-decoration: none !important;
    }

    .btn-action:focus,
    .btn-action:active {
        text-decoration: none !important;
    }
    .badge-model {
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 12px;
        font-weight: 600;
        color: #fff;
        display: inline-block;
    }
    .badge-ses { background-color: #10b981; }
    .badge-hwes { background-color: #8b5cf6; }
    .badge-arima { background-color: #f59e0b; }

    .periode-box {
        font-size: 13px;
        line-height: 1.4;
    }
    .periode-box .horizon-days {
        font-weight: 600;
        color: #111827;
    }
    .periode-box .periode-range {
        color: #6b7280;
        font-size: 12px;
    }

    .hasil-prediksi {
        font-weight: 700;
        font-size: 15px;
        color: #2563eb;
    }
    .hasil-prediksi .unit-label {
        font-weight: 400;
        font-size: 12px;
        color: #6b7280;
        display: block;
    }

    .action-icons a {
        margin: 0 4px;
        text-decoration: none;
        font-size: 13px;
        white-space: nowrap;
    }

    @media(max-width: 768px) {
        .filter-form { flex-direction: column; gap: 12px; align-items: stretch; }
        .filter-form .form-group,
        .filter-form .action-group { width: 100%; }
        .filter-form .action-group { flex-direction: column; }
        .filter-form .action-group .back-button,
        .filter-form .action-group button { width: 100%; }
        .container-laporan { padding: 15px 10px; }
    }
</style>

<div class="container-laporan">
    <div class="header">
        <h2>Riwayat Prediksi Barang Keluar</h2>
        <a href="{{ route('prediksi.create') }}" class="btn-tambah">+ Buat Prediksi Baru</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- Filter Form --}}
    <form method="GET" class="filter-form">
        <div class="form-group">
            <label>Nama Barang:</label>
            <input type="text" name="nama_barang" value="{{ request('nama_barang') }}">
        </div>
        <div class="form-group">
            <label>Model:</label>
            <select name="selected_model">
                <option value="">-- Semua Model --</option>
                <option value="SES" {{ request('selected_model') == 'SES' ? 'selected' : '' }}>SES</option>
                <option value="HWES" {{ request('selected_model') == 'HWES' ? 'selected' : '' }}>HWES (Holt-Winters)</option>
                <option value="ARIMA" {{ request('selected_model') == 'ARIMA' ? 'selected' : '' }}>ARIMA</option>
            </select>
        </div>
        <div class="action-group">
            <button type="submit">Terapkan Filter</button>
            <a href="{{ route('prediksi.index') }}" class="back-button">Reset Filter</a>
        </div>
    </form>

    @php
        $sortBy = $sortBy ?? request('sort_by');
        $columns = [
            'selected_model' => 'Model Forecast',
        ];
        $startNo = $data->firstItem() ?? 1;
    @endphp

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Item</th>
                    <th>Nama Item</th>
                    @foreach($columns as $key => $label)
                        <th>
                            <a href="{{ route('prediksi.index', array_merge(request()->all(), ['sort_by' => $key, 'sort_dir' => ($sortBy === $key && request('sort_dir') === 'asc') ? 'desc' : 'asc'])) }}">
                                {{ $label }}
                                @if($sortBy === $key)
                                    {{ request('sort_dir') === 'asc' ? '↑' : '↓' }}
                                @else
                                    ▲▼
                                @endif
                            </a>
                        </th>
                    @endforeach
                    <th>Periode Prediksi</th>
                    <th>Hasil Prediksi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $i => $run)
                    <tr>
                        <td>{{ $startNo + $i }}</td>
                        <td><span class="kode-item">{{ $run->item->kode_barang ?? '-' }}</span></td>
                        <td class="text-left">{{ $run->item->nama_barang ?? '-' }}</td>
                        <td>
                            @php
                                $badgeClass = match($run->selected_model) {
                                    'SES' => 'badge-ses',
                                    'HWES' => 'badge-hwes',
                                    'ARIMA' => 'badge-arima',
                                    default => 'badge-ses',
                                };
                            @endphp
                            <span class="badge-model {{ $badgeClass }}">{{ $run->selected_model }}</span>
                        </td>
                        <td>
                            <div class="periode-box">
                                <div class="horizon-days">{{ $run->horizon }} hari ke depan</div>
                                <div class="periode-range">
                                    mulai {{ \Carbon\Carbon::parse($run->data_end)->addDay()->format('d M Y') }}
                                    &ndash;
                                    {{ \Carbon\Carbon::parse($run->data_end)->addDays($run->horizon)->format('d M Y') }}
                                </div>
                            </div>'
                            
                        </td>
                        <td>
                            @if($run->total_prediksi !== null)
                                <div class="hasil-prediksi">
                                    {{ number_format($run->total_prediksi, 0, ',', '.') }}
                                    <span class="unit-label">total unit ({{ $run->horizon }} hari)</span>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                         <td class="text-start">
                            <div class="d-flex flex-wrap gap-2">
                                <a href="{{ route('prediksi.show', $run->id) }}" 
                                class="btn-action" style="background-color: #60a5fa; color: #fff;">Detail</a>
                                <a href="{{ route('prediksi.pdf', $run->id) }}" target="_blank"
                                class="btn-action" style="background-color: #34d399; color: #111;">PDF</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">Belum ada riwayat prediksi.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination + summary --}}
    @php
        $total = $data->total();
        $first = $data->firstItem() ?? 0;
        $last  = $data->lastItem() ?? 0;
    @endphp
    <div style="margin-top:20px; text-align:center;">
        <div class="small text-muted">
            Showing {{ $first }} to {{ $last }} of {{ $total }} results
        </div>
        <div class="ms-auto">
            {!! $data->appends(request()->query())->links('pagination::bootstrap-5') !!}
        </div>
    </div>
</div>
@endsection