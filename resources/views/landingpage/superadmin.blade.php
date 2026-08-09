@extends('layouts.landing')

@section('content')
<div class="container" data-aos="fade-down" data-aos-delay="100">
    <!-- Breadcrumb -->
    <div class="pt-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Fitur / Superadmin</li>
            </ol>
        </nav>
    </div>

    <!-- Kotak konten biru lembut tanpa garis tepi -->
    <div class="rounded p-4 shadow-sm bg-opacity-10" style="background-color: #79b687;" data-aos="fade-up" data-aos-delay="200">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold" data-aos="fade-down">👑 Peran Superadmin</h2>
                <p class="text-muted" data-aos="fade-down" data-aos-delay="100">
                    Pengguna Superadmin mengelola akun pengguna dan memiliki akses untuk melihat laporan-laporan penting.
                </p>
            </div>

        <!-- Grid Desktop -->
        <div class="row row-cols-1 row-cols-md-2 g-4 card-grid-desktop">
            @foreach([
                ['Dashboard', 'Menyajikan informasi ringkasan seluruh sistem, termasuk statistik pengguna dan data gudang.', 'bi-bar-chart-line-fill', 'primary'],
                ['Kelola User (Create, Read, Update)', 'Mengatur data akun pengguna, termasuk peran dan status aktif.', 'bi-people-fill', 'info'],
                ['Laporan Stok Barang', 'Melihat stok barang yang tersedia di seluruh gudang.', 'bi-box-seam', 'warning']
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

        <!-- Accordion Mobile -->
        <div class="accordion accordion-desktop" id="accordionSuperadmin">
            @foreach([
                ['Dashboard', 'Menyajikan informasi ringkasan seluruh sistem, termasuk statistik pengguna dan data gudang.'],
                ['Kelola User (Create, Read, Update)', 'Mengatur data akun pengguna, termasuk peran dan status aktif.'],
                ['Laporan Stok Barang', 'Melihat stok barang yang tersedia di seluruh gudang.']
            ] as $index => [$title, $desc])
            <div class="accordion-item" data-aos="fade-up">
                <h2 class="accordion-header" id="heading{{ $index }}">
                    <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $index }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="collapse{{ $index }}">
                        {{ $title }}
                    </button>
                </h2>
                <div id="collapse{{ $index }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="heading{{ $index }}" data-bs-parent="#accordionSuperadmin">
                    <div class="accordion-body">
                        {{ $desc }}
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
@endsection