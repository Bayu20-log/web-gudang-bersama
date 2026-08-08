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
    
    /* Custom Profile Banner & Avatar */
    .profile-banner {
        height: 140px;
        background-color: #1e293b;
        border-bottom: 4px solid #f97316;
    }
    .profile-img-container {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid #fff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        position: relative;
        background-color: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: -65px;
    }
    .profile-img-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .password-wrapper { position: relative; }
    .toggle-password { 
        position: absolute; 
        top: 50%; 
        right: 15px; 
        transform: translateY(-50%); 
        cursor: pointer; 
        color: #94a3b8; 
        font-size: 1.1rem; 
        transition: 0.2s;
        display: none; /* Awalnya disembunyikan sampai user mengetik */
    }
    .toggle-password:hover { color: #1e293b; }
    .cursor-pointer { cursor: pointer; }
</style>

<div class="container mt-2 mb-5" style="max-width: 900px;">
    <!-- Judul & Tombol Kembali di Atas -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0" style="color: #374151;">Akun Saya &rsaquo; Pengaturan Profil</h3>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3 shadow-sm"><i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger rounded-3 shadow-sm"><i class="fa-solid fa-triangle-exclamation me-2"></i>{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger rounded-3 shadow-sm">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="profile-banner"></div>
        
        <div class="card-body px-4 px-md-5 pb-5">
            <form method="POST" action="{{ route('profile.update', $user->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Avatar & Identitas Singkat -->
                <div class="d-flex flex-column flex-md-row align-items-center align-items-md-end mb-4 mb-md-5">
                    <div class="profile-img-container flex-shrink-0 mx-auto mx-md-0 mb-3 mb-md-0">
                        <img id="imagePreview" src="{{ $user->photo ? asset('storage/'.$user->photo) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=f97316&color=fff&size=150' }}" alt="Foto Profil">
                    </div>
                    
                    <div class="flex-grow-1 text-center text-md-start pb-md-2 ms-md-4 mb-3 mb-md-0">
                        <h4 class="fw-bold mb-1" style="color: #1e293b;">{{ $user->name }}</h4>
                        <span class="badge bg-secondary px-3 py-2 rounded-pill text-uppercase">{{ $user->role }}</span>
                    </div>
                    
                    <div class="text-center text-md-start pb-md-2">
                        <label for="photo" class="btn btn-outline-dark fw-bold rounded-pill px-4 mb-0 cursor-pointer shadow-sm">
                            <i class="fa-solid fa-camera me-2"></i> Ganti Foto
                        </label>
                        <input type="file" name="photo" id="photo" class="d-none" accept="image/*" onchange="previewFile(event)">
                    </div>
                </div>

                <hr class="text-muted mb-4 border-2">

                <!-- Formulir Data Pribadi -->
                <h5 class="fw-bold mb-3" style="color: #1e293b;"><i class="fa-solid fa-user-pen me-2" style="color: #f97316;"></i> Data Pribadi</h5>
                <div class="row g-4 mb-5">
                    <div class="col-md-6">
                        <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <input name="name" type="text" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input name="username" type="text" class="form-control" value="{{ old('username', $user->username) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input name="email" type="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Telepon <span class="text-muted fw-normal">(Opsional)</span></label>
                        <input name="phone" type="text" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxx">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Posisi / Jabatan <span class="text-muted fw-normal">(Opsional)</span></label>
                        <input name="position" type="text" class="form-control" value="{{ old('position', $user->position) }}" placeholder="Contoh: Staff Gudang">
                    </div>
                </div>

                <!-- Formulir Keamanan -->
                <h5 class="fw-bold mb-3" style="color: #1e293b;"><i class="fa-solid fa-shield-halved me-2" style="color: #f97316;"></i> Keamanan Akun</h5>
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Password Lama</label>
                        <div class="password-wrapper">
                            <input type="password" name="current_password" id="current_password" class="form-control" placeholder="Kosongkan jika tidak ingin diubah" autocomplete="current-password" oninput="checkPasswordInput('current_password', 'eye_current')">
                            <i class="fa-solid fa-eye-slash toggle-password" id="eye_current" onclick="togglePassword('current_password', this)"></i>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Password Baru</label>
                        <div class="password-wrapper">
                            <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Minimal 6 karakter" autocomplete="new-password" oninput="checkPasswordInput('new_password', 'eye_new')">
                            <i class="fa-solid fa-eye-slash toggle-password" id="eye_new" onclick="togglePassword('new_password', this)"></i>
                        </div>
                    </div>
                </div>

                <!-- Tombol Aksi Bawah (Simpan & Kembali) -->
                <div class="d-flex flex-column flex-md-row justify-content-end gap-2 pt-4 border-top">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-dark px-4 py-2 fw-bold">Kembali</a>
                    <button type="submit" class="btn btn-orange px-5 py-2 fw-bold shadow-sm"><i class="fa-solid fa-save me-2"></i>Simpan Perubahan</button>
                </div>
            </form>
            
            <!-- Tombol Logout terpisah -->
            <div class="d-flex justify-content-end mt-3">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger fw-bold px-4"><i class="fa-solid fa-power-off me-2"></i>Logout</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function previewFile(event) {
        const file = event.target.files[0];
        const previewImg = document.getElementById('imagePreview');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    }

    // Fitur Munculkan Ikon Mata Hanya Saat Kolom Password Terisi
    function checkPasswordInput(inputId, eyeId) {
        const input = document.getElementById(inputId);
        const eyeIcon = document.getElementById(eyeId);
        if (input.value.length > 0) {
            eyeIcon.style.display = 'block';
        } else {
            eyeIcon.style.display = 'none';
            input.type = 'password'; // Kembalikan ke mode hidden jika dikosongkan
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        }
    }

    // Fitur Show/Hide Password
    function togglePassword(inputId, iconEl) {
        const input = document.getElementById(inputId);
        if (input.type === 'password') {
            input.type = 'text';
            iconEl.classList.remove('fa-eye-slash');
            iconEl.classList.add('fa-eye');
        } else {
            input.type = 'password';
            iconEl.classList.remove('fa-eye');
            iconEl.classList.add('fa-eye-slash');
        }
    }
</script>
@endsection