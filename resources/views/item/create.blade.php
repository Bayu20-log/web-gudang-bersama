

 @extends('layouts.app')

@section('content')
<div class="container">
    <h4>Tambah Item</h4>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('item.store') }}" method="POST" enctype="multipart/form-data">
        @csrf



        {{-- Nama Barang --}}
        <div class="mb-3">
            <label for="nama_barang">Nama Barang <span class="text-danger">*</span></label>
            <input type="text" name="nama_barang" class="form-control" value="{{ old('nama_barang') }}" required>
        </div>

        {{-- Nama Barang --}}
        <div class="mb-3">
            <label for="harga_dasar">harga dasar <span class="text-danger">*</span></label>
            <input type="number" name="harga_dasar" class="form-control" value="{{ old('harga_dasar') }}" required>
        </div>
        {{-- Kategori --}}
        <div class="mb-3">
            <label for="id_kategori">Kategori <span class="text-danger">*</span></label>
            <select name="id_kategori" class="form-control" required>
                <option value="">-- Pilih Kategori --</option>
                @foreach ($kategori as $k)
                    <option value="{{ $k->id }}" {{ old('id_kategori') == $k->id ? 'selected' : '' }}>
                        {{ $k->kategori }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Satuan --}}
        <div class="mb-3">
            <label for="id_satuan">Satuan <span class="text-danger">*</span></label>
            <select name="id_satuan" class="form-control" required>
                <option value="">-- Pilih Satuan --</option>
                @foreach ($satuan as $s)
                    <option value="{{ $s->id }}" {{ old('id_satuan') == $s->id ? 'selected' : '' }}>
                        {{ $s->nama_satuan }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Stok Minimum --}}
        <div class="mb-3">
            <label for="stok_minimum">Stok Minimum <span class="text-danger">*</span></label>
            <input type="number" name="stok_minimum" class="form-control" value="{{ old('stok_minimum', $item->stok_minimum ?? '') }}" required>
        </div>


        {{-- Deskripsi --}}
        <div class="mb-3">
            <label for="deskripsi">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="2">{{ old('deskripsi') }}</textarea>
        </div>

        {{-- Foto --}}
        <div class="mb-3">
            <label for="foto">Foto</label>
            <input type="file" name="foto" class="form-control @error('foto') is-invalid @enderror">
            @error('foto')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</div>
@endsection