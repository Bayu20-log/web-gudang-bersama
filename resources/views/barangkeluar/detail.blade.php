@extends('layouts.app')
@section('content')

<style>
    body { padding-top: 40px; }
    .detail-label { font-weight: 600; color: #4b5563; font-size: 0.9rem; margin-bottom: 2px;}
    .detail-value { font-weight: 700; color: #1e293b; font-size: 1.1rem; margin-bottom: 15px;}
    .btn-orange { background-color: #f97316; color: #fff; border: none; transition: 0.3s; }
    .btn-orange:hover { background-color: #ea580c; color: #fff; }
</style>

<div class="container mt-2 mb-5" style="max-width: 800px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0" style="color: #374151;">Barang Keluar &rsaquo; Detail Transaksi</h3>
        <a href="{{ route('barang-keluar.index') }}" class="btn btn-outline-secondary px-4 fw-medium">Kembali</a>
    </div>

    @php
        $hargaRata = \App\Models\BarangMasuk::where('kode_barang', $barangKeluar->kode_barang)
            ->where('id_lokasi', $barangKeluar->id_lokasi)
            ->where('id_kondisi', $barangKeluar->id_kondisi)
            ->orderByDesc('tanggal_masuk')
            ->orderByDesc('id')
            ->value('harga_satuan') ?? 0;
    @endphp

    <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 bg-white">
        <div class="row">
            <div class="col-sm-6">
                <div class="detail-label">Tanggal Keluar</div>
                <div class="detail-value text-danger">{{ \Carbon\Carbon::parse($barangKeluar->tanggal_keluar)->format('d M Y, H:i') }}</div>
            </div>
            <div class="col-sm-6">
                <div class="detail-label">Penerima</div>
                <div class="detail-value">{{ $barangKeluar->penerima ?? '-' }}</div>
            </div>
            <div class="col-sm-6">
                <div class="detail-label">Nama Barang</div>
                <div class="detail-value">{{ $barangKeluar->item->nama_barang ?? '-' }}</div>
            </div>
            <div class="col-sm-6">
                <div class="detail-label">Kode Barang</div>
                <div class="detail-value">{{ $barangKeluar->kode_barang }}</div>
            </div>
            <div class="col-sm-6">
                <div class="detail-label">Jumlah Keluar</div>
                <div class="detail-value text-danger fs-4">{{ $barangKeluar->jumlah_keluar }} <span class="fs-6 text-muted">{{ $barangKeluar->item->satuan->nama_satuan ?? '' }}</span></div>
            </div>
            <div class="col-sm-6">
                <div class="detail-label">Total Harga Jual</div>
                <div class="detail-value">Rp {{ number_format($barangKeluar->total_harga_jual, 0, ',', '.') }}</div>
            </div>
            <div class="col-sm-6">
                <div class="detail-label">Lokasi Awal (Gudang)</div>
                <div class="detail-value">{{ $barangKeluar->lokasi->nama_lokasi ?? '-' }}</div>
            </div>
            <div class="col-sm-6">
                <div class="detail-label">Lokasi Tujuan</div>
                <div class="detail-value">{{ $barangKeluar->lokasi_tujuan ?? '-' }}</div>
            </div>
        </div>

        <hr class="text-muted mt-2 mb-4">

        <div class="row">
            <div class="col-sm-6">
                <div class="detail-label">Catatan Pengeluaran</div>
                <div class="text-secondary mb-3">{{ $barangKeluar->catatan ?? '-' }}</div>
            </div>
            <div class="col-sm-6">
                <div class="detail-label">Dibuat Pada</div>
                <div class="text-secondary" style="font-size:0.85em;">{{ $barangKeluar->created_at->format('d M Y H:i:s') }} oleh {{ $barangKeluar->user->username ?? '-' }}</div>
            </div>
        </div>

        {{-- Tombol Cetak Dokumen --}}
        <div class="d-flex flex-wrap gap-2 justify-content-end mt-4 pt-3 border-top">
            <a href="{{ route('barang-keluar.cetak-detail', $barangKeluar->id) }}" target="_blank" class="btn btn-outline-dark fw-bold px-4">
                <i class="fa-solid fa-file-pdf me-2"></i>Cetak PDF
            </a>
            <a href="{{ route('barang-keluar.cetak-ba', $barangKeluar->id) }}" target="_blank" class="btn btn-orange fw-bold px-4">
                <i class="fa-solid fa-file-signature me-2"></i>Cetak Berita Acara
            </a>
        </div>
    </div>
</div>
@endsection