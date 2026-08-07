@extends('layouts.landing')

@section('content')
<div class="container" data-aos="fade-down" data-aos-delay="100">
    <!-- Breadcrumb -->
    <div class="pt-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Fitur / Viewer</li>
            </ol>
        </nav>
    </div>

    <!-- Kotak konten biru lembut tanpa garis tepi -->
    <div class="rounded p-4 shadow-sm bg-opacity-10" style="background-color: #79b687;" data-aos="fade-up" data-aos-delay="200">
        <div class="container py-5">
            <div class="text-center mb-5">
                <h2 class="fw-bold" data-aos="fade-down">👁️ Peran Viewer</h2>
                <p class="text-muted" data-aos="fade-down" data-aos-delay="100">
                    Pengguna Viewer hanya memiliki hak akses untuk melihat laporan tanpa dapat mengubah data.
                </p>
            </div>

        <!-- Desktop View (Card) -->
        <div class="card shadow-sm border-0 d-none d-md-block mx-auto mb-4" style="max-width: 800px;" data-aos="fade-up">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-clipboard-data text-warning me-2"></i>Laporan Stok Barang (Read)
                </h5>
                <p class="card-text">
                    Melihat data stok barang untuk keperluan monitoring, audit, atau pengecekan stok.
                </p>
            </div>
        </div>

        <!-- Mobile View (Accordion) -->
        <div class="accordion d-block d-md-none mx-auto" id="accordionViewer" style="max-width: 800px;">
            <div class="accordion-item" data-aos="fade-up">
                <h2 class="accordion-header" id="headingViewer">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseViewer" aria-expanded="true" aria-controls="collapseViewer">
                        <i class="bi bi-clipboard-data text-warning me-2"></i>Laporan Stok Barang (Read)
                    </button>
                </h2>
                <div id="collapseViewer" class="accordion-collapse collapse show" aria-labelledby="headingViewer" data-bs-parent="#accordionViewer">
                    <div class="accordion-body">
                        Melihat data stok barang untuk keperluan monitoring, audit, atau pengecekan stok.
                    </div>
                </div>
            </div>
        </div>
</div>
@endsection