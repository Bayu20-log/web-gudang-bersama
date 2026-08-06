@extends('layouts.app')


@section('content')
<div class="container">
    <h4>Edit Satuan</h4>


    {{-- Flash error --}}
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif


    {{-- Validasi error --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


    <form method="POST" action="{{ route('satuan.update', $satuan->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="nama_satuan" class="form-label">Nama Satuan <span class="text-danger">*</span></label>
            <input type="text" name="nama_satuan"
                   class="form-control @error('nama_satuan') is-invalid @enderror"
                   value="{{ old('nama_satuan', $satuan->nama_satuan) }}" required>
            @error('nama_satuan')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('satuan.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>
@endsection
