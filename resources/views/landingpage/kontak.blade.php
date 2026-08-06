@extends('layouts.landing')

@section('title', 'Kontak Kami')

@section('content')
<div class="container" data-aos="fade-down" data-aos-delay="100">
    <!-- Breadcrumb -->
    <div class="pt-2">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-3">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
                <li class="breadcrumb-item active" aria-current="page">Kontak Kami</li>
            </ol>
        </nav>
    </div>

    <!-- Kotak konten biru lembut tanpa garis tepi -->
    <div class="rounded p-4 shadow-sm bg-primary bg-opacity-10" data-aos="fade-up" data-aos-delay="200">
        <h2 class="text-center fw-bold mb-4">Kontak Kami</h2>
        <p class="text-center fs-5 text-muted mb-4">
            Hubungi kami untuk pertanyaan, dukungan, atau kerjasama lebih lanjut.
        </p>
        <div class="row g-4 justify-content-center">
        <!-- Kolom Email -->
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body text-center">
                    <i class="bi bi-envelope-fill text-primary mb-3" style="font-size: 3rem;"></i>
                    <h5 class="fw-bold">Email Kami</h5>
                    <p class="text-muted">Klik tombol di bawah untuk mengirim email langsung ke kami.</p>
                    <a href="https://mail.google.com/mail/?view=cm&to=admin@gudangku.id" target="_blank" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-send-fill me-2"></i>admin@gudangku.id
                    </a>
                </div>
            </div>
        </div>

        <!-- Kolom WhatsApp -->
        <div class="col-md-6">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body text-center">
                    <i class="bi bi-whatsapp text-success mb-3" style="font-size: 3rem;"></i>
                    <h5 class="fw-bold">WhatsApp Kami</h5>
                    <p class="text-muted">Klik tombol di bawah untuk menghubungi kami melalui WhatsApp.</p>
                    <a href="https://wa.me/6281234567890" target="_blank" class="btn btn-outline-success btn-lg">
                        <i class="bi bi-whatsapp me-2"></i>+62 812 3456 7890
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection