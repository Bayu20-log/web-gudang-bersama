@extends('layouts.app')


@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Edit User</h3>


    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif


    @if ($errors->any())
        <div class="alert alert-danger">Silakan periksa kembali input Anda.</div>
    @endif


    <form action="{{ route('user.update', $user->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')


        <div class="mb-3">
            <label class="form-label">Nama:</label>
            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $user->name) }}" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <div class="mb-3">
            <label class="form-label">Username:</label>
            <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                   value="{{ old('username', $user->username) }}" required>
            @error('username')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Email:</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Telepon:</label>
                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                       value="{{ old('phone', $user->phone) }}">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>


        <div class="row mb-3">
            <div class="col-md-6 position-relative">
                <label class="form-label">Password Baru (kosongkan jika tidak diubah):</label>
                <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror">
                <i class="bi bi-eye-slash toggle-password" toggle="#password" title="Tampilkan/Sembunyikan Password"></i>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6 position-relative">
                <label class="form-label">Konfirmasi Password:</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
                <i class="bi bi-eye-slash toggle-password" toggle="#password_confirmation" title="Tampilkan/Sembunyikan Password"></i>
            </div>
        </div>


        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Role:</label>
                <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                    <option value="superadmin" {{ $user->role == 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                    <option value="gudang" {{ $user->role == 'gudang' ? 'selected' : '' }}>Gudang</option>
                    <option value="viewer" {{ $user->role == 'viewer' ? 'selected' : '' }}>Viewer</option>
                </select>
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">Posisi:</label>
                <input type="text" name="position" class="form-control @error('position') is-invalid @enderror"
                       value="{{ old('position', $user->position) }}">
                @error('position')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>


        <div class="mb-3">
    <label class="form-label">Status Kepegawaian:</label>
    <select name="status" class="form-select @error('status') is-invalid @enderror">
        <option value="">-- Pilih Status Kepegawaian --</option>
        <option value="Pegawai" {{ old('status', $user->status) == 'Pegawai' ? 'selected' : '' }}>Pegawai</option>
        <option value="Kontrak" {{ old('status', $user->status) == 'Kontrak' ? 'selected' : '' }}>Kontrak</option>
        <option value="Outsourcing" {{ old('status', $user->status) == 'Outsourcing' ? 'selected' : '' }}>Outsourcing</option>
    </select>
    @error('status')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>



        <div class="mb-3">
            <label class="form-label">Upload Foto (Opsional):</label>
            <input type="file" name="photo" class="form-control @error('photo') is-invalid @enderror">
            @error('photo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <div class="mb-3">
            <label class="form-label">Catatan:</label>
            <textarea name="note" rows="3" class="form-control @error('note') is-invalid @enderror">{{ old('note', $user->note) }}</textarea>
            @error('note')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>


        <div class="text-end">
            <button type="submit" class="btn btn-primary px-4">Update</button>
        </div>
    </form>
</div>


{{-- Bootstrap Icons --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">


{{-- Style & Script for Toggle Password --}}
<style>
    .toggle-password {
        position: absolute;
        top: 36px;
        right: 30px;
        cursor: pointer;
        font-size: 1.1rem;
        color: #666;
        z-index: 2;
    }
</style>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.toggle-password').forEach(icon => {
            icon.addEventListener('click', function () {
                const input = document.querySelector(this.getAttribute('toggle'));
                if (input) {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    this.classList.toggle('bi-eye');
                    this.classList.toggle('bi-eye-slash');
                }
            });
        });
    });
</script>
@endsection
