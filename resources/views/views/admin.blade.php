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


    /* Export Buttons */
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
    }
    th a { color: inherit; text-decoration: none; }
    th a:hover { text-decoration: underline; }
    tr { box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.05); border-radius: 8px; }


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
        <h2>Laporan Stok Barang</h2>
    </div>

    {{-- Filter Nama / Kode Barang --}}
    <form method="GET" class="filter-form">
        <div class="form-group" style="flex-grow:1;">
            <label>Cari Barang:</label>
            <input type="text" name="search" placeholder="Cari Kode / Nama Barang / username" value="{{ request('search') }}">
        </div>
        <div class="form-group">
            <button type="submit">Terapkan Filter</button>
        </div>
        <div class="form-group">
            <a href="{{ route('laporan.stok.admin') }}" class="back-button">Reset Filter</a>
        </div>
    </form>

    {{-- Tabel Stok Viewer --}}
    <div class="table-responsive">
    <table>
            <thead>
        @php
            $columns = [
                'kode_barang' => 'Kode Barang',
                'nama_barang' => 'Nama Barang',
                'stok_akhir'  => 'Stok Barang',
                'username'    => 'Username',
            ];
        @endphp
        <tr>
            @foreach($columns as $key => $label)
                <th>
                    <a href="{{ route('laporan.stok.admin', array_merge(request()->all(), [
                        'sort_by' => $key,
                        'sort_dir' => ($sortBy === $key && request('sort_dir') === 'asc') ? 'desc' : 'asc'
                    ])) }}">
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
            @forelse($data as $item)
                <tr>
                    <td>{{ $item->kode_barang }}</td>
                    <td>{{ $item->nama_barang }}</td>
                    <td>{{ $item->stok_akhir }}</td>
                    <td>{{ $item->username ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data tersedia.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    </div>
    <div class="d-flex justify-content-center mt-4">
        {!! $data->appends(request()->query())->links() !!}
    </div>
</div>
@endsection
