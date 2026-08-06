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
        min-width: 800px;
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
        <h2>Laporan Total Aset</h2>
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
            <label>Lokasi:</label>
            <select name="lokasi">
                <option value="">-- Semua Lokasi --</option>
                @foreach($listLokasi as $lokasi)
                    <option value="{{ $lokasi }}" {{ request('lokasi') == $lokasi ? 'selected' : '' }}>
                        {{ $lokasi }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Kondisi:</label>
            <select name="kondisi">
                <option value="">-- Semua Kondisi --</option>
                @foreach($listKondisi as $kondisi)
                    <option value="{{ $kondisi }}" {{ request('kondisi') == $kondisi ? 'selected' : '' }}>
                        {{ $kondisi }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label>Nama Barang:</label>
            <input type="text" name="nama_barang" placeholder="Nama Barang..." value="{{ request('nama_barang') }}">
        </div>
        <div class="action-group">
            <button type="submit">Terapkan Filter</button>
            <a href="{{ route('aset.index') }}" class="back-button">Reset Filter</a>
        </div>
    </form>




    {{-- Tombol Ekspor --}}
    @php
        $query = request()->except(['page']);
        if (request('start_date')) $query['tanggal_mulai'] = request('start_date');
        if (request('end_date')) $query['tanggal_selesai'] = request('end_date');
    @endphp


    <div class="export-buttons">
        <a href="{{ route('export.aset.pdf', $query) }}" target="_blank">📄 Cetak PDF</a>
        <a href="{{ route('export.aset.excel', $query) }}">📊 Export Excel</a>
    </div>






    {{-- Tabel --}}
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    @php
                        $sortBy = request('sort_by');
                        $columns = [
                            'nama_barang' => 'Nama Barang',
                            'lokasi'      => 'Lokasi',
                            'kondisi'     => 'Kondisi',
                            'stok_akhir'  => 'Stok Akhir',
                            'harga_beli'  => 'Harga Beli',
                            'jumlah_aset' => 'Jumlah Aset',
                        ];
                    @endphp
                    <th>No</th>
                    @foreach($columns as $key => $label)
                        <th>
                            <a href="{{ route('aset.index', array_merge(request()->all(), ['sort_by' => $key, 'sort_dir' => ($sortBy === $key && request('sort_dir') === 'asc') ? 'desc' : 'asc'])) }}">
                                {{ $label }}
                                @if($sortBy === $key)
                                    {{ request('sort_dir') === 'asc' ? '↑' : '↓' }}
                                @else
                                    ▲▼
                                @endif
                            </a>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($grouped as $index => $row)
                    <tr>
                        {{-- nomor urut mengikuti halaman --}}
                        <td>{{ ($grouped->firstItem() ?? 0) + $index }}</td>
                        <td>{{ $row['nama_barang'] }}</td>
                        <td>{{ $row['lokasi'] }}</td>
                        <td>{{ $row['kondisi'] }}</td>
                        <td>{{ $row['stok_akhir'] }}</td>
                        <td>Rp {{ number_format($row['harga_beli'], 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($row['jumlah_aset'], 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="6" class="text-center">Total Aset</td>
                    <td>Rp {{ number_format($totalAset, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>




    {{-- Pagination + summary (selalu tampil meski 1 halaman) --}}
    @if ($grouped instanceof \Illuminate\Pagination\LengthAwarePaginator)
        <div style="margin-top:20px; text-align:center;">
            <div class="small text-muted">
                Showing {{ $grouped->firstItem() ?? ($grouped->total() ? 1 : 0) }}
                to {{ $grouped->lastItem() ?? $grouped->total() }}
                of {{ $grouped->total() }} results
            </div>
            <div class="ms-auto">
                {!! $grouped->appends(request()->query())->links('pagination::bootstrap-5') !!}
            </div>
        </div>
    @endif
</div>
 
@endsection




@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            flatpickr("input[name='start_date']", {
                dateFormat: "d/m/Y",
            });
            flatpickr("input[name='end_date']", {
                dateFormat: "d/m/Y",
            });
        });
    </script>
@endsection
