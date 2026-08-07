@extends('layouts.app')


@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">


        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif


        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif


        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h4 class="mb-4 fw-bold text-primary"><span style="color: #79b687">Pro<span style="color: #003322">file</span></h4>


                <!-- FOTO, NAMA & ICON BELL -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center">
                        <img src="{{ $user->photo ? asset('storage/'.$user->photo) : asset('default.jpg') }}"
                             class="rounded-circle me-3" style="width:70px;height:70px;object-fit:cover;">
                        <div>
                            <h5 class="mb-0">{{ $user->name }}</h5>
                            <small class="text-muted">{{ ucfirst($user->role) }}</small>
                        </div>
                    </div>
                    @if(Auth::user()->role === 'gudang')
                        <a href="{{ route('notifications.index') }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-bell"></i>
                        </a>
                    @endif
                </div>


                <!-- FORM -->
                <form method="POST" action="{{ route('profile.update', $user->id) }}" enctype="multipart/form-data" class="row g-3">
                    @csrf
                    @method('PUT')


                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap</label>
                        <input name="name" type="text" class="form-control"
                               value="{{ old('name', $user->name) }}" required>
                    </div>


                    <div class="col-md-6">
                        <label class="form-label">Username</label>
                        <input name="username" type="text" class="form-control"
                               value="{{ old('username', $user->username) }}" required>
                    </div>


                    <div class="col-md-6">
                        <label class="form-label">Email</label>
                        <input name="email" type="email" class="form-control"
                               value="{{ old('email', $user->email) }}" required>
                    </div>


                    <div class="col-md-6">
                        <label class="form-label">Telepon</label>
                        <input name="phone" type="text" class="form-control"
                               value="{{ old('phone', $user->phone) }}">
                    </div>


                    <div class="col-md-6">
                        <label class="form-label">Posisi</label>
                        <input name="position" type="text" class="form-control"
                               value="{{ old('position', $user->position) }}">
                    </div>


                    <div class="col-6">
                        <label class="form-label d-block">Foto (optional)</label>
                        <input name="photo" type="file" class="form-control">
                        <small class="text-muted">Biarkan kosong jika tidak mengganti.</small>
                    </div>


                    <div class="col-12 mt-4">
                        <h5 class="fw-bold">Ganti Password</h5>
                    </div>


                    <div class="col-md-6 position-relative">
                        <label class="form-label">Password Lama</label>
                        <input type="password" name="current_password" id="current_password" class="form-control" autocomplete="current-password">
                        <i class="bi bi-eye-slash toggle-password" toggle="#current_password"></i>
                    </div>


                    <div class="col-md-6 position-relative">
                        <label class="form-label">Password Baru</label>
                        <input type="password" name="new_password" id="new_password" class="form-control" autocomplete="new-password">
                        <i class="bi bi-eye-slash toggle-password" toggle="#new_password"></i>
                    </div>


                    <div class="col-12 mt-3">
                        <button class="btn btn-primary w-100" type="submit">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>


                <div class="mt-4">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- Bootstrap Icons --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">


{{-- JS: Toggle Password --}}
<style>
    .toggle-password {
        position: absolute;
        top: 36px;
        right: 15px;
        cursor: pointer;
        font-size: 1.1rem;
        color: #666;
    }
</style>


<script>
    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', function () {
            const input = document.querySelector(this.getAttribute('toggle'));
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    });
</script>
@endsection


