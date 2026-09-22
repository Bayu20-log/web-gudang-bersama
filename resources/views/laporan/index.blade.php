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
    .filter-form .form-group { display: flex; flex-direction: column; min-width: 200px; flex-grow: 1; }
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
    .bg-stok-akhir { 
        background-color: #22c55e !important;
        color: #ffffff !important; 
    }
</style>

@php $role = auth()->user()->role; @endphp

<div class="container-laporan mb-5">
    <div class="header">
        <h4 class="fw-bold mb-0" style="color: #1e293b;">Stok &amp; Arus Barang</h4>
        @if ($role === 'gudang')
            <div class="report-toggle">
                <a href="{{ route('laporan') }}" class="report-toggle-btn active">Stok Barang</a>
                <a href="{{ route('laporan.arus') }}" class="report-toggle-btn">Arus Barang</a>
            </div>
        @endif
    </div>

    {{-- Search bar + tombol filter --}}
    <div class="search-filter-bar">
        <form method="GET" class="search-bar-form">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/></svg>
            <input type="text" name="nama_barang" placeholder="Cari nama barang..." value="{{ request('nama_barang') }}" onchange="this.form.submit()">
            @if(request('kode_barang'))<input type="hidden" name="kode_barang" value="{{ request('kode_barang') }}">@endif
            @if(request('lokasi'))<input type="hidden" name="lokasi" value="{{ request('lokasi') }}">@endif
        </form>
        <button type="button" class="filter-icon-btn" data-bs-toggle="offcanvas" data-bs-target="#filterLaporanStok" title="Filter">
            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M11.5 2a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3ZM9.05 3a2.5 2.5 0 0 1 4.9 0H16v1h-2.05a2.5 2.5 0 0 1-4.9 0H0V3h9.05Zm-4.9 5a2.5 2.5 0 0 1 4.9 0H16v1H9.05a2.5 2.5 0 0 1-4.9 0H0V8h4.15Zm.9.5a1.5 1.5 0 1 0 3 0 1.5 1.5 0 0 0-3 0ZM.5 12a2.5 2.5 0 0 1 4.9 0H16v1H5.4a2.5 2.5 0 0 1-4.9 0H0v-1h.5Z"/></svg>
            @if(request('kode_barang') || request('lokasi'))<span class="filter-dot"></span>@endif
        </button>
    </div>

    {{-- Panel filter bottom-sheet (khusus HP) --}}
    <div class="offcanvas offcanvas-bottom offcanvas-filter" tabindex="-1" id="filterLaporanStok" style="height:auto; max-height:80vh; border-radius:20px 20px 0 0;">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Filter Laporan Stok</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <form method="GET" class="d-flex flex-column">
            <div class="offcanvas-body">
                <label>Nama Barang</label>
                <input type="text" name="nama_barang" class="form-control" placeholder="Ketik nama barang..." value="{{ request('nama_barang') }}">
                <label>Kode Barang</label>
                <input type="text" name="kode_barang" class="form-control" placeholder="Ketik kode..." value="{{ request('kode_barang') }}">
                <label>Lokasi</label>
                <select name="lokasi" class="form-select">
                    <option value="">-- Semua Lokasi --</option>
                    @foreach($lokasis as $lokasi)
                        @php
                            $lokasiId   = is_array($lokasi) ? ($lokasi['id'] ?? $lokasi['nama_lokasi']) : $lokasi;
                            $lokasiName = is_array($lokasi) ? ($lokasi['nama_lokasi'] ?? '-') : $lokasi;
                        @endphp
                        <option value="{{ $lokasiId }}" {{ request('lokasi') == $lokasiId ? 'selected' : '' }}>
                            {{ $lokasiName }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="offcanvas-footer">
                <a href="{{ route('laporan') }}" class="btn btn-outline-dark fw-bold flex-fill">Reset</a>
                <button type="submit" class="btn btn-orange fw-bold flex-fill">Terapkan</button>
            </div>
        </form>
    </div>

    {{-- Ringkasan jumlah barang + tombol Ekspor --}}
    <div class="report-count-row">
        <div class="rc-text">
            <strong>{{ $totalBarang }}</strong> barang
            @if($perluTindakan > 0)
                &middot; <span class="rc-warn">{{ $perluTindakan }} perlu tindakan</span>
            @endif
        </div>
        @if ($role === 'gudang')
            <div class="dropdown export-split">
                <button class="btn btn-outline-dark fw-bold shadow-sm px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="currentColor" class="me-1" viewBox="0 0 16 16"><path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5Z"/><path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3Z"/></svg> Ekspor
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('laporan.pdf', request()->query()) }}" target="_blank">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#ef4444" class="me-2" viewBox="0 0 16 16"><path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5Zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2Z"/></svg> Sebagai PDF
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('laporan.excel', request()->query()) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="#16a34a" class="me-2" viewBox="0 0 16 16"><path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5Zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2Z"/></svg> Sebagai Excel
                        </a>
                    </li>
                </ul>
            </div>
        @endif
    </div>

    @php
        $sortBy = request('sort_by');
        $columns = [
            'kode_barang' => 'Kode Barang',
            'nama_barang' => 'Nama Barang',
            'harga_dasar' => 'Harga Dasar',
            'total_masuk' => 'Total Masuk',
            'total_keluar' => 'Total Keluar',
            'stok_akhir' => 'Stok Akhir',
            'lokasi' => 'Lokasi',
            'username' => 'Username',
        ];
    @endphp

    {{-- ================= TAMPILAN DESKTOP (tabel) ================= --}}
    <div class="table-wrapper desktop-table-wrapper">
        <table>
            <thead>
                <tr>
                    @foreach($columns as $key => $label)
                        <th>
                            <a href="{{ route('laporan', array_merge(request()->all(), ['sort_by' => $key, 'sort_dir' => ($sortBy === $key && request('sort_dir') === 'asc') ? 'desc' : 'asc'])) }}">
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
                @forelse ($data as $item)
                    <tr>
                        <td class="fw-medium text-secondary">{{ $item['kode_barang'] }}</td>
                        <td class="fw-bold text-dark">{{ $item['nama_barang'] }}</td>
                        <td>Rp {{ number_format($item['harga_dasar'], 0, ',', '.') }}</td>
                        <td class="text-success fw-bold">+{{ $item['total_masuk'] }}</td>
                        <td class="text-danger fw-bold">-{{ $item['total_keluar'] }}</td>
                        <td><span class="badge bg-stok-akhir px-3 py-2 rounded-pill fs-6">{{ $item['stok_akhir'] }}</span></td>
                        <td>{{ is_array($item['lokasi']) ? ($item['lokasi']['nama_lokasi'] ?? '-') : $item['lokasi'] }}</td>
                        <td>{{ $item['username'] }}</td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-5 text-muted fw-medium"><svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="mb-2" style="color: #cbd5e1;" viewBox="0 0 16 16"><path d="M.54 3.87.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a1.99 1.99 0 0 1 .342-1.31zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4H2.19zm4.69-1.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139C1.72 3.042 1.95 3 2.19 3h5.396l-.707-.707z"/></svg><br>Data laporan stok belum tersedia.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ================= TAMPILAN HP (kartu) ================= --}}
    <div class="mobile-card-list">
        @forelse ($data as $item)
            @php
                $min = $item['stok_minimum'] ?? 0;
                $stokAkhir = $item['stok_akhir'];
                if ($stokAkhir <= 0) { $status = 'HABIS'; $badgeClass = 'habis'; }
                elseif ($min > 0 && $stokAkhir < $min) { $status = 'KRITIS'; $badgeClass = 'kritis'; }
                elseif ($min > 0 && $stokAkhir < $min * 1.5) { $status = 'RENDAH'; $badgeClass = 'rendah'; }
                else { $status = 'AMAN'; $badgeClass = 'aman'; }
                $namaLokasi = is_array($item['lokasi']) ? ($item['lokasi']['nama_lokasi'] ?? '-') : $item['lokasi'];
            @endphp
            <div class="report-card">
                <div class="rcard-head">
                    <span class="rcard-title">{{ $item['nama_barang'] }}</span>
                    <span class="mc-badge {{ $badgeClass }}">{{ $status }}</span>
                </div>
                <div class="rcard-sub">{{ $item['kode_barang'] }} &middot; {{ $namaLokasi }} &middot; Rp {{ number_format($item['harga_dasar'], 0, ',', '.') }}</div>
                <div class="rcard-divider"></div>
                <div class="rcard-stats">
                    <div>
                        <div class="stat-label">Masuk</div>
                        <div class="stat-value text-success">+{{ $item['total_masuk'] }}</div>
                    </div>
                    <div>
                        <div class="stat-label">Keluar</div>
                        <div class="stat-value text-danger">-{{ $item['total_keluar'] }}</div>
                    </div>
                    <div>
                        <div class="stat-label">Stok Akhir</div>
                        <div class="stat-value" style="color:#1e293b;">{{ $stokAkhir }}@if($min > 0)<span class="stat-sub"> / min.{{ $min }}</span>@endif</div>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-5 text-muted fw-medium">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="mb-2" style="color: #cbd5e1;" viewBox="0 0 16 16"><path d="M.54 3.87.5 3a2 2 0 0 1 2-2h3.672a2 2 0 0 1 1.414.586l.828.828A2 2 0 0 0 9.828 3h3.982a2 2 0 0 1 1.992 2.181l-.637 7A2 2 0 0 1 13.174 14H2.826a2 2 0 0 1-1.991-1.819l-.637-7a1.99 1.99 0 0 1 .342-1.31zM2.19 4a1 1 0 0 0-.996 1.09l.637 7a1 1 0 0 0 .995.91h10.348a1 1 0 0 0 .995-.91l.637-7A1 1 0 0 0 13.81 4H2.19zm4.69-1.707A1 1 0 0 0 6.172 2H2.5a1 1 0 0 0-1 .981l.006.139C1.72 3.042 1.95 3 2.19 3h5.396l-.707-.707z"/></svg><br>
                Data laporan stok belum tersedia.
            </div>
        @endforelse
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

    @php $chartItems = $isPaginator ? $data->items() : $data; @endphp
    @if ($total > 0)
        <div class="card border-0 shadow-sm mt-5 p-4 rounded-4 bg-white">
            <h5 class="fw-bold mb-4 text-center" style="color:#1e293b;">Grafik Stok Akhir</h5>
            <canvas id="stokChart" height="100"></canvas>
        </div>
        
        <!-- PERBAIKAN: SCRIPT DIPINDAH KE SINI AGAR LANGSUNG DIEKSEKUSI -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const labels   = {!! json_encode(collect($chartItems)->pluck('nama_barang')->values()) !!};
                const stokData = {!! json_encode(collect($chartItems)->pluck('stok_akhir')->values()) !!};
                const ctx = document.getElementById('stokChart').getContext('2d');
                
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Stok Akhir',
                            data: stokData,
                            backgroundColor: '#f97316',
                            borderRadius: 4
                        }]
                    },
                    options: { 
                        responsive: true, 
                        scales: { y: { beginAtZero: true } },
                        plugins: {
                            legend: { display: false } // Menyembunyikan legend ganda agar lebih bersih
                        }
                    }
                });
            });
        </script>
    @endif
</div>
@endsection