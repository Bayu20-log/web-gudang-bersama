@extends('layouts.app')


@section('content')

<div class="container">
    <h4>Detail Barang</h4>


    <div class="card mb-4">
        <div class="row g-0">
            <div class="col-md-4 text-center p-3">
                @if($item->foto)
                    <img src="{{ asset('storage/' . $item->foto) }}" class="img-fluid rounded" alt="{{ $item->nama_barang }}">
                @else
                    <span class="text-muted fst-italic">(Tidak ada foto)</span>
                @endif
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h5 class="card-title">{{ $item->nama_barang }}</h5>
                    <p class="card-text"><strong>Kode Barang:</strong> {{ $item->kode_barang }}</p>
                    
                    <p class="card-text"><strong>Harga Dasar:</strong>Rp {{ number_format($item['harga_dasar'], 0, ',', '.') }}
                    <p class="card-text"><strong>Kategori:</strong> {{ $item->kategori->kategori }}</p>
                    
                    <p class="card-text"><strong>Catatan:</strong> {{ $item->deskripsi ?? '-' }}</p>
                    
                    
                    <p class="card-text"><strong>Satuan:</strong> {{ $item->satuan->nama_satuan ?? '-' }}</p>
                    

                    <p class="card-text"><small class="text-muted">Ditambahkan pada {{ $item->created_at }}</small></p>


                    <a href="{{ route('item.index') }}" class="btn btn-secondary mt-2">← Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
