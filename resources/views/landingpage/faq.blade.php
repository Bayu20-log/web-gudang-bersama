@extends('layouts.landing')

@section('content')
<div class="container" data-aos="fade-down" data-aos-delay="100">
    <!-- Breadcrumb -->
    <div class="pt-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Frequently Ask Questions (FAQ)</li>
            </ol>
        </nav>
    </div>

    <!-- Kotak konten biru lembut tanpa garis tepi -->
    <div class="rounded p-4 shadow-sm bg-primary bg-opacity-10" data-aos="fade-up" data-aos-delay="200">
        <h2 class="text-center fw-bold mb-4">Frequently Ask Questions</h2>

        <div class="container mt-4" x-data="{ open1: false, open2: false, open3: false, open4: false, open5: false, open6: false, open7: false, open8: false, open9: false }">
            
            <!-- Q1 -->
            <div class="mb-3 bg-white shadow-sm p-4 rounded-4 hover:shadow-md transition-all duration-300 cursor-pointer" @click="open1 = !open1">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <h6 class="mb-0 fw-semibold text-dark">1. Apakah Gudangku bisa digunakan di handphone atau tablet?</h6>
                    </div>
                    <i class="bi" :class="open1 ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                </div>
                <div x-show="open1" x-transition class="mt-3 text-muted small">
                    <STRONG>Ya.</STRONG> Gudangku berbasis web dan dirancang dengan antarmuka responsif, sehingga dapat digunakan melalui browser di perangkat apa pun—baik desktop, laptop, tablet, maupun smartphone.
                </div>
            </div>

            <!-- Q2 -->
            <div class="mb-3 bg-white shadow-sm p-4 rounded-4 hover:shadow-md transition-all duration-300 cursor-pointer" @click="open2 = !open2">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <h6 class="mb-0 fw-semibold text-dark">2. Apakah sistem Gudangku membutuhkan instalasi?</h6>
                    </div>
                    <i class="bi" :class="open2 ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                </div>
                <div x-show="open2" x-transition class="mt-3 text-muted small">
                    <STRONG>Tidak perlu.</STRONG> Gudangku adalah sistem berbasis web, sehingga Anda cukup membukanya melalui browser tanpa instalasi aplikasi tambahan.
                </div>
            </div>

            <!-- Q3 -->
            <div class="mb-3 bg-white shadow-sm p-4 rounded-4 hover:shadow-md transition-all duration-300 cursor-pointer" @click="open3 = !open3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <h6 class="mb-0 fw-semibold text-dark">3. Apakah Gudangku bisa digunakan untuk lebih dari satu lokasi gudang?</h6>
                    </div>
                    <i class="bi" :class="open3 ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                </div>
                <div x-show="open3" x-transition class="mt-3 text-muted small">
                    <STRONG>Bisa.</STRONG> Gudangku mendukung sistem multi-lokasi, sehingga Anda dapat mencatat dan memantau stok dari berbagai gudang atau penyimpanan dalam satu dashboard.
                </div>
            </div>

            <!-- Q4 -->
            <div class="mb-3 bg-white shadow-sm p-4 rounded-4 hover:shadow-md transition-all duration-300 cursor-pointer" @click="open4 = !open4">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <h6 class="mb-0 fw-semibold text-dark">4. Apakah saya bisa mengekspor laporan ke PDF atau Excel?</h6>
                    </div>
                    <i class="bi" :class="open4 ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                </div>
                <div x-show="open4" x-transition class="mt-3 text-muted small">
                    <STRONG>Ya.</STRONG> Semua laporan arus barang dan stok tersedia dalam format cetak (PDF) dan spreadsheet (Excel) yang siap digunakan untuk rekap, atau arsip.
                </div>
            </div>

            <!-- Q5 -->
            <div class="mb-3 bg-white shadow-sm p-4 rounded-4 hover:shadow-md transition-all duration-300 cursor-pointer" @click="open5 = !open5">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <h6 class="mb-0 fw-semibold text-dark">5. Apakah Gudangku menyediakan notifikasi saat stok barang menipis?</h6>
                    </div>
                    <i class="bi" :class="open5 ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                </div>
                <div x-show="open5" x-transition class="mt-3 text-muted small">
                    <STRONG>Ya</STRONG>. Anda dapat mengatur batas minimum stok. Jika stok barang menyentuh batas tersebut, sistem akan memberikan notifikasi otomatis kepada petugas gudang.
                </div>
            </div>

            <!-- Q6 -->
            <div class="mb-3 bg-white shadow-sm p-4 rounded-4 hover:shadow-md transition-all duration-300 cursor-pointer" @click="open6 = !open6">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <h6 class="mb-0 fw-semibold text-dark">6. Bagaimana keamanan data saya di Gudangku?</h6>
                    </div>
                    <i class="bi" :class="open6 ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                </div>
                <div x-show="open6" x-transition class="mt-3 text-muted small">
                    <STRONG>Data Anda aman.</STRONG> Sistem dilengkapi dengan pengelolaan hak akses berbasis peran (role-based access), backup berkala, dan enkripsi data yang sesuai standar keamanan.
                </div>
            </div>

            <!-- Q7 -->
            <div class="mb-3 bg-white shadow-sm p-4 rounded-4 hover:shadow-md transition-all duration-300 cursor-pointer" @click="open7 = !open7">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <h6 class="mb-0 fw-semibold text-dark">7. Apakah Gudangku bisa digunakan secara offline?</h6>
                    </div>
                    <i class="bi" :class="open7 ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                </div>
                <div x-show="open7" x-transition class="mt-3 text-muted small">
                    Saat ini Gudangku hanya tersedia dalam mode online agar seluruh data tersimpan dan tersinkron secara real-time. Namun, data dapat diekspor untuk digunakan secara offline.
                </div>
            </div>

            <!-- Q8 -->
            <div class="mb-3 bg-white shadow-sm p-4 rounded-4 hover:shadow-md transition-all duration-300 cursor-pointer" @click="open8 = !open8">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <h6 class="mb-0 fw-semibold text-dark">8. Siapa saja yang bisa menggunakan sistem Gudangku?</h6>
                    </div>
                    <i class="bi" :class="open8 ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                </div>
                <div x-show="open8" x-transition class="mt-3 text-muted small">
                    Gudangku dirancang untuk digunakan oleh:
                    <ul>
                        <li>Petugas Gudang (untuk pencatatan harian),</li>
                        <li>Superadmin (untuk manajemen user dan laporan),</li>
                        <li>Viewer (untuk pemantauan stok tanpa akses edit).</li>
                    </ul>
                </div>
            </div>

            <!-- Q9 -->
            <div class="mb-3 bg-white shadow-sm p-4 rounded-4 hover:shadow-md transition-all duration-300 cursor-pointer" @click="open9 = !open9">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <h6 class="mb-0 fw-semibold text-dark">9. Apakah bisa dibuatkan berita acara dari transaksi barang?</h6>
                    </div>
                    <i class="bi" :class="open9 ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                </div>
                <div x-show="open9" x-transition class="mt-3 text-muted small">
                    <strong>Bisa.</strong> Gudangku menyediakan fitur pembuatan berita acara otomatis berdasarkan data barang masuk dan barang keluar.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection