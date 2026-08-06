@extends('layouts.app')


@section('content')
<div class="py-5 text-white" style="background: linear-gradient(135deg, #0d6efd, #6f42c1);">
    <div class="container text-center">
        <h1 class="display-5 fw-bold">Kenali Fitur Setiap Role</h1>
        <p class="lead">Pelajari apa saja yang bisa dilakukan oleh pengguna berdasarkan peran mereka di Sistem Informasi Gudang.</p>
    </div>
</div>


<div class="container my-5">
    <div class="row g-4">
        {{-- Role Viewer --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <span class="badge bg-primary">Role: Viewer</span>
                    </h4>
                    <div class="accordion" id="accordionViewer">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="viewer-heading1">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#viewer-collapse1" aria-expanded="false" aria-controls="viewer-collapse1">
                                    📊 Melihat laporan stok barang
                                </button>
                            </h2>
                            <div id="viewer-collapse1" class="accordion-collapse collapse" aria-labelledby="viewer-heading1" data-bs-parent="#accordionViewer">
                                <div class="accordion-body">
                                    Viewer dapat mengakses laporan ketersediaan stok barang secara real-time untuk mengetahui jumlah dan posisi barang yang tersedia di gudang.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- Role Gudang --}}
        <div class="col-md-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <h4 class="card-title mb-3">
                        <span class="badge bg-success">Role: Gudang</span>
                    </h4>
                    <div class="accordion" id="accordionGudang">
                        @php
                            $gudangFeatures = [
                                ['📥 Mencatat Barang Masuk', 'Fitur ini memungkinkan staf gudang mencatat setiap barang yang masuk ke dalam sistem dengan cepat dan akurat.'],
                                ['📤 Mencatat Barang Keluar', 'Staf gudang dapat mencatat barang yang keluar dari gudang, termasuk jumlah dan tujuannya, untuk pelacakan yang lebih efisien.'],
                                ['📊 Melihat Laporan Stok Barang', 'Laporan stok memberikan gambaran jumlah stok terkini untuk setiap barang yang disimpan di gudang.'],
                                ['📈 Melihat Laporan Arus Barang', 'Fitur ini menyajikan data arus masuk dan keluar barang secara periodik, sehingga memudahkan pemantauan.'],
                                ['📝 Membuat berita acara', 'Staf gudang bisa membuat berita acara resmi sebagai dokumentasi kegiatan seperti serah terima atau pelaporan.'],
                                ['💼 Melihat Laporan Aset', 'Menampilkan data aset tetap dan nilai persediaan yang ada di gudang untuk keperluan audit dan manajemen.'],
                                ['💰 Melihat Laporan Omzet', 'Laporan omzet menyediakan ringkasan nilai barang keluar sebagai gambaran omzet harian atau bulanan.']
                            ];
                        @endphp


                        @foreach($gudangFeatures as $index => [$title, $description])
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="gudang-heading{{ $index }}">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#gudang-collapse{{ $index }}" aria-expanded="false" aria-controls="gudang-collapse{{ $index }}">
                                        {{ $title }}
                                    </button>
                                </h2>
                                <div id="gudang-collapse{{ $index }}" class="accordion-collapse collapse" aria-labelledby="gudang-heading{{ $index }}" data-bs-parent="#accordionGudang">
                                    <div class="accordion-body">
                                        {{ $description }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>


    {{-- Tombol kembali ke login --}}
    <div class="text-center mt-5">
        <a href="{{ route('login') }}" class="btn btn-outline-primary px-4 py-2 shadow-sm">
            ← Kembali ke Halaman Login
        </a>
    </div>
</div>
@endsection
