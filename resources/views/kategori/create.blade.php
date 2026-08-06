@extends('layouts.app')

@section('content')
<div class="container">
    <h4 class="mb-4">Tambah Kategori</h4>

    {{-- Flash error dari session --}}
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
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

    <form method="POST" action="{{ route('kategori.store') }}">
        @csrf

        <div class="mb-3">
            <label for="kategori" class="form-label">
                Nama Kategori <span class="text-danger">*</span>
            </label>
            <input type="text" name="kategori"
                   class="form-control @error('kategori') is-invalid @enderror"
                   value="{{ old('kategori') }}"
                   placeholder="Masukkan nama kategori..." required>
            @error('kategori')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi</label>
            <textarea name="deskripsi"
                      class="form-control @error('deskripsi') is-invalid @enderror"
                      rows="3"
                      placeholder="Deskripsi kategori (opsional)">{{ old('deskripsi') }}</textarea>
            @error('deskripsi')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-start gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Simpan
            </button>
            <a href="{{ route('kategori.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </form>
</div>
@endsection
