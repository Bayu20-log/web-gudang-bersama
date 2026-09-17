@extends('layouts.app')
@section('content')

<style>
    body { padding-top: 40px; }
    .detail-label { font-weight: 600; color: #64748b; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;}
    .detail-value { font-weight: 700; color: #1e293b; font-size: 1.1rem; margin-bottom: 18px;}
    .qr-box { border: 2px dashed #cbd5e1; border-radius: 16px; padding: 15px; background: #f8fafc; transition: 0.3s; }
    .qr-box:hover { border-color: #f97316; box-shadow: 0 4px 15px rgba(249, 115, 22, 0.1); }
    
    .btn-orange { background-color: #f97316; color: #fff; border: none; transition: 0.3s; }
    .btn-orange:hover { background-color: #ea580c; color: #fff; }
    .btn-outline-dark { border: 2px solid #1e293b; color: #1e293b; transition: 0.3s; background: transparent; font-weight: 600;}
    .btn-outline-dark:hover { background-color: #1e293b; color: #fff; }

    .photo-container {
        width: 100%;
        max-width: 280px;
        aspect-ratio: 1 / 1;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        background-color: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    }
    .photo-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>

<div class="container mt-2 mb-5" style="max-width: 950px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0" style="color: #374151;">Barang Masuk &rsaquo; Detail & QR Code</h3>
        <a href="{{ route('barang-masuk.index') }}" class="btn btn-outline-secondary px-4 fw-medium">Kembali</a>
    </div>

    <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 bg-white">
        <div class="row g-5 align-items-start">
            
            <!-- Kiri: Foto Barang -->
            <div class="col-md-4 text-center">
                <div class="mb-3 detail-label text-center">Foto Barang</div>
                <div class="photo-container">
                    @if($barangMasuk->item && $barangMasuk->item->foto && Storage::disk('public')->exists($barangMasuk->item->foto))
                        <img src="{{ asset('storage/' . $barangMasuk->item->foto) }}" alt="Foto Barang">
                    @else
                        <div class="text-muted fst-italic d-flex flex-column align-items-center">
                            <i class="fa-solid fa-image fs-1 mb-2 text-secondary"></i>
                            <span>Tidak ada foto</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tengah & Kanan: Info & QR Code -->
            <div class="col-md-8">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="detail-label">Kode Barang</div>
                        <div class="detail-value text-primary fs-5">{{ $barangMasuk->kode_barang }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="detail-label">Nama Barang</div>
                        <div class="detail-value">{{ $barangMasuk->item->nama_barang ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="detail-label">Jumlah Masuk</div>
                        <div class="detail-value text-success fs-4">{{ $barangMasuk->jumlah }} <span class="fs-6 text-muted">{{ $barangMasuk->item->satuan->nama_satuan ?? '' }}</span></div>
                    </div>
                    <div class="col-sm-6">
                        <div class="detail-label">Harga Beli / Unit</div>
                        <div class="detail-value">Rp {{ number_format($barangMasuk->harga_satuan, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="detail-label">Total Harga</div>
                        <div class="detail-value">Rp {{ number_format($barangMasuk->total_harga, 0, ',', '.') }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="detail-label">Tanggal Masuk</div>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($barangMasuk->tanggal_masuk)->format('d M Y, H:i') }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="detail-label">Kondisi & Lokasi</div>
                        <div class="detail-value">{{ $barangMasuk->kondisi->nama_kondisi ?? '-' }} &mdash; {{ $barangMasuk->lokasi->nama_lokasi ?? '-' }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div class="detail-label">Dicatat Oleh (Petugas)</div>
                        <div class="detail-value">{{ $barangMasuk->user->name ?? '-' }}</div>
                    </div>
                </div>
                
                <hr class="text-muted my-4 border-2">

                <!-- Bagian Bawah: QR Code & Tombol Cetak -->
                <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-4">
                    
                    <!-- Kotak QR -->
                    <div class="qr-box text-center shadow-sm d-flex flex-column align-items-center justify-content-center">
                        @if($barangMasuk->qr_code && Storage::disk('public')->exists($barangMasuk->qr_code))
                            <img src="{{ asset('storage/' . $barangMasuk->qr_code) }}" alt="QR Code" style="width: 120px; height: 120px; mix-blend-mode: multiply;">
                        @else
                            <div class="text-muted fst-italic p-3">QR Code belum digenerate</div>
                        @endif
                    </div>
                    
                    <!-- Kumpulan Tombol Cetak -->
                    <div class="d-flex flex-wrap gap-2 justify-content-md-end justify-content-center w-100">
                        <a href="{{ route('barang-masuk.cetak-qr-kecil', $barangMasuk->id) }}" class="btn btn-outline-dark px-4 py-2 shadow-sm rounded-pill">
                            <i class="fa-solid fa-qrcode me-1"></i> Cetak QR
                        </a>
                        <a href="{{ route('barang-masuk.cetak.pdf', $barangMasuk->id) }}" class="btn btn-outline-dark px-4 py-2 shadow-sm rounded-pill">
                            <i class="fa-solid fa-tag me-1"></i> Cetak Label
                        </a>
                        <a href="{{ route('barang-masuk.cetak-berita-acara', $barangMasuk->id) }}" target="_blank" class="btn btn-orange fw-bold px-4 py-2 shadow-sm rounded-pill">
                            <i class="fa-solid fa-file-signature me-1"></i> Cetak BA
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection