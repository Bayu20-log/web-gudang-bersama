@extends('layouts.app')


@section('content')
<div class="container">
    <h4>Edit Pemasok</h4>


    {{-- Flash error dari session --}}
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif


    {{-- Error Validasi --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form action="{{ route('pemasok.update', $pemasok->id) }}" method="POST">
        @csrf
        @method('PUT')


        <div class="mb-3">
            <label for="nama_pemasok" class="form-label">Nama Pemasok</label>
            <input type="text" name="nama_pemasok"
                   class="form-control @error('nama_pemasok') is-invalid @enderror"
                   value="{{ old('nama_pemasok', $pemasok->nama_pemasok) }}" required>
            @error('nama_pemasok')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <div class="mb-3">
            <label for="nama_pic" class="form-label">Nama PIC</label>
            <input type="text" name="nama_pic"
                   class="form-control @error('nama_pic') is-invalid @enderror"
                   value="{{ old('nama_pic', $pemasok->nama_pic) }}" required>
            @error('nama_pic')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email"
                   class="form-control @error('email') is-invalid @enderror"
                   value="{{ old('email', $pemasok->email) }}" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <div class="mb-3">
            <label for="no_telepon" class="form-label">Nomor Telepon</label>
            <input type="text" name="no_telepon"
                   class="form-control @error('no_telepon') is-invalid @enderror"
                   value="{{ old('no_telepon', $pemasok->no_telepon) }}">
            @error('no_telepon')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <div class="mb-3">
            <label for="jenis" class="form-label">Jenis</label>
            <input type="text" name="jenis"
                   class="form-control @error('jenis') is-invalid @enderror"
                   value="{{ old('jenis', $pemasok->jenis) }}">
            @error('jenis')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <div class="mb-3">
            <label for="alamat" class="form-label">Alamat</label>
            <textarea name="alamat"
                      class="form-control @error('alamat') is-invalid @enderror"
                      rows="3">{{ old('alamat', $pemasok->alamat) }}</textarea>
            @error('alamat')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <div class="mb-3">
            <label for="bergabung_sejak" class="form-label">Bergabung Sejak</label>
            <input type="date" name="bergabung_sejak"
                   class="form-control @error('bergabung_sejak') is-invalid @enderror"
                   value="{{ old('bergabung_sejak', $pemasok->bergabung_sejak) }}">
            @error('bergabung_sejak')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('pemasok.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
