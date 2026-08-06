@extends('layouts.landing')

@section('title', 'Galeri')

@section('content')
<div class="container" data-aos="fade-down" data-aos-delay="100">
    <!-- Breadcrumb -->
    <div class="pt-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Galeri</li>
            </ol>
        </nav>
    </div>

    <!-- Kotak konten biru lembut tanpa garis tepi -->
    <div class="rounded p-4 shadow-sm bg-primary bg-opacity-10" data-aos="fade-up" data-aos-delay="200">
        <h2 class="text-center fw-bold mb-4">Galeri</h2>
        <p class="text-center fs-5 text-muted mb-4">
            Tampilan Antarmuka GudangKu
        </p>
        <div class="container py-5">

    <!-- Tabs Navigation -->
    <ul class="nav nav-pills justify-content-center mb-4" id="galeriTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="semua-tab" data-bs-toggle="pill" data-bs-target="#semua" type="button" role="tab">Semua</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="dashboard-tab" data-bs-toggle="pill" data-bs-target="#dashboard" type="button" role="tab">Dashboard</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="fitur-tab" data-bs-toggle="pill" data-bs-target="#fitur" type="button" role="tab">Fitur</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="aktivitas-tab" data-bs-toggle="pill" data-bs-target="#aktivitas" type="button" role="tab">Aktivitas</button>
        </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content" id="galeriTabsContent">
        @foreach ([
            'semua' => ['superadmin.png', 'gudang.png', 'viewer.png'],
            'dashboard' => ['dashboard1.png', 'dashboard2.png'],
            'fitur' => ['fitur1.png', 'fitur2.png'],
            'aktivitas' => ['aktivitas1.jpg', 'aktivitas2.jpg']
        ] as $tab => $images)
        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $tab }}" role="tabpanel">
            <div class="row g-4 mt-2">
                @foreach ($images as $img)
                <div class="col-sm-6 col-md-4 col-lg-3" data-aos="zoom-in">
                    <div class="card shadow-sm border-0 h-100">
                        <img src="{{ asset('images/galeri/' . $img) }}" class="card-img-top" alt="Galeri {{ $tab }}">
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection