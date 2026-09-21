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
        
        /* PERBAIKAN: Khusus baris total di HP */         
        .total-row td[colspan] { display: none; }         
        .total-row td:last-child { 
            padding: 15px !important; 
            text-align: center !important; 
            font-size: 1.3rem !important; 
            border-top: none !important;
        }         
        .total-row td:last-child:before { 
            content: "TOTAL OMZET KESELURUHAN" !important; 
            position: static; 
            display: block; 
            width: 100%; /* Memperbaiki isu terpotong/setengah */
            margin-bottom: 10px; 
            color: #1e293b; 
            font-size: 1rem;
            text-align: center;
            border-bottom: 2px dashed #cbd5e1;
            padding-bottom: 10px;
        }     
    } 
</style> 

<div class="container-laporan mb-5">     
    <div class="header">         
        <h4 class="fw-bold" style="color: #1e293b;">Laporan &rsaquo; Omzet Penjualan</h4>     
    </div>     
    
    {{-- Filter versi desktop --}}     
    <form method="GET" class="filter-form filter-form-inline">         
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

    {{-- Tombol pemicu filter versi HP --}}
    <button type="button" class="filter-trigger-btn" data-bs-toggle="offcanvas" data-bs-target="#filterOmzet">
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M11.5 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3ZM9.05 3a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0V3h9.05Zm-4.9 5a2.5 2.5 0 0 1 4.9 0H16v1H9.05a2.5 2.5 0 0 1-4.9 0H0V8h4.15Zm.9.5a1.5 1.5 0 1 0 3 0 1.5 1.5 0 0 0-3 0ZM.5 12a2.5 2.5 0 0 1 4.9 0H16v1H5.4a2.5 2.5 0 0 1-4.9 0H0v-1h.5Z"/></svg> Filter
    </button>

    {{-- Panel filter bottom-sheet (khusus HP) --}}
    <div class="offcanvas offcanvas-bottom offcanvas-filter" tabindex="-1" id="filterOmzet" style="height:auto; max-height:85vh; border-radius:20px 20px 0 0;">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Filter Omzet Penjualan</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <form method="GET" class="d-flex flex-column">
            <div class="offcanvas-body">
                <label>Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                <label>Tanggal Selesai</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                <label>Cari Barang</label>
                <input type="text" name="search" class="form-control" placeholder="Ketik kode atau nama..." value="{{ request('search') }}">
                <label>Lokasi</label>
                <select name="lokasi" class="form-select">
                    <option value="">-- Semua Lokasi --</option>
                    @foreach($lokasis as $lokasi)
                        <option value="{{ $lokasi->id }}" {{ request('lokasi') == $lokasi->id ? 'selected' : '' }}>{{ $lokasi->nama_lokasi }}</option>
                    @endforeach
                </select>
                <label>Kondisi</label>
                <select name="kondisi" class="form-select">
                    <option value="">-- Semua Kondisi --</option>
                    @foreach($kondisis as $kondisi)
                        <option value="{{ $kondisi->id }}" {{ request('kondisi') == $kondisi->id ? 'selected' : '' }}>{{ $kondisi->nama_kondisi }}</option>
                    @endforeach
                </select>
            </div>
            <div class="offcanvas-footer">
                <a href="{{ route('omzet.index') }}" class="btn btn-outline-dark fw-bold flex-fill">Reset</a>
                <button type="submit" class="btn btn-orange fw-bold flex-fill">Terapkan</button>
            </div>
        </form>
    </div>
    
    {{-- Tombol Ekspor ringkas: 1 tombol, terbuka jadi 2 pilihan --}}     
    <div class="dropdown export-split mb-4">
        <button class="btn btn-outline-dark fw-bold shadow-sm px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="me-1" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5Z"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3Z"/></svg> Ekspor
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('export.omzet.pdf', request()->query()) }}" target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ef4444" class="me-2" viewBox="0 0 16 16"><path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5Zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2Z"/></svg> Sebagai PDF
            </a></li>
            <li><a class="dropdown-item" href="{{ route('export.omzet.excel', request()->query()) }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#16a34a" class="me-2" viewBox="0 0 16 16"><path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5Zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2Z"/></svg> Sebagai Excel
            </a></li>
        </ul>
    </div>     

    {{-- Ringkasan Total Omzet (Dipindah ke atas, gaya dipertahankan) --}}
    @if(count($data) > 0)
    <div class="table-wrapper mb-4" style="border-radius: 12px; overflow: hidden;">
        <table>
            <tbody>
                <tr class="total-row">
                    <td colspan="7" class="text-end pe-4 text-uppercase" style="width: 80%;">Total Omzet Keseluruhan</td>
                    <td class="text-success fs-5 fw-bold" style="width: 20%;">Rp {{ number_format($total_omzet, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
    @endif

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