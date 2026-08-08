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

<div class="container mt-2 mb-5" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0" style="color: #1e293b;">Master Data &rsaquo; Edit Pemasok</h4>
    </div>

    @if(session('error'))
        <div class="alert alert-danger rounded-3 shadow-sm">{{ session('error') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
        <form method="POST" action="{{ route('pemasok.update', $pemasok->id) }}">
            @csrf
            @method('PUT')
            
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <label for="nama_pemasok" class="form-label">Nama Pemasok <span class="text-danger">*</span></label>
                    <input type="text" name="nama_pemasok" class="form-control @error('nama_pemasok') is-invalid @enderror" value="{{ old('nama_pemasok', $pemasok->nama_pemasok) }}" required>
                    @error('nama_pemasok')<div class="invalid-feedback fw-medium mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="nama_pic" class="form-label">Nama PIC <span class="text-danger">*</span></label>
                    <input type="text" name="nama_pic" class="form-control @error('nama_pic') is-invalid @enderror" value="{{ old('nama_pic', $pemasok->nama_pic) }}" required>
                    @error('nama_pic')<div class="invalid-feedback fw-medium mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $pemasok->email) }}" required>
                    @error('email')<div class="invalid-feedback fw-medium mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="no_telepon" class="form-label">Nomor Telepon <span class="text-muted fw-normal">(Opsional)</span></label>
                    <input type="text" name="no_telepon" class="form-control @error('no_telepon') is-invalid @enderror" value="{{ old('no_telepon', $pemasok->no_telepon) }}">
                    @error('no_telepon')<div class="invalid-feedback fw-medium mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="jenis" class="form-label">Jenis <span class="text-muted fw-normal">(Opsional)</span></label>
                    <input type="text" name="jenis" class="form-control @error('jenis') is-invalid @enderror" value="{{ old('jenis', $pemasok->jenis) }}">
                    @error('jenis')<div class="invalid-feedback fw-medium mt-1">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="bergabung_sejak" class="form-label">Bergabung Sejak <span class="text-muted fw-normal">(Opsional)</span></label>
                    <input type="date" name="bergabung_sejak" class="form-control @error('bergabung_sejak') is-invalid @enderror" value="{{ old('bergabung_sejak', $pemasok->bergabung_sejak) }}">
                    @error('bergabung_sejak')<div class="invalid-feedback fw-medium mt-1">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="alamat" class="form-label">Alamat <span class="text-muted fw-normal">(Opsional)</span></label>
                    <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3">{{ old('alamat', $pemasok->alamat) }}</textarea>
                    @error('alamat')<div class="invalid-feedback fw-medium mt-1">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 pt-3 border-top">
                <a href="{{ route('pemasok.index') }}" class="btn btn-outline-dark px-4">Batal</a>
                <button type="submit" class="btn btn-orange px-5 fw-bold"><i class="fa-solid fa-save me-1"></i> Perbarui Pemasok</button>
            </div>
        </form>
    </div>
</div>
@endsection