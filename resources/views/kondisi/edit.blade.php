@extends('layouts.app')
@section('content')

<style>
    body { padding-top: 40px; }
    .btn-orange { background-color: #f97316; color: #fff; border: none; transition: 0.3s; }
    .btn-orange:hover { background-color: #ea580c; color: #fff; }
    .btn-outline-dark { border: 1px solid #1e293b; color: #1e293b; transition: 0.3s; background: transparent; font-weight: 600;}
    .btn-outline-dark:hover { background-color: #1e293b; color: #fff; }
    .form-label { font-weight: 600; color: #374151; font-size: 0.95rem; }
    .form-control { background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 15px; transition: 0.3s; }
    .form-control:focus { border-color: #f97316; box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1); background-color: #fff; }
</style>

<div class="container mt-2 mb-5" style="max-width: 700px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0" style="color: #1e293b;">Master Data &rsaquo; Edit Kondisi</h4>
    </div>

    @if(session('error'))
        <div class="alert alert-danger rounded-3 shadow-sm">{{ session('error') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
        <form method="POST" action="{{ route('kondisi.update', $kondisi->id) }}">
            @csrf
            @method('PUT')
            
            <div class="mb-4">
                <label for="nama_kondisi" class="form-label">
                    Nama Kondisi <span class="text-danger">*</span>
                </label>
                <input type="text" name="nama_kondisi" id="nama_kondisi"
                       class="form-control @error('nama_kondisi') is-invalid @enderror"
                       value="{{ old('nama_kondisi', $kondisi->nama_kondisi) }}" required>
                @error('nama_kondisi')
                    <div class="invalid-feedback fw-medium mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-5">
                <label for="deskripsi" class="form-label">
                    Deskripsi <span class="text-muted fw-normal">(Opsional)</span>
                </label>
                <textarea name="deskripsi" id="deskripsi"
                          class="form-control @error('deskripsi') is-invalid @enderror"
                          rows="4">{{ old('deskripsi', $kondisi->deskripsi) }}</textarea>
                @error('deskripsi')
                    <div class="invalid-feedback fw-medium mt-1">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="{{ route('kondisi.index') }}" class="btn btn-outline-dark px-4">Batal</a>
                <button type="submit" class="btn btn-orange px-5 fw-bold"><i class="fa-solid fa-save me-1"></i> Perbarui Kondisi</button>
            </div>
        </form>
    </div>
</div>
@endsection