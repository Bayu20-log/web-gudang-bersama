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

<!-- CDN Bootstrap Icons (Memastikan ikon tidak blank) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<style>
    body { padding-top: 40px; background-color: #f8fafc; }
    
    /* Layout dinamis: Maksimal 1000px di Desktop agar membentang elegan */
    .app-container {
        max-width: 1000px;
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
        color: white; /* Memastikan teks tetap putih saat di-hover */
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

    /* Grid Akses Cepat */
    .quick-access-wrapper {
        background: #ffffff;
        border-radius: 1.2rem;
        padding: 30px 25px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        margin-top: 25px;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 25px;
    }

    /* Desain Grid Mobile-First (3 Kolom) */
    .grid-container {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px 15px;
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
        line-height: 1.3;
    }

    /* Kustomisasi warna ikon */
    .icon-box.green { color: #16a34a; }
    .icon-box.red { color: #dc2626; }
    .icon-box.orange { color: #ea580c; }
    .icon-box.blue { color: #2563eb; }
    .icon-box.teal { color: #0d9488; }
    .icon-box.purple { color: #9333ea; }
    .icon-box.yellow { color: #ca8a04; }

    /* --- RESPONSIVE DESKTOP OVERRIDE --- */
    @media (min-width: 768px) {
        .grid-container {
            grid-template-columns: repeat(6, 1fr); /* Membentang 6 kolom di Desktop */
            gap: 30px 20px;
        }
        .dashboard-banner { padding: 40px 50px; }
        .banner-title { font-size: 1.7rem; }
        .banner-desc { font-size: 1rem; max-width: 70%; }
        .icon-box { width: 70px; height: 70px; font-size: 2rem; border-radius: 18px; }
        .icon-text { font-size: 0.85rem; }
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
        <!-- Ikon Background Samar -->
        <i class="bi bi-bar-chart-fill banner-icon-bg"></i>
    </a>

    <!-- Kotak Akses Cepat -->
    <div class="quick-access-wrapper">
        <div class="section-title">Akses Cepat</div>
        
        <div class="grid-container">
            <!-- Transaksi & Barang -->
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
                <div class="icon-box blue"><i class="bi bi-box-seam"></i></div>
                <span class="icon-text">Daftar Produk</span>
            </a>

            <!-- Master Data -->
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

            <!-- Laporan & Fitur Ekstra -->
            <a href="{{ route('laporan') }}" class="grid-item">
                <div class="icon-box teal"><i class="bi bi-pie-chart"></i></div>
                <span class="icon-text">Laporan Stok</span>
            </a>
            <a href="{{ route('laporan.arus') }}" class="grid-item">
                <div class="icon-box teal"><i class="bi bi-graph-up-arrow"></i></div>
                <span class="icon-text">Arus Barang</span>
            </a>
            
            <!-- Fitur Tambahan -->
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