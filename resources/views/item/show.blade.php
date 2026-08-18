@extends('layouts.app')
@section('content')

<style>
    body { padding-top: 40px; }
    .bg-dark-header { background-color: #1e293b !important; color: #fff; border-bottom: 3px solid #f97316; }
    .box-stok { border: 2px solid #f97316; background-color: #fffaf5; border-radius: 8px; padding: 20px; text-align: center; }
    .box-min { border: 1px solid #e2e8f0; background-color: #f8fafc; border-radius: 8px; padding: 20px; text-align: center; }
    .table-history { width: 100%; border-collapse: collapse; }
    .table-history th, .table-history td { padding: 15px; text-align: center; border-bottom: 1px solid #e2e8f0; }
    .table-history tr:hover { background-color: #f8fafc; }
    .img-box { width: 120px; height: 120px; object-fit: contain; border-radius: 8px; border: 1px solid #e2e8f0; background: #f8fafc; padding: 5px;}
    
    @media(max-width: 768px) {
        .table-history, .table-history thead, .table-history tbody, .table-history th, .table-history td, .table-history tr { display: block; width: 100%; }
        .table-history thead { display: none; }
        .table-history tr { margin-bottom: 15px; border: 1px solid #e2e8f0; border-radius: 12px; padding: 10px; background-color: #fff; }
        .table-history td { border: none !important; text-align: left; padding: 8px 12px 8px 50%; position: relative; }
        .table-history td:before { position: absolute; top: 8px; left: 12px; width: 40%; white-space: nowrap; font-weight: bold; color: #4b5563; }
        .table-history td:nth-of-type(1):before { content: "Tanggal"; }
        .table-history td:nth-of-type(2):before { content: "Jenis"; }
        .table-history td:nth-of-type(3):before { content: "Jumlah"; }
        .table-history td:nth-of-type(4):before { content: "Lokasi"; }
        .table-history td:nth-of-type(5):before { content: "Dicatat Oleh"; }
    }
</style>

<div class="container mt-2 mb-5" style="max-width: 1000px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0" style="color: #374151;">Katalog &rsaquo; Detail Produk</h3>
        <a href="{{ route('item.index') }}" class="btn btn-outline-secondary px-4 fw-medium">Kembali</a>
    </div>

    {{-- Kartu Info Utama --}}
    <div class="card border-0 shadow-sm mb-4 p-4 rounded-4">
        <div class="row align-items-center g-4">
            <!-- Foto -->
            <div class="col-md-2 text-center">
                @if($item->foto)
                    <img src="{{ asset('storage/' . $item->foto) }}" class="img-box shadow-sm">
                @else
                    <div class="img-box d-flex align-items-center justify-content-center mx-auto">
                        <span class="text-muted fw-medium" style="font-size:0.8rem;">Foto Produk</span>
                    </div>
                @endif
            </div>
            
            <!-- Info Detail -->
            <div class="col-md-5">
                <h4 class="fw-bold text-dark mb-3">{{ $item->nama_barang }}</h4>
                <p class="text-muted mb-2">Kode: <strong>{{ $item->kode_barang }}</strong> &nbsp;|&nbsp; Kategori: <strong>{{ $item->kategori->kategori ?? '-' }}</strong> &nbsp;|&nbsp; Satuan: <strong>{{ $item->satuan->nama_satuan ?? '-' }}</strong></p>
                <p class="text-muted mb-2">Harga Dasar: <strong>Rp {{ number_format($item->harga_dasar, 0, ',', '.') }} / {{ $item->satuan->nama_satuan ?? 'unit' }}</strong></p>
                <p class="text-muted mb-0" style="font-size: 0.9em;">Dibuat Pada: {{ $item->created_at->format('d M Y') }}</p>
            </div>
            
            <!-- Stok Boxes -->
            <div class="col-md-5 d-flex gap-3">
                <div class="box-stok flex-fill shadow-sm">
                    <h6 class="fw-bold mb-2" style="color: #ea580c;">Stok Saat Ini</h6>
                    <h2 class="fw-bold mb-0" style="color: #f97316;">{{ $stokSaatIni }} <span class="fs-5">{{ $item->satuan->nama_satuan ?? '' }}</span></h2>
                </div>
                <div class="box-min flex-fill shadow-sm">
                    <h6 class="text-muted fw-bold mb-2">Stok Minimum</h6>
                    <h2 class="fw-bold text-dark mb-0">{{ $item->stok_minimum }} <span class="fs-5">{{ $item->satuan->nama_satuan ?? '' }}</span></h2>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Riwayat --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <table class="table-history">
            <thead class="bg-dark-header">
                <tr>
                    <th>Tanggal</th>
                    <th>Jenis</th>
                    <th>Jumlah</th>
                    <th>Lokasi</th>
                    <th>Dicatat Oleh</th>
                </tr>
            </thead>
            <tbody>
                @forelse($riwayat as $trx)
                <tr>
                    <td class="fw-medium text-secondary">{{ \Carbon\Carbon::parse($trx['tanggal'])->format('d M Y') }}</td>
                    <td class="fw-bold {{ $trx['jenis'] == 'Masuk' ? 'text-success' : 'text-danger' }}">{{ $trx['jenis'] }}</td>
                    <td class="fw-bold {{ $trx['jenis'] == 'Masuk' ? 'text-success' : 'text-danger' }}">
                        {{ $trx['jenis'] == 'Masuk' ? '+' : '-' }}{{ $trx['jumlah'] }} <span class="fw-normal">{{ $item->satuan->nama_satuan ?? '' }}</span>
                    </td>
                    <td>{{ $trx['lokasi'] }}</td>
                    <td>{{ $trx['user'] }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-5 text-muted fw-medium">Belum ada riwayat transaksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection