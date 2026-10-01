@extends('layouts.app')
@section('content')

<style>
    body { padding-top: 40px; }
    .bg-dark-header { background-color: #1e293b !important; color: #fff; border-bottom: 3px solid #f97316; }
    .box-stok { border: 2px solid #f97316; background-color: #fffaf5; border-radius: 8px; padding: 20px; text-align: center; }
    .box-min { border: 1px solid #e2e8f0; background-color: #f8fafc; border-radius: 8px; padding: 20px; text-align: center; }
    .img-box { width: 120px; height: 120px; object-fit: contain; border-radius: 8px; border: 1px solid #e2e8f0; background: #f8fafc; padding: 5px;}

    /* --- Riwayat Transaksi versi ringkas --- */
    .riwayat-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px; padding: 0 4px; }
    .riwayat-header .rh-title { text-transform: uppercase; font-size: 12px; font-weight: 700; color: #64748b; letter-spacing: .04em; }
    .riwayat-header .rh-link { font-size: 13px; font-weight: 700; color: #f97316; text-decoration: none; }
    .riwayat-header .rh-link:hover { color: #ea580c; text-decoration: underline; }
    .riwayat-item { display: flex; justify-content: space-between; align-items: flex-start; gap: 10px; padding: 12px 4px; border-bottom: 1px solid #f1f5f9; }
    .riwayat-item:last-child { border-bottom: none; }
    .riwayat-item .ri-date { font-weight: 700; color: #1e293b; font-size: 14px; }
    .riwayat-item .ri-sub { font-size: 12.5px; color: #6b7280; margin-top: 2px; }
    .riwayat-item .ri-jumlah { font-weight: 700; font-size: 14.5px; white-space: nowrap; }
    .riwayat-badge { font-size: 10.5px; font-weight: 700; padding: 2px 8px; border-radius: 999px; }
    .riwayat-badge.masuk { background: #f0fdf4; color: #16a34a; }
    .riwayat-badge.keluar { background: #fef2f2; color: #ef4444; }

    /* --- Empty state Riwayat Transaksi --- */
    .riwayat-empty { text-align: center; padding: 30px 20px; }
    .riwayat-empty-icon {
        width: 52px; height: 52px; border-radius: 50%; background: #f1f5f9; color: #94a3b8;
        display: flex; align-items: center; justify-content: center; margin: 0 auto 14px;
    }
    .riwayat-empty .re-title { font-weight: 700; color: #1e293b; font-size: 15px; margin-bottom: 4px; }
    .riwayat-empty .re-sub { color: #6b7280; font-size: 13px; max-width: 280px; margin: 0 auto; }
</style>

<div class="container mt-2 mb-5" style="max-width: 1000px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0" style="color: #374151;">Detail Produk</h3>
        <a href="{{ route('item.index') }}" class="btn btn-outline-secondary px-4 fw-medium">Kembali</a>
    </div>

    {{-- Kartu Info Utama --}}
    <div class="card border-0 shadow-sm mb-4 p-4 rounded-4">
        <div class="row align-items-center g-4">
            <!-- Foto -->
            <div class="col-md-2 text-center">
                @if($item->foto)
                    <img src="{{ asset($item->foto) }}" class="img-box shadow-sm">
                @else
                    <div class="img-box d-flex align-items-center justify-content-center mx-auto text-secondary" style="opacity:0.5;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16"><path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/><path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-10zm10 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h10z"/></svg>
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

    {{-- Riwayat Transaksi (versi ringkas) --}}
    <div class="card border-0 shadow-sm rounded-4 p-3">
        <div class="riwayat-header">
            <span class="rh-title">Riwayat Transaksi</span>
            @if($riwayat->count() > 0)
                <a href="{{ route('laporan.arus', ['search' => $item->kode_barang]) }}" class="rh-link">Lihat semua</a>
            @endif
        </div>

        @forelse($riwayat->take(5) as $trx)
            <div class="riwayat-item">
                <div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="ri-date">{{ \Carbon\Carbon::parse($trx['tanggal'])->format('d M Y') }}</span>
                        <span class="riwayat-badge {{ $trx['jenis'] == 'Masuk' ? 'masuk' : 'keluar' }}">{{ strtoupper($trx['jenis']) }}</span>
                    </div>
                    <div class="ri-sub">{{ $trx['lokasi'] }} &middot; {{ $trx['user'] }}</div>
                </div>
                <div class="ri-jumlah {{ $trx['jenis'] == 'Masuk' ? 'text-success' : 'text-danger' }}">
                    {{ $trx['jenis'] == 'Masuk' ? '+' : '-' }}{{ $trx['jumlah'] }} <span class="fw-normal" style="font-size:12px;">{{ $item->satuan->nama_satuan ?? '' }}</span>
                </div>
            </div>
        @empty
            <div class="riwayat-empty">
                <div class="riwayat-empty-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 16 16"><path d="M8 3.5a.5.5 0 0 0-1 0V9a.5.5 0 0 0 .252.434l3.5 2a.5.5 0 0 0 .496-.868L8 8.71V3.5z"/><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm7-8A7 7 0 1 1 1 8a7 7 0 0 1 14 0z"/></svg>
                </div>
                <div class="re-title">Belum ada transaksi</div>
                <div class="re-sub">Riwayat masuk dan keluar produk ini akan muncul di sini setelah dicatat.</div>
            </div>
        @endforelse
    </div>
</div>

@endsection