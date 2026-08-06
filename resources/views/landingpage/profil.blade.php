@extends('layouts.landing')

@section('content')
<div class="container" data-aos="fade-down" data-aos-delay="100" >
    <!-- Breadcrumb -->
    <div class="pt-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Profil GudangKu</li>
            </ol>
        </nav>
    </div>

    <!-- Kotak konten biru lembut tanpa garis tepi -->
    <div class="rounded p-4 shadow-sm bg-primary bg-opacity-10" data-aos="fade-up" data-aos-delay="200" >
        <h2 class="text-center fw-bold mb-4">Profil GudangKu</h2>
        <p class="text-center fs-5 text-muted mb-4">
            Di era digital yang menuntut efisiensi dan kecepatan, pengelolaan gudang secara manual tidak lagi relevan. Gudangku hadir sebagai solusi sistem informasi pergudangan yang modern, sederhana, dan powerful untuk membantu Anda mengelola stok barang dengan lebih mudah, cepat, dan akurat.
        </p>

        <div class="container mt-4" x-data="{ open1: false, open2: false, open3: false, open4: false, open5: false, open6: false, open7: false, open8: false }">
            <h3 class="fw-bold mb-4">Keunggulan meliputi:</h3>

            <!-- Keunggulan 1 -->
            <div class="mb-3 bg-white shadow-sm p-4 rounded-4 hover:shadow-md transition-all duration-300 cursor-pointer" @click="open1 = !open1">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                        <h6 class="mb-0 fw-semibold text-dark">Tanpa Instalasi — Akses Langsung dari Browser</h6>
                    </div>
                    <i class="bi" :class="open1 ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                </div>
                <div x-show="open1" x-transition class="mt-3 text-muted small">
                    Pengguna tidak perlu mengunduh atau menginstal aplikasi tambahan. Cukup buka browser (Chrome, Firefox, Safari, dll.), login, dan sistem langsung siap digunakan. Ini sangat praktis, terutama untuk lingkungan kerja dengan banyak pengguna.
                </div>
            </div>

            <!-- Keunggulan 2 -->
            <div class="mb-3 bg-white shadow-sm p-4 rounded-4 hover:shadow-md transition-all duration-300 cursor-pointer" @click="open2 = !open2">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                        <h6 class="mb-0 fw-semibold text-dark">Bisa Diakses Kapan Saja & di Mana Saja</h6>
                    </div>
                    <i class="bi" :class="open2 ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                </div>
                <div x-show="open2" x-transition class="mt-3 text-muted small">
                    Karena berbasis web, GudangKu dapat diakses dari komputer kantor, laptop pribadi, hingga tablet dan smartphone — selama terhubung internet. Cocok untuk tim lapangan, gudang, dan manajemen di lokasi berbeda.
                </div>
            </div>

            <!-- Keunggulan 3 -->
            <div class="mb-3 bg-white shadow-sm p-4 rounded-4 hover:shadow-md transition-all duration-300 cursor-pointer" @click="open3 = !open3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                        <h6 class="mb-0 fw-semibold text-dark">Sistem Multi-User & Role-Based Access</h6>
                    </div>
                    <i class="bi" :class="open3 ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                </div>
                <div x-show="open3" x-transition class="mt-3 text-muted small">
                    Dengan pembagian peran (Gudang, Superadmin, Viewer), setiap pengguna hanya dapat mengakses data sesuai kebutuhan dan tanggung jawabnya. Keamanan data lebih terjaga, operasional lebih tertib.
                </div>
            </div>

            <!-- Keunggulan 4 -->
            <div class="mb-3 bg-white shadow-sm p-4 rounded-4 hover:shadow-md transition-all duration-300 cursor-pointer" @click="open4 = !open4">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                        <h6 class="mb-0 fw-semibold text-dark">Pantau Semua Pergerakan Barang dalam Genggaman</h6>
                    </div>
                    <i class="bi" :class="open4 ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                </div>
                <div x-show="open4" x-transition class="mt-3 text-muted small">
                    Dengan fitur pencatatan barang masuk dan keluar yang sistematis, Anda tak perlu lagi khawatir kehilangan jejak barang. Semua data tercatat otomatis dan bisa dilacak kapan saja.
                </div>
            </div>

            <!-- Keunggulan 5 -->
            <div class="mb-3 bg-white shadow-sm p-4 rounded-4 hover:shadow-md transition-all duration-300 cursor-pointer" @click="open5 = !open5">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                        <h6 class="mb-0 fw-semibold text-dark">Notifikasi Stok Minimum – Hindari Kehabisan Barang</h6>
                    </div>
                    <i class="bi" :class="open5 ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                </div>
                <div x-show="open5" x-transition class="mt-3 text-muted small">
                    Sistem akan memberitahu Anda saat stok barang mencapai batas minimum. Ini membantu Anda mengambil tindakan lebih cepat untuk pengadaan ulang, tanpa menunggu keluhan pengguna atau pelanggan.
                </div>
            </div>

            <!-- Keunggulan 6 -->
            <div class="mb-3 bg-white shadow-sm p-4 rounded-4 hover:shadow-md transition-all duration-300 cursor-pointer" @click="open6 = !open6">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                        <h6 class="mb-0 fw-semibold text-dark">Laporan Otomatis & Siap Cetak</h6>
                    </div>
                    <i class="bi" :class="open6 ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                </div>
                <div x-show="open6" x-transition class="mt-3 text-muted small">
                    Tidak perlu lagi menyusun laporan manual. Semua data dapat diunduh dalam bentuk PDF atau Excel, baik untuk stok, arus barang, maupun berita acara. Siap digunakan untuk keperluan monitoring, evaluasi, atau pelaporan.
                </div>
            </div>

            <!-- Keunggulan 7 -->
            <div class="mb-3 bg-white shadow-sm p-4 rounded-4 hover:shadow-md transition-all duration-300 cursor-pointer" @click="open7 = !open7">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                        <h6 class="mb-0 fw-semibold text-dark">QR Code Otomatis untuk Identifikasi Cepat</h6>
                    </div>
                    <i class="bi" :class="open7 ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                </div>
                <div x-show="open7" x-transition class="mt-3 text-muted small">
                    Setiap barang yang masuk akan otomatis memiliki QR Code/barcode, memudahkan pencarian dan verifikasi barang secara cepat dan akurat.
                </div>
            </div>

            <!-- Keunggulan 8 -->
            <div class="mb-3 bg-white shadow-sm p-4 rounded-4 hover:shadow-md transition-all duration-300 cursor-pointer" @click="open8 = !open8">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
                        <h6 class="mb-0 fw-semibold text-dark">Tampilan Ramah Pengguna & Mobile-Friendly</h6>
                    </div>
                    <i class="bi" :class="open8 ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                </div>
                <div x-show="open8" x-transition class="mt-3 text-muted small">
                    Antarmuka Gudangku dirancang intuitif dan ringan, bisa diakses dari komputer maupun perangkat mobile. Cocok untuk operasional lapangan maupun back office.
                </div>
            </div>
        </div>
    <BR>
    <h4 class="text-center fw-bold mb-4">🚀 Saatnya Tinggalkan Sistem Manual. Beralih ke GudangKu Sekarang!</h4>
    <p><i>Dengan <strong>GudangKu</strong>, Anda tidak hanya mengelola gudang. Anda sedang membangun sistem kerja yang lebih profesional, rapi, dan siap berkembang.</i></p>

    </div>
</div>
@endsection