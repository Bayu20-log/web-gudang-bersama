@extends('layouts.app')
@section('content')

@php
    // Menentukan rute dashboard secara otomatis sesuai role user yang sedang login
    $role = Auth::user()->role;
    $dashboardRoute = match ($role) {
        'superadmin' => route('dashboard.superadmin'),
        'viewer'     => route('dashboard.viewer'),
        'gudang'     => route('dashboard.gudang'),
        default      => route('dashboard'),
    };
@endphp

<!-- CDN Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    body { padding-top: 40px; background-color: #f8fafc; }
    
    /* Layout dinamis: Maksimal 900px di Desktop agar membentang elegan */
    .app-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 10px 15px 50px 15px;
    }

    /* Banner Oranye yang bisa di-klik */
    .dashboard-banner {
        display: block;
        text-decoration: none;
        background: linear-gradient(135deg, #f97316, #ea580c);
        border-radius: 1.2rem;
        padding: 30px 25px;
        color: white;
        box-shadow: 0 10px 20px rgba(249, 115, 22, 0.2);
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .dashboard-banner:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 25px rgba(249, 115, 22, 0.3);
        color: white;
    }
    
    /* Lingkaran dekorasi */
    .dashboard-banner::after {
        content: '';
        position: absolute;
        top: -50px;
        right: -20px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        z-index: 1;
    }

    .banner-content {
        position: relative;
        z-index: 2;
    }

    .banner-title {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .banner-desc {
        font-size: 0.9rem;
        opacity: 0.95;
        margin-bottom: 0;
        line-height: 1.5;
        max-width: 90%;
    }

    /* Ikon samar di background kanan */
    .banner-icon-bg {
        position: absolute;
        right: 30px;
        bottom: -20px;
        font-size: 7rem;
        color: rgba(255, 255, 255, 0.15);
        z-index: 1;
        transform: rotate(-10deg);
    }

    /* KOTAK KATEGORI */
    .category-wrapper {
        background: #ffffff;
        border-radius: 1.2rem;
        padding: 25px 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        margin-top: 20px;
        border: 1px solid #f1f5f9;
    }

    .section-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #334155;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        border-bottom: 2px solid #f8fafc;
        padding-bottom: 10px;
    }

    /* Desain Grid Membagi 4 Kolom di HP */
    .grid-container {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px 10px;
    }

    .grid-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-decoration: none;
        color: #374151;
        transition: transform 0.2s;
    }

    .grid-item:hover { transform: translateY(-3px); }

    .icon-box {
        width: 55px;
        height: 55px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 8px;
        border: 1px solid #e2e8f0;
        background-color: #fff;
        color: #1e293b;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        transition: 0.3s;
    }

    .grid-item:hover .icon-box {
        border-color: #f97316;
        color: #f97316;
        box-shadow: 0 4px 10px rgba(249, 115, 22, 0.15);
    }

    .icon-text {
        font-size: 0.75rem;
        font-weight: 600;
        text-align: center;
        line-height: 1.2;
    }

    /* Kustomisasi warna ikon */
    .icon-box.green { color: #16a34a; }
    .icon-box.red { color: #dc2626; }
    .icon-box.orange { color: #ea580c; }
    .icon-box.blue { color: #2563eb; }
    .icon-box.teal { color: #0d9488; }
    .icon-box.purple { color: #9333ea; }
    .icon-box.yellow { color: #ca8a04; }

    /* --- RESPONSIVE UNTUK HP KECIL --- */
    @media (max-width: 480px) {
        .category-wrapper { padding: 20px 15px; }
        .grid-container { gap: 15px 5px; }
        .icon-box { width: 50px; height: 50px; font-size: 1.35rem; }
        .icon-text { font-size: 0.7rem; }
    }

    /* --- RESPONSIVE DESKTOP OVERRIDE --- */
    @media (min-width: 768px) {
        .dashboard-banner { padding: 40px 50px; }
        .banner-title { font-size: 1.7rem; }
        .banner-desc { font-size: 1rem; max-width: 70%; }
        .icon-box { width: 70px; height: 70px; font-size: 2rem; border-radius: 18px; }
        .icon-text { font-size: 0.85rem; }
        
        /* Ubah jadi 5 kolom di Desktop agar Master Data muat dalam 1 baris */
        .grid-container {
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
        }
    }
</style>

<div class="app-container">
    
    <!-- Banner Dashboard Clickable -->
    <a href="{{ $dashboardRoute }}" class="dashboard-banner">
        <div class="banner-content">
            <div class="banner-title">
                Buka Dashboard Gudang <i class="bi bi-arrow-right-circle-fill ms-2"></i>
            </div>
            <p class="banner-desc">
                Pantau ringkasan transaksi hari ini, peringatan stok hampir habis, barang kadaluarsa, serta analitik data pergudangan secara lengkap.
            </p>
        </div>
        <i class="bi bi-bar-chart-fill banner-icon-bg"></i>
    </a>

    <!-- Kategori 1: TRANSAKSI & PRODUK -->
    <div class="category-wrapper">
        <div class="section-title">
            <i class="bi bi-box-seam text-orange me-2 fs-5"></i> Transaksi & Produk
        </div>
        <div class="grid-container">
            <a href="{{ route('barang-masuk.create') }}" class="grid-item">
                <div class="icon-box green"><i class="bi bi-box-arrow-in-down"></i></div>
                <span class="icon-text">Barang Masuk</span>
            </a>
            <a href="{{ route('barang-keluar.create') }}" class="grid-item">
                <div class="icon-box red"><i class="bi bi-box-arrow-up"></i></div>
                <span class="icon-text">Barang Keluar</span>
            </a>
            <a href="{{ route('item.create') }}" class="grid-item">
                <div class="icon-box orange"><i class="bi bi-plus-square"></i></div>
                <span class="icon-text">Tambah Produk</span>
            </a>
            <a href="{{ route('item.index') }}" class="grid-item">
                <div class="icon-box blue"><i class="bi bi-boxes"></i></div>
                <span class="icon-text">Daftar Produk</span>
            </a>
        </div>
    </div>

    <!-- Kategori 2: MASTER DATA -->
    <div class="category-wrapper">
        <div class="section-title">
            <i class="bi bi-database-fill text-blue me-2 fs-5"></i> Master Data
        </div>
        <div class="grid-container">
            <a href="{{ route('pemasok.index') }}" class="grid-item">
                <div class="icon-box"><i class="bi bi-truck"></i></div>
                <span class="icon-text">Pemasok</span>
            </a>
            <a href="{{ route('lokasi.index') }}" class="grid-item">
                <div class="icon-box"><i class="bi bi-shop"></i></div>
                <span class="icon-text">Gudang</span>
            </a>
            <a href="{{ route('kategori.index') }}" class="grid-item">
                <div class="icon-box"><i class="bi bi-tags"></i></div>
                <span class="icon-text">Kategori</span>
            </a>
            <a href="{{ route('satuan.index') }}" class="grid-item">
                <div class="icon-box"><i class="bi bi-rulers"></i></div>
                <span class="icon-text">Satuan</span>
            </a>
            <a href="{{ route('kondisi.index') }}" class="grid-item">
                <div class="icon-box"><i class="bi bi-clipboard2-check"></i></div>
                <span class="icon-text">Kondisi</span>
            </a>
        </div>
    </div>

    <!-- Kategori 3: LAPORAN -->
    <div class="category-wrapper">
        <div class="section-title">
            <i class="bi bi-file-earmark-bar-graph-fill text-teal me-2 fs-5"></i> Laporan
        </div>
        <div class="grid-container">
            <a href="{{ route('laporan') }}" class="grid-item">
                <div class="icon-box teal"><i class="bi bi-pie-chart"></i></div>
                <span class="icon-text">Laporan Stok</span>
            </a>
            <a href="{{ route('laporan.arus') }}" class="grid-item">
                <div class="icon-box teal"><i class="bi bi-graph-up-arrow"></i></div>
                <span class="icon-text">Arus Barang</span>
            </a>
            <a href="{{ route('aset.index') }}" class="grid-item">
                <div class="icon-box teal"><i class="bi bi-building"></i></div>
                <span class="icon-text">Laporan Aset</span>
            </a>
            <a href="{{ route('omzet.index') }}" class="grid-item">
                <div class="icon-box teal"><i class="bi bi-cash-coin"></i></div>
                <span class="icon-text">Omzet Penjualan</span>
            </a>
        </div>
    </div>

    <!-- Kategori 4: FITUR TAMBAHAN -->
    <div class="category-wrapper">
        <div class="section-title">
            <i class="bi bi-grid-fill text-purple me-2 fs-5"></i> Fitur Tambahan
        </div>
        <div class="grid-container">
            <a href="{{ route('notifications.index') ?? '#' }}" class="grid-item">
                <div class="icon-box yellow"><i class="bi bi-bell"></i></div>
                <span class="icon-text">Notifikasi</span>
            </a>
            <a href="#" class="grid-item" onclick="alert('Fitur Prediksi Data (AI) masih dalam tahap pengembangan!')">
                <div class="icon-box purple"><i class="bi bi-magic"></i></div>
                <span class="icon-text">Prediksi</span>
            </a>
        </div>
    </div>

</div>

@endsection