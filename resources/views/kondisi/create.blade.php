@extends('layouts.app')


@section('content')
<div class="container">
    <h4>Tambah Kondisi</h4>


    {{-- Flash error (dari session) --}}
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif


    {{-- Error Validasi --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form method="POST" action="{{ route('kondisi.store') }}">
        @csrf


        <div class="mb-3">
            <label for="nama_kondisi" class="form-label">Nama Kondisi <span class="text-danger">*</span></label>
            <input type="text" name="nama_kondisi" class="form-control @error('nama_kondisi') is-invalid @enderror"
                   value="{{ old('nama_kondisi') }}" required>
            @error('nama_kondisi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror" rows="3"
                      placeholder="Tambahkan deskripsi (opsional)">{{ old('deskripsi') }}</textarea>
            @error('deskripsi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <button type="submit" class="btn btn-primary">Simpan</button>
        <a href="{{ route('kondisi.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
</div>
@endsection


