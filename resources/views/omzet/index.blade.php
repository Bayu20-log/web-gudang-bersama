@extends('layouts.app')
@section('content')

<style>
    body { padding-top: 40px; }
    .btn-orange { background-color: #f97316; color: #fff; border: none; transition: 0.3s; }
    .btn-orange:hover { background-color: #ea580c; color: #fff; }
    .btn-outline-dark { border: 1px solid #1e293b; color: #1e293b; transition: 0.3s; background: transparent; font-weight: 600;}
    .btn-outline-dark:hover { background-color: #1e293b; color: #fff; }
    
    .container-laporan { max-width: 1200px; margin: auto; padding: 30px 20px; font-family: 'Segoe UI', sans-serif; }
    .header { margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px; }

    /* TEMA FILTER */
    form.filter-form { background-color: #ffffff; border: none; border-radius: 12px; padding: 20px 25px; margin-bottom: 25px; display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .filter-form .form-group { display: flex; flex-direction: column; min-width: 180px; flex-grow: 1; }
    .filter-form label { font-size: 14px; font-weight: 600; margin-bottom: 8px; color: #374151;}
    .filter-form input, .filter-form select { padding: 10px 15px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background-color: #f8fafc; transition: 0.3s; }
    .filter-form input:focus, .filter-form select:focus { border-color: #f97316; outline: none; box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1); }
    
    /* TEMA TABEL */
    .table-wrapper { width: 100%; overflow-x: auto; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); background: white; }
    table { width: 100%; border-collapse: collapse; min-width: 900px; }
    th, td { padding: 16px 20px; text-align: center; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    th { background-color: #1e293b !important; color: #ffffff !important; font-weight: 600; white-space: nowrap; border-bottom: 4px solid #f97316; letter-spacing: 0.5px; }
    th a { color: #ffffff; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; }
    th a:hover { color: #f97316; }
    tr:hover { background-color: #f8fafc; }
    
    /* TEMA TOTAL ROW */
    .total-row { background-color: #f1f5f9 !important; border-top: 2px solid #cbd5e1; }
    .total-row td { font-weight: bold; color: #1e293b; font-size: 1.05rem;}

    @media(max-width: 768px) {
        .header { flex-direction: column; align-items: flex-start; }
        .filter-form { flex-direction: column; }
        .filter-form .form-group, .btn-action-group { width: 100%; }
        .btn-action-group { display: flex; gap: 10px; }
        .btn-action-group button, .btn-action-group a { flex: 1; text-align: center; justify-content: center; }
        
        table { min-width: 100%; }
        table, thead, tbody, th, td, tr { display: block; width: 100%; }
        thead { display: none; }
        tr { margin-bottom: 15px; border: 1px solid #e2e8f0; border-radius: 12px; padding: 15px; background-color: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
        td { border: none !important; text-align: left; padding: 8px 0 8px 45%; position: relative; }
        td:before { position: absolute; top: 8px; left: 0; width: 40%; white-space: nowrap; font-weight: 600; color: #4b5563; }
        
        td:nth-of-type(1):before { content: "No"; }
        td:nth-of-type(2):before { content: "Nama Barang"; }
        td:nth-of-type(3):before { content: "Tanggal"; }
        td:nth-of-type(4):before { content: "Lokasi"; }
        td:nth-of-type(5):before { content: "Kondisi"; }
        td:nth-of-type(6):before { content: "Jumlah Keluar"; }
        td:nth-of-type(7):before { content: "Harga Jual"; }
        td:nth-of-type(8):before { content: "Omzet"; }

        /* Khusus baris total di HP */
        .total-row td[colspan] { display: none; }
        .total-row td:last-child { padding: 15px 0 !important; text-align: right; font-size: 1.2rem; color: #ea580c; border-top: 2px dashed #cbd5e1 !important;}
        .total-row td:last-child:before { content: "TOTAL OMZET:" !important; position: static; display: block; margin-bottom: 5px; color: #1e293b; font-size: 1rem;}
    }
</style>

<div class="container-laporan mb-5">
    <div class="header">
        <h4 class="fw-bold" style="color: #1e293b;">Laporan &rsaquo; Omzet Penjualan</h4>
    </div>

    {{-- Filter --}}
    <form method="GET" class="filter-form">
        <div class="form-group">
            <label>Tanggal Mulai</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}">
        </div>
        <div class="form-group">
            <label>Tanggal Selesai</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}">
        </div>
        <div class="form-group">
            <label>Cari Barang</label>
            <input type="text" name="search" placeholder="Ketik kode atau nama..." value="{{ request('search') }}">
        </div>
        <div class="form-group">
            <label>Lokasi</label>
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
            <label>Kondisi</label>
            <select name="kondisi">
                <option value="">-- Semua Kondisi --</option>
                @foreach($kondisis as $kondisi)
                    <option value="{{ $kondisi->id }}" {{ request('kondisi') == $kondisi->id ? 'selected' : '' }}>
                        {{ $kondisi->nama_kondisi }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="btn-action-group" style="display: flex; gap: 10px;">
            <button type="submit" class="btn btn-orange fw-bold px-4">Filter</button>
            <a href="{{ route('omzet.index') }}" class="btn btn-outline-dark fw-bold px-4" style="display: inline-flex; align-items: center; justify-content: center;">Reset</a>
        </div>
    </form>

    {{-- Export Buttons --}}
    <div class="d-flex flex-wrap gap-2 mb-3">
        <a href="{{ route('export.omzet.pdf', request()->query()) }}" target="_blank" class="btn btn-outline-danger fw-bold shadow-sm">
            <i class="fa-solid fa-file-pdf me-1"></i> Cetak PDF
        </a>
        <a href="{{ route('export.omzet.excel', request()->query()) }}" class="btn btn-outline-success fw-bold shadow-sm">
            <i class="fa-solid fa-file-excel me-1"></i> Export Excel
        </a>
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
                    <th><a href="{{ sortUrlBlade('nama_barang') }}">Nama Barang @if(request('sort_by') === 'nama_barang')<i class="fa-solid fa-sort-{{ request('sort_dir') === 'asc' ? 'up' : 'down' }} ms-1"></i>@else<i class="fa-solid fa-sort text-muted ms-1" style="opacity: 0.5;"></i>@endif</a></th>
                    <th><a href="{{ sortUrlBlade('tanggal') }}">Tanggal @if(request('sort_by') === 'tanggal')<i class="fa-solid fa-sort-{{ request('sort_dir') === 'asc' ? 'up' : 'down' }} ms-1"></i>@else<i class="fa-solid fa-sort text-muted ms-1" style="opacity: 0.5;"></i>@endif</a></th>
                    <th><a href="{{ sortUrlBlade('lokasi') }}">Lokasi @if(request('sort_by') === 'lokasi')<i class="fa-solid fa-sort-{{ request('sort_dir') === 'asc' ? 'up' : 'down' }} ms-1"></i>@else<i class="fa-solid fa-sort text-muted ms-1" style="opacity: 0.5;"></i>@endif</a></th>
                    <th><a href="{{ sortUrlBlade('kondisi') }}">Kondisi @if(request('sort_by') === 'kondisi')<i class="fa-solid fa-sort-{{ request('sort_dir') === 'asc' ? 'up' : 'down' }} ms-1"></i>@else<i class="fa-solid fa-sort text-muted ms-1" style="opacity: 0.5;"></i>@endif</a></th>
                    <th><a href="{{ sortUrlBlade('jumlah_keluar') }}">Jml Keluar @if(request('sort_by') === 'jumlah_keluar')<i class="fa-solid fa-sort-{{ request('sort_dir') === 'asc' ? 'up' : 'down' }} ms-1"></i>@else<i class="fa-solid fa-sort text-muted ms-1" style="opacity: 0.5;"></i>@endif</a></th>
                    <th><a href="{{ sortUrlBlade('harga_jual') }}">Harga Jual @if(request('sort_by') === 'harga_jual')<i class="fa-solid fa-sort-{{ request('sort_dir') === 'asc' ? 'up' : 'down' }} ms-1"></i>@else<i class="fa-solid fa-sort text-muted ms-1" style="opacity: 0.5;"></i>@endif</a></th>
                    <th><a href="{{ sortUrlBlade('omzet_item') }}">Omzet @if(request('sort_by') === 'omzet_item')<i class="fa-solid fa-sort-{{ request('sort_dir') === 'asc' ? 'up' : 'down' }} ms-1"></i>@else<i class="fa-solid fa-sort text-muted ms-1" style="opacity: 0.5;"></i>@endif</a></th>
                </tr>
            </thead>
            <tbody>
                @forelse($data as $index => $item)
                    <tr>
                        <td class="fw-medium text-secondary">
                            {{ ($data instanceof \Illuminate\Pagination\LengthAwarePaginator) ? (($data->firstItem() ?? 0) + $index) : ($loop->iteration) }}
                        </td>
                        <td class="fw-bold text-dark">{{ $item['nama_barang'] }}</td>
                        <td>{{ \Carbon\Carbon::parse($item['tanggal'])->format('d M Y, H:i') }}</td>
                        <td>{{ $item['lokasi'] }}</td>
                        <td>{{ $item['kondisi'] }}</td>
                        <td class="text-danger fw-bold">{{ $item['jumlah_keluar'] }}</td>
                        <td>Rp {{ number_format($item['harga_jual'], 0, ',', '.') }}</td>
                        <td class="fw-bold text-success">Rp {{ number_format($item['omzet_item'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-5 text-muted fw-medium"><i class="fa-solid fa-folder-open fs-2 mb-2 d-block text-light"></i>Data laporan omzet belum tersedia.</td></tr>
                @endforelse
                
                @if(count($data) > 0)
                <tr class="total-row">
                    <td colspan="7" class="text-end pe-4 text-uppercase">Total Omzet Keseluruhan</td>
                    <td class="text-success fs-5">Rp {{ number_format($total_omzet, 0, ',', '.') }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    @php
        $isPaginator = $data instanceof \Illuminate\Pagination\LengthAwarePaginator;
        $total = $isPaginator ? $data->total() : (is_countable($data) ? count($data) : collect($data)->count());
        $first = $isPaginator ? ($data->firstItem() ?? ($total ? 1 : 0)) : ($total ? 1 : 0);
        $last  = $isPaginator ? ($data->lastItem()  ?? $total) : $total;
    @endphp

    <div class="mt-4 d-flex flex-column flex-md-row justify-content-between align-items-center">
        <div class="small text-muted mb-2 mb-md-0 fw-medium">
            Menampilkan {{ $first }} - {{ $last }} dari {{ $total }} data
        </div>
        <div>
            @if($isPaginator)
                {!! $data->appends(request()->query())->links('pagination::bootstrap-5') !!}
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        flatpickr("input[name='start_date']", { dateFormat: "Y-m-d" });
        flatpickr("input[name='end_date']", { dateFormat: "Y-m-d" });
    });
</script>
@endpush