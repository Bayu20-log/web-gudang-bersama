@extends('layouts.landing')

@section('content')
<style>
    .logo-gudangku {
        height: 180px;
        width: 180px;
        object-fit: cover;
        border-radius: 45%;
    }

    @media (max-width: 768px) {
        .logo-gudangku {
            padding-top: 20px;
            height: 120px;
            width: 120px;
        }
        .container.py-5 { padding-top: 1.75rem !important; padding-bottom: 1.75rem !important; }
        .hero-band-brand.p-4 { padding: 1.25rem !important; }
        #fitur.py-5, section#fitur { padding-top: 1.5rem !important; padding-bottom: 0.5rem !important; }
        .text-center.mb-5 { margin-bottom: 1.5rem !important; }
        .row.g-4 { row-gap: 0.9rem !important; }
        .card-body { padding: 1.1rem !important; }
    }
</style>
<div class="container py-5">
    {{-- Kotak hero bertema gudang --}}
    <div class="rounded p-4 mb-5 hero-band-brand">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h6 class="fw-bold text-brand mb-1">Profil RAKSAKTI</h6>
                <h2 class="fw-bold mb-2" style="color:#1e293b">RAKSAKTI</h2>
                <p class="fst-italic mb-2" style="color:#78716c">Kelola Stok Barang dengan Cerdas, Cepat, dan Akurat.</p>

                {{-- Versi ringkas khusus HP --}}
                <p class="d-md-none mb-3" style="color:#57534e; font-size:0.95rem;">
                    Sistem pergudangan berbasis web — real-time, multi-user, tanpa instalasi.
                </p>
                {{-- Versi lengkap khusus desktop/tablet --}}
                <p class="d-none d-md-block" style="color:#57534e">
                    RAKSAKTI adalah sistem informasi pergudangan berbasis web yang dirancang
                    untuk memudahkan pengelolaan stok barang secara real-time, multi-user, dan
                    terstruktur. Tidak perlu instalasi tambahan, cukup buka browser dan login.
                </p>
                <!--Tombol -->
                <div class="d-flex gap-3">
                    <a href="{{ route('profil') }}" class="btn btn-warning fw-bold" style="background-color: #f97316; border-color: #f97316; color: #fff;">Selengkapnya</a>
                </div>
            </div>  
            <div class="col-md-4 text-center">
                    <img src="{{ asset('images/logo_or.png') }}" 
                        alt="Logo" 
                        class="img-fluid logo-gudangku">
                </div>

        </div>
    </div>

    {{-- Judul Fitur --}}
    <section id="fitur" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold" id="fitur">Fitur GudangKu</h2>
                {{-- Versi ringkas khusus HP --}}
                <p class="text-muted d-md-none px-2">
                    3 peran pengguna: <strong>Superadmin</strong>, <strong>Gudang</strong>, dan <strong>Viewer</strong>.
                </p>
                {{-- Versi lengkap khusus desktop/tablet --}}
                <p class="text-muted d-none d-md-block">
                    Sistem ini mendukung pengelolaan barang secara terstruktur melalui pembagian peran: 
                    <strong>Superadmin</strong>, <strong>Gudang</strong>, dan <strong>Viewer</strong>. 
                    Masing-masing peran memiliki akses fitur yang sesuai dengan tanggung jawabnya.
                </p>
            </div>

            {{-- Kartu Fitur --}}
            <div class="row justify-content-center g-4">
                {{-- Superadmin --}}
                <div class="col-md-4">
                    <div class="card h-100 text-center border-0 shadow-sm">
                        <div class="card-body">
                            <img src="{{ asset('images/superadmin1.png') }}" alt="Superadmin" style="height:70px;" class="mb-3">
                            <h5 class="fw-bold mb-2" style="color:#1e293b">Superadmin</h5>
                            <p class="text-muted small d-md-none mb-2">Kelola akun &amp; lihat laporan.</p>
                            <p class="text-muted d-none d-md-block">
                                Pengguna Superadmin mengelola akun pengguna dan memiliki akses untuk melihat laporan-laporan penting.
                            </p>
                            <a href="{{ route('superadmin') }}" class="btn btn-outline-dark-slate">Lihat Detail</a>
                        </div>
                    </div>
                </div>

            {{-- Gudang --}}
            <div class="col-md-4">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body">
                        <img src="{{ asset('images/gudang.png') }}" alt="Gudang" style="height:70px;" class="mb-3">
                        <h5 class="fw-bold mb-2 text-brand">Gudang</h5>
                        <p class="text-muted small d-md-none mb-2">Catat barang masuk &amp; keluar.</p>
                        <p class="text-muted d-none d-md-block">
                            Pengguna Gudang bertugas mencatat dan mengelola aktivitas keluar masuk barang, serta menyusun berita acara.
                        </p>
                        <a href="{{ route('gudang') }}" class="btn btn-outline-brand">Lihat Detail</a>
                    </div>
                </div>
            </div>

            {{-- Viewer --}}
            <div class="col-md-4">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body">
                        <img src="{{ asset('images/viewer.png') }}" alt="Viewer" style="height:70px;" class="mb-3">
                        <h5 class="fw-bold mb-2" style="color:#2563eb">Viewer</h5>
                        <p class="text-muted small d-md-none mb-2">Lihat laporan saja.</p>
                        <p class="text-muted d-none d-md-block">
                            Pengguna Viewer hanya memiliki hak akses untuk melihat laporan tanpa dapat mengubah data.
                        </p>
                        <a href="{{ route('viewer') }}" class="btn" style="border:1px solid #2563eb; color:#2563eb;">Lihat Detail</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
</div>
@endsection