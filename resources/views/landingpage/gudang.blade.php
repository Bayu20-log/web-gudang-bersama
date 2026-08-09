@extends('layouts.landing')

@section('content')
<div class="container" data-aos="fade-down" data-aos-delay="100">
    <!-- Breadcrumb -->
    <div class="pt-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Fitur / Gudang</li>
            </ol>
        </nav>
    </div>

    <!-- Kotak konten biru lembut tanpa garis tepi -->
    <div class="rounded p-4 shadow-sm bg-opacity-10" style="background-color: #79b687;" data-aos="fade-up" data-aos-delay="200">

        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold" data-aos="fade-down">📦 Peran Gudang</h2>
                <p class="text-muted" data-aos="fade-down" data-aos-delay="100">
                    Pengguna Gudang bertugas mencatat dan mengelola aktivitas keluar masuk barang, serta menyusun berita acara.
                </p>
            </div>

    <!-- Versi Grid untuk Desktop -->
    <div class="row row-cols-1 row-cols-md-2 g-4 card-grid-desktop">
        @foreach([
            ['Dashboard', 'Menampilkan ringkasan data stok, arus barang, dan notifikasi penting (misalnya stok minimum).', 'bi-bar-chart-line-fill', 'primary'],
            ['Barang Masuk (Create & Read)', 'Input data lengkap: lokasi, pemasok, tanggal, QR Code. Lihat histori barang masuk.', 'bi-box-arrow-in-down', 'success'],
            ['Barang Keluar (Create & Read)', 'Catat barang keluar dan tujuan. Lihat histori pengeluaran.', 'bi-box-arrow-up', 'danger'],
            ['Laporan Stok Barang', 'Tampilkan stok berdasarkan lokasi, kategori, dan kondisi.', 'bi-clipboard-data', 'warning'],
            ['Laporan Arus Barang', 'Riwayat lengkap masuk dan keluar. Unduh PDF/Excel.', 'bi-arrow-left-right', 'info'],
            ['Notifikasi Stok', 'Peringatan saat stok minimum atau barang hampir kadaluarsa.', 'bi-bell-fill', 'danger'],
            ['QR Code Otomatis', 'Barang masuk diberi kode unik untuk pemindaian cepat.', 'bi-upc-scan', 'success'],
            ['Berita Acara', 'Membuat dan meninjau berita acara transaksi.', 'bi-journal-text', 'primary'],
            ['Laporan Aset', 'Menampilkan daftar seluruh aset beserta nilai, lokasi, dan kondisi untuk pemantauan kekayaan inventaris.', 'bi-building', 'secondary'],
            ['Laporan Omzet', 'Rekap nilai barang keluar sebagai estimasi omzet berdasarkan kategori, periode, dan tujuan.', 'bi-cash-coin', 'success']
        ] as [$title, $desc, $icon, $color])
        <div class="col" data-aos="fade-up">
            <div class="card h-100 shadow-sm border-0 hover-shadow" style="transition: transform 0.3s;">
                <div class="card-body text-start">
                    <h5 class="card-title">
                        <i class="bi {{ $icon }} me-2 text-{{ $color }}"></i>{{ $title }}
                    </h5>
                    <p class="card-text">{{ $desc }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Versi Accordion untuk Mobile -->
    <div class="accordion accordion-desktop" id="fiturAccordion">
        @foreach([
            ['Dashboard', 'Menampilkan ringkasan data stok, arus barang, dan notifikasi penting (misalnya stok minimum).'],
            ['Barang Masuk (Create & Read)', 'Input data lengkap: lokasi, pemasok, tanggal, QR Code. Lihat histori barang masuk.'],
            ['Barang Keluar (Create & Read)', 'Catat barang keluar dan tujuan. Lihat histori pengeluaran.'],
            ['Laporan Stok Barang', 'Tampilkan stok berdasarkan lokasi, kategori, dan kondisi.'],
            ['Laporan Arus Barang', 'Riwayat lengkap masuk dan keluar. Unduh PDF/Excel.'],
            ['Notifikasi Stok', 'Peringatan saat stok minimum atau barang hampir kadaluarsa.'],
            ['QR Code Otomatis', 'Barang masuk diberi kode unik untuk pemindaian cepat.'],
            ['Berita Acara', 'Membuat dan meninjau berita acara transaksi.']
        ] as $index => [$title, $desc])
        <div class="accordion-item" data-aos="fade-up">
            <h2 class="accordion-header" id="heading{{ $index }}">
                <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
                    {{ $title }}
                </button>
            </h2>
            <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="heading{{ $index }}" data-bs-parent="#fiturAccordion">
                <div class="accordion-body">
                    {{ $desc }}
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection