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
        }
    }
</style>
<div class="container py-5">
    {{-- Kotak biru lembut --}}
    <div class="rounded p-4 mb-5" style="background-color: #edd078; color: #070707;">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h6 class="fw-bold" style="color: #003322;">Profil RAKSAKTI</h6>
                <h2 class="fw-bold">RAKSAKTI</h2>
                <p class="fst-italic">Kelola Stok Barang dengan Cerdas, Cepat, dan Akurat.</p>
                <p>
                    RAKSAKTI adalah sistem informasi pergudangan berbasis web yang dirancang
                    untuk memudahkan pengelolaan stok barang secara real-time, multi-user, dan
                    terstruktur. Tidak perlu instalasi tambahan, cukup buka browser dan login.
                </p>
                <!--Tombol -->
                <div class="d-flex gap-3">
                    <a href="{{ route('profil') }}" class="btn btn-outline-success" style="color: #162f26">Selengkapnya</a>
                </div>
            </div>  
            <div class="col-md-4 text-center">
                    <img src="{{ asset('images/gudangku 2.png') }}" 
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
                <p class="text-muted">
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
                            <h5 class="fw-bold mb-2 text-primary">Superadmin</h5>
                            <p class="text-muted">
                                Pengguna Superadmin mengelola akun pengguna dan memiliki akses untuk melihat laporan-laporan penting.
                            </p>
                            <a href="{{ route('superadmin') }}" class="btn btn-outline-primary">Lihat Detail</a>
                        </div>
                    </div>
                </div>

            {{-- Gudang --}}
            <div class="col-md-4">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body">
                        <img src="{{ asset('images/gudang.png') }}" alt="Gudang" style="height:70px;" class="mb-3">
                        <h5 class="fw-bold mb-2 text-success">Gudang</h5>
                        <p class="text-muted">
                            Pengguna Gudang bertugas mencatat dan mengelola aktivitas keluar masuk barang, serta menyusun berita acara.
                        </p>
                        <a href="{{ route('gudang') }}" class="btn btn-outline-success">Lihat Detail</a>
                    </div>
                </div>
            </div>

            {{-- Viewer --}}
            <div class="col-md-4">
                <div class="card h-100 text-center border-0 shadow-sm">
                    <div class="card-body">
                        <img src="{{ asset('images/viewer.png') }}" alt="Viewer" style="height:70px;" class="mb-3">
                        <h5 class="fw-bold mb-2 text-info">Viewer</h5>
                        <p class="text-muted">
                            Pengguna Viewer hanya memiliki hak akses untuk melihat laporan tanpa dapat mengubah data.
                        </p>
                        <a href="{{ route('viewer') }}" class="btn btn-outline-info">Lihat Detail</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
</div>
@endsection