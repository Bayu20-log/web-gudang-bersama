@extends('layouts.app')

@section('content')
<div class="container">
    <h4>Edit Item</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('item.update', $item->kode_barang) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        
        {{-- Nama Barang --}}
        <div class="mb-3">
            <label for="nama_barang">Nama Barang</label>
            <input type="text" name="nama_barang" class="form-control" value="{{ old('nama_barang', $item->nama_barang) }}" required>
        </div>
        {{-- Nama Barang --}}
        <div class="mb-3">
            <label for="harga_dasar">harga dasar</label>
            <input type="number" name="harga_dasar" class="form-control" value="{{ old('harga_dasar', $item->harga_dasar)}}" required>
        </div>
        {{-- Kategori --}}
        <div class="mb-3">
            <label for="id_kategori">Kategori</label>
            <select name="id_kategori" class="form-control" required>
                @foreach ($kategori as $k)
                    <option value="{{ $k->id }}" {{ old('id_kategori', $item->id_kategori) == $k->id ? 'selected' : '' }}>
                        {{ $k->kategori }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Satuan --}}
        <div class="mb-3">
            <label for="id_satuan">Satuan</label>
            <select name="id_satuan" class="form-control" required>
                @foreach ($satuan as $s)
                    <option value="{{ $s->id }}" {{ old('id_satuan', $item->id_satuan) == $s->id ? 'selected' : '' }}>
                        {{ $s->nama_satuan }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Stok Minimum --}}
        <div class="mb-3">
            <label for="stok_minimum">Stok Minimum</label>
            <input type="number" name="stok_minimum" class="form-control" value="{{ old('stok_minimum', $item->stok_minimum ?? '') }}" required>
        </div>
        

        

        {{-- Catatan / Deskripsi --}}
        <div class="mb-3">
            <label for="deskripsi">Catatan</label>
            <textarea name="deskripsi" class="form-control" rows="2">{{ old('deskripsi', $item->deskripsi) }}</textarea>
        </div>

        {{-- Foto --}}
        <div class="mb-3">
            <label for="foto">Foto</label>
            <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror">
            @if ($item->foto)
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $item->foto) }}" alt="Foto" width="100" height="100">
                </div>
            @endif
            @error('foto')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
</div>
@endsection
