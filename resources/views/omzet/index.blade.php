@extends('layouts.app')




@section('content')
<style>
    .container-laporan {
        max-width: 1200px;
        margin: 42px auto 24px;;
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
        min-width: 160px;
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




    /* Tombol */
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




    /* Export */
    .export-buttons { margin-bottom: 20px; }
    .export-buttons a {
        padding: 8px 12px;
        background-color: #2563eb;
        color: #fff;
        border-radius: 6px;
        margin-right: 10px;
        text-decoration: none;
        font-size: 14px;
    }
    .export-buttons a:hover { background-color: #1d4ed8; }




    /* Table */
    .table-wrapper { overflow-x: auto; }
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
        min-width: 900px;
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
    }
    th a { color: inherit; text-decoration: none; }
    th a:hover { text-decoration: underline; }
    tr { box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.05); border-radius: 8px; }
    .total-row { background-color: yellow; font-weight: bold; }




    /* Responsif */
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
        <h2>Laporan Omzet</h2>
    </div>




    {{-- Filter --}}
    <form method="GET" class="filter-form">
        <div class="form-group">
            <label>Tanggal Mulai:</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}">
        </div>
        <div class="form-group">
            <label>Tanggal Selesai:</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}">
        </div>
        <div class="form-group">
            <label>Cari Barang (Kode/Nama):</label>
            <input type="text" name="search" placeholder="Cari kode atau nama barang..." value="{{ request('search') }}">
        </div>
        <div class="form-group">
            <label>Lokasi:</label>
            <select name="lokasi">
                <option value="">-- Semua Lokasi --</option>
                @foreach($lokasis as $lokasi)
                    <option value="{{ $lokasi->id }}" {{ request('lokasi') == $lokasi->id ? 'selected' : '' }}>
                        {{ $lokasi->nama_lokasi }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Kondisi:</label>
            <select name="kondisi">
                <option value="">-- Semua Kondisi --</option>
                @foreach($kondisis as $kondisi)
                    <option value="{{ $kondisi->id }}" {{ request('kondisi') == $kondisi->id ? 'selected' : '' }}>
                        {{ $kondisi->nama_kondisi }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="action-group">
            <button type="submit">Terapkan Filter</button>
            <a href="{{ route('omzet.index') }}" class="back-button">Reset Filter</a>
        </div>
    </form>




    {{-- Export Buttons --}}
    <div class="export-buttons">
        <a href="{{ route('export.omzet.pdf', request()->query()) }}" target="_blank">📄 Cetak PDF</a>
        <a href="{{ route('export.omzet.excel', request()->query()) }}">📊 Export Excel</a>
    </div>




    {{-- Table --}}
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    @php
                        function sortUrlBlade($field) {
                            return route('omzet.index', array_merge(request()->all(), [
                                'sort_by' => $field,
                                'sort_dir' => (request('sort_by') === $field && request('sort_dir') === 'asc') ? 'desc' : 'asc'
                            ]));
                        }
                    @endphp
                    <th>No</th>
                    <th><a href="{{ sortUrlBlade('nama_barang') }}">Nama Barang {!! request('sort_by') === 'nama_barang' ? (request('sort_dir') === 'asc' ? '↑' : '↓') : '▲▼' !!}</a></th>
                    <th><a href="{{ sortUrlBlade('tanggal') }}">Tanggal {!! request('sort_by') === 'tanggal' ? (request('sort_dir') === 'asc' ? '↑' : '↓') : '▲▼' !!}</a></th>
                    <th><a href="{{ sortUrlBlade('lokasi') }}">Lokasi {!! request('sort_by') === 'lokasi' ? (request('sort_dir') === 'asc' ? '↑' : '↓') : '▲▼' !!}</a></th>
                    <th><a href="{{ sortUrlBlade('kondisi') }}">Kondisi {!! request('sort_by') === 'kondisi' ? (request('sort_dir') === 'asc' ? '↑' : '↓') : '▲▼' !!}</a></th>
                    <th><a href="{{ sortUrlBlade('jumlah_keluar') }}">Jumlah Keluar {!! request('sort_by') === 'jumlah_keluar' ? (request('sort_dir') === 'asc' ? '↑' : '↓') : '▲▼' !!}</a></th>
                    <th><a href="{{ sortUrlBlade('harga_jual') }}">Harga Jual {!! request('sort_by') === 'harga_jual' ? (request('sort_dir') === 'asc' ? '↑' : '↓') : '▲▼' !!}</a></th>
                    <th><a href="{{ sortUrlBlade('omzet_item') }}">Omzet {!! request('sort_by') === 'omzet_item' ? (request('sort_dir') === 'asc' ? '↑' : '↓') : '▲▼' !!}</a></th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $index => $item)
                    <tr>
                        {{-- nomor urut mengikuti halaman jika paginator --}}
                        <td>
                            {{ ($data instanceof \Illuminate\Pagination\LengthAwarePaginator)
                                ? (($data->firstItem() ?? 0) + $index)
                                : ($loop->iteration) }}
                        </td>
                        <td>{{ $item['nama_barang'] }}</td>
                        <td>{{ \Carbon\Carbon::parse($item['tanggal'])->format('j/n/Y H:i:s') }}</td>
                        <td>{{ $item['lokasi'] }}</td>
                        <td>{{ $item['kondisi'] }}</td>
                        <td>{{ $item['jumlah_keluar'] }}</td>
                        <td>Rp {{ number_format($item['harga_jual'], 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($item['omzet_item'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="7">Total Omzet</td>
                    <td>Rp {{ number_format($total_omzet, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>




    {{-- Pagination + summary (selalu tampil) --}}
    @php
        $isPaginator = $data instanceof \Illuminate\Pagination\LengthAwarePaginator;
        $total = $isPaginator ? $data->total() : (is_countable($data) ? count($data) : collect($data)->count());
        $first = $isPaginator ? ($data->firstItem() ?? ($total ? 1 : 0)) : ($total ? 1 : 0);
        $last  = $isPaginator ? ($data->lastItem()  ?? $total) : $total;
    @endphp
    <div style="margin-top:20px; text-align:center;">
        <div class="small text-muted">
            Showing {{ $first }} to {{ $last }} of {{ $total }} results
        </div>
        <div class="ms-auto">
            @if($isPaginator)
                {!! $data->appends(request()->query())->links('pagination::bootstrap-5') !!}
            @endif
        </div>
    </div>
</div>
@endsection




@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("input[type=date]", {
            dateFormat: "d/m/Y",
            locale: "id"
        });
    });
</script>
@endpush
