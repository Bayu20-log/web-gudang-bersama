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
    table { width: 100%; border-collapse: collapse; min-width: 800px; }     
    th, td { padding: 16px 20px; text-align: center; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }     
    th { background-color: #1e293b !important; color: #ffffff !important; font-weight: 600; white-space: nowrap; border-bottom: 4px solid #f97316; letter-spacing: 0.5px; }     
    th a { color: #ffffff; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; }     
    th a:hover { color: #f97316; }     
    tr:hover { background-color: #f8fafc; }
</style> 

<div class="container-laporan mb-5">     
    <div class="header">         
        <h4 class="fw-bold" style="color: #1e293b;">Total Aset</h4>     
    </div>     
    
    {{-- Search bar + tombol filter --}}     
    <div class="search-filter-bar">
        <form method="GET" class="search-bar-form">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>
            <input type="text" name="nama_barang" placeholder="Cari nama barang..." value="{{ request('nama_barang') }}" onchange="this.form.submit()">
            @if(request('start_date'))<input type="hidden" name="start_date" value="{{ request('start_date') }}">@endif
            @if(request('end_date'))<input type="hidden" name="end_date" value="{{ request('end_date') }}">@endif
            @if(request('lokasi'))<input type="hidden" name="lokasi" value="{{ request('lokasi') }}">@endif
            @if(request('kondisi'))<input type="hidden" name="kondisi" value="{{ request('kondisi') }}">@endif
        </form>
        <button type="button" class="filter-icon-btn" data-bs-toggle="offcanvas" data-bs-target="#filterAset" title="Filter">
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M11.5 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3ZM9.05 3a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0V3h9.05Zm-4.9 5a2.5 2.5 0 0 1 4.9 0H16v1H9.05a2.5 2.5 0 0 1-4.9 0H0V8h4.15Zm.9.5a1.5 1.5 0 1 0 3 0 1.5 1.5 0 0 0-3 0ZM.5 12a2.5 2.5 0 0 1 4.9 0H16v1H5.4a2.5 2.5 0 0 1-4.9 0H0v-1h.5Z"/></svg>
            @if(request('start_date') || request('end_date') || request('lokasi') || request('kondisi'))<span class="filter-dot"></span>@endif
        </button>
    </div>

    {{-- Panel filter bottom-sheet (khusus HP) --}}
    <div class="offcanvas offcanvas-bottom offcanvas-filter" tabindex="-1" id="filterAset" style="height:auto; max-height:85vh; border-radius:20px 20px 0 0;">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Filter Aset</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <form method="GET" class="d-flex flex-column">
            <div class="offcanvas-body">
                <label>Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                <label>Tanggal Selesai</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                <label>Lokasi</label>
                <select name="lokasi" class="form-select">
                    <option value="">-- Semua Lokasi --</option>
                    @foreach($listLokasi as $lokasi)
                        <option value="{{ $lokasi }}" {{ request('lokasi') == $lokasi ? 'selected' : '' }}>{{ $lokasi }}</option>
                    @endforeach
                </select>
                <label>Kondisi</label>
                <select name="kondisi" class="form-select">
                    <option value="">-- Semua Kondisi --</option>
                    @foreach($listKondisi as $kondisi)
                        <option value="{{ $kondisi }}" {{ request('kondisi') == $kondisi ? 'selected' : '' }}>{{ $kondisi }}</option>
                    @endforeach
                </select>
                <label>Nama Barang</label>
                <input type="text" name="nama_barang" class="form-control" placeholder="Ketik nama barang..." value="{{ request('nama_barang') }}">
            </div>
            <div class="offcanvas-footer">
                <a href="{{ route('aset.index') }}" class="btn btn-outline-dark fw-bold flex-fill">Reset</a>
                <button type="submit" class="btn btn-orange fw-bold flex-fill">Terapkan</button>
            </div>
        </form>
    </div>

    @php         
        $query = request()->except(['page']);         
        if (request('start_date')) $query['tanggal_mulai'] = request('start_date');         
        if (request('end_date')) $query['tanggal_selesai'] = request('end_date');     
    @endphp          
    
    {{-- Tombol Ekspor ringkas: 1 tombol, terbuka jadi 2 pilihan --}}     
    <div class="dropdown export-split mb-4">
        <button class="btn btn-outline-dark fw-bold shadow-sm px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="me-1" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5Z"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3Z"/></svg> Ekspor
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('export.aset.pdf', $query) }}" target="_blank">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ef4444" class="me-2" viewBox="0 0 16 16"><path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5Zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2Z"/></svg> Sebagai PDF
            </a></li>
            <li><a class="dropdown-item" href="{{ route('export.aset.excel', $query) }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#16a34a" class="me-2" viewBox="0 0 16 16"><path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5Zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2Z"/></svg> Sebagai Excel
            </a></li>
        </ul>
    </div>     

    {{-- Ringkasan Total Aset --}}
    @if($grouped->count() > 0)
    <div class="report-summary-card mb-4">
        <div class="rsc-label">Total Keseluruhan Aset</div>
        <div class="rsc-value">Rp {{ number_format($totalAset, 0, ',', '.') }}</div>
    </div>
    @endif

    {{-- ================= TAMPILAN DESKTOP (tabel) ================= --}}
    <div class="table-wrapper desktop-table-wrapper">         
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
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="ms-1" viewBox="0 0 16 16">@if(request('sort_dir') === 'asc')<path d="M7.247 4.86l-4.796 5.481c-.566.647-.106 1.659.753 1.659h9.592a1 1 0 0 0 .753-1.659l-4.796-5.48a1 1 0 0 0-1.506 0z"/>@else<path d="M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>@endif</svg>
                                @else                                     
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="currentColor" class="ms-1" style="opacity:0.5" viewBox="0 0 16 16"><path d="M3.5 12.5a.5.5 0 0 1-.5-.5V3.707L1.354 5.354a.5.5 0 1 1-.708-.708l2.5-2.5a.5.5 0 0 1 .708 0l2.5 2.5a.5.5 0 1 1-.708.708L4 3.707V12a.5.5 0 0 1-.5.5Zm9-9a.5.5 0 0 1 .5.5v8.293l1.646-1.647a.5.5 0 0 1 .708.708l-2.5 2.5a.5.5 0 0 1-.708 0l-2.5-2.5a.5.5 0 1 1 .708-.708L12 12.293V4a.5.5 0 0 1 .5-.5Z"/></svg>
                                @endif                             
                            </a>                         
                        </th>                     
                    @endforeach                 
                </tr>             
            </thead>             
            <tbody>                 
                @forelse($grouped as $index => $row)                     
                    <tr>                         
                        <td class="fw-medium text-secondary">{{ ($grouped->firstItem() ?? 0) + $index }}</td>                         
                        <td class="fw-bold text-dark">{{ $row['nama_barang'] }}</td>                         
                        <td>{{ $row['lokasi'] }}</td>                         
                        <td>{{ $row['kondisi'] }}</td>                         
                        <td><span class="badge px-3 py-2 rounded-pill fs-6" style="background-color: #22c55e; color: #ffffff;">{{ $row['stok_akhir'] }}</span></td>                         
                        <td>Rp {{ number_format($row['harga_beli'], 0, ',', '.') }}</td>                         
                        <td class="fw-bold text-dark">Rp {{ number_format($row['jumlah_aset'], 0, ',', '.') }}</td>                     
                    </tr>                 
                @empty                     
                    <tr><td colspan="7" class="text-center py-5 text-muted fw-medium"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="mb-2" style="color: #cbd5e1;" viewBox="0 0 16 16"><path d="M.54 3.87.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a1.99 1.99 0 0 1 .342-1.31zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4H2.19zm4.69-1.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139C1.72 3.042 1.95 3 2.19 3h5.396l-.707-.707z"/></svg><br>Data laporan aset belum tersedia.</td></tr>                 
                @endforelse                                  
            </tbody>         
        </table>     
    </div>

    {{-- ================= TAMPILAN HP (kartu) ================= --}}
    <div class="mobile-card-list">
        @forelse($grouped as $row)
            <div class="mobile-card-item">
                <div>
                    <div class="mc-title">{{ $row['nama_barang'] }}</div>
                    <div class="mc-sub">{{ $row['lokasi'] }} &middot; {{ $row['kondisi'] }}</div>
                    <div class="mc-sub">Harga Beli Rp {{ number_format($row['harga_beli'], 0, ',', '.') }}</div>
                </div>
                <div class="text-end">
                    <span class="badge px-3 py-2 rounded-pill fs-6 mb-1 d-inline-block" style="background-color: #22c55e; color: #ffffff;">{{ $row['stok_akhir'] }}</span>
                    <div class="fw-bold" style="color:#1e293b;">Rp {{ number_format($row['jumlah_aset'], 0, ',', '.') }}</div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted fw-medium">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="mb-2" style="color: #cbd5e1;" viewBox="0 0 16 16"><path d="M.54 3.87.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a1.99 1.99 0 0 1 .342-1.31zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4H2.19zm4.69-1.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139C1.72 3.042 1.95 3 2.19 3h5.396l-.707-.707z"/></svg><br>
                Data laporan aset belum tersedia.
            </div>
        @endforelse
    </div>     
    
    @if ($grouped instanceof \Illuminate\Pagination\LengthAwarePaginator)         
        <div class="mt-4 d-flex flex-column flex-md-row justify-content-between align-items-center">             
            <div class="small text-muted mb-2 mb-md-0 fw-medium">                 
                Menampilkan {{ $grouped->firstItem() ?? ($grouped->total() ? 1 : 0) }}                  
                - {{ $grouped->lastItem() ?? $grouped->total() }}                  
                dari {{ $grouped->total() }} data             
            </div>             
            <div>                 
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
            flatpickr("input[name='start_date']", { dateFormat: "Y-m-d" });             
            flatpickr("input[name='end_date']", { dateFormat: "Y-m-d" });         
        });     
    </script> 
@endsection