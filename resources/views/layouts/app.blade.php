<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Web Gudang</title>
    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-bxgU4WZzP5YzvFQXbztTxV2K/v5zvnXdGV0N+vH8JbYmNcrwVyoAfCq4S+fzO3B92TxQJ5mFUCULN31hz/1Fbw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Bootstrap core -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom styles -->
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { padding-top: 40px; }
        
        /* NAVBAR GELAP */
        .navbar-custom {
            background-color: #1a1e23 !important;
        }
        .navbar-nav .nav-link {
            color: #d1d5db !important;
            padding: 0.5rem 1rem;
            transition: all 0.2s;
            border-radius: 0.375rem;
        }
        .nav-link.active, .dropdown-item.active {
            background-color: #f97316 !important;
            color: #ffffff !important;
            font-weight: 600;
            border-radius: 0.375rem;
            box-shadow: 0 2px 6px rgba(249, 115, 22, 0.3);
        }
        .nav-link:hover:not(.active) {
            color: #f97316 !important;
            background-color: rgba(255, 255, 255, 0.05);
        }
        .dropdown-menu {
            border-radius: 0.75rem;
            box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(0,0,0,0.05);
        }
        .dropdown-item { transition: 0.2s; }
        .dropdown-item:hover:not(.active) {
            background-color: #fffaf5;
            color: #f97316;
        }

        /* Foto Profil & Ikon Lonceng SVG */
        .user-photo {
            width: 42px;
            height: 42px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #374151;
            transition: 0.3s;
        }
        .user-photo:hover { border-color: #f97316; }
        
        .bell-icon { 
            color: #ffffff; 
            transition: transform 0.2s, color 0.2s; 
            cursor: pointer;
        }
        .bell-icon:hover { 
            transform: scale(1.15) rotate(10deg); 
            color: #f97316; 
        }
        .notif-badge {
            width: 18px; 
            height: 18px; 
            font-size: 0.65rem; 
            padding: 0;
            background-color: #ef4444; 
        }

        html, body { height: 100%; margin: 0; display: flex; flex-direction: column; min-height: 100vh; }
        main { flex: 1; }
        footer { background: #f8f9fa; padding: 15px 0; text-align: center; color: #666; font-size: 14px; }
    </style>
</head>
@if(Auth::check())
<script>
    (function () {
        const IDLE_TIMEOUT = 900;
        let idleTime = 0;
        function resetIdleTime() { idleTime = 0; }
        function logoutViaPost() {
            fetch('{{ route('logout') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
            }).then(() => { window.location.href = '{{ route('welcome') }}'; });
        }
        setInterval(() => {
            idleTime++;
            if (idleTime >= IDLE_TIMEOUT) {
                Swal.fire({
                    title: 'Auto Logout',
                    text: 'Anda telah logout otomatis karena tidak aktif selama 15 menit.',
                    icon: 'warning', confirmButtonText: 'OK', allowOutsideClick: false, allowEscapeKey: false,
                    confirmButtonColor: '#f97316'
                }).then((result) => { if (result.isConfirmed) { logoutViaPost(); } });
            }
        }, 1000);
        window.onload = resetIdleTime; document.onmousemove = resetIdleTime; document.onkeypress = resetIdleTime; document.onclick = resetIdleTime; document.onscroll = resetIdleTime;
    })();
</script>
@endif
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                <span style="color: #f97316">RAK</span><span class="text-white">SAKTI</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="mainNavbar">
                @auth
                    @php $role = strtolower(Auth::user()->role); $routeName = Route::currentRouteName(); @endphp
                    
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-2 mt-3 mt-lg-0">
                       <li class="nav-item">
                            <a class="nav-link {{ $routeName === 'home' ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                        </li>
                        
                        @if($role == 'superadmin')
                            <li class="nav-item"><a class="nav-link {{ $routeName === 'dashboard.' . $role ? 'active' : '' }}" href="{{ route('dashboard.' . $role) }}">Dashboard</a></li>
                            <li class="nav-item"><a class="nav-link {{ $routeName === 'user.index' ? 'active' : '' }}" href="{{ route('user.index') }}">Kelola User</a></li>
                            <li class="nav-item"><a class="nav-link {{ $routeName === 'laporan.stok.admin' ? 'active' : '' }}" href="{{ route('laporan.stok.admin') }}">Laporan Stok</a></li>
                        
                        @elseif($role == 'gudang')
                            <li class="nav-item"><a class="nav-link {{ $routeName === 'dashboard.' . $role ? 'active' : '' }}" href="{{ route('dashboard.' . $role) }}">Dashboard</a></li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ in_array($routeName, ['kondisi.index', 'lokasi.index', 'kategori.index', 'satuan.index', 'pemasok.index']) ? 'active' : '' }}" href="#" id="masterDataDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Master Data</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item fw-medium {{ $routeName === 'kondisi.index' ? 'active' : '' }}" href="{{ route('kondisi.index') }}">Kondisi Barang</a></li>
                                    <li><a class="dropdown-item fw-medium {{ $routeName === 'lokasi.index' ? 'active' : '' }}" href="{{ route('lokasi.index') }}">Lokasi</a></li>
                                    <li><a class="dropdown-item fw-medium {{ $routeName === 'kategori.index' ? 'active' : '' }}" href="{{ route('kategori.index') }}">Kategori</a></li>
                                    <li><a class="dropdown-item fw-medium {{ $routeName === 'satuan.index' ? 'active' : '' }}" href="{{ route('satuan.index') }}">Satuan</a></li>
                                    <li><a class="dropdown-item fw-medium {{ $routeName === 'pemasok.index' ? 'active' : '' }}" href="{{ route('pemasok.index') }}">Pemasok</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ in_array($routeName, ['item.index', 'item.create', 'barang-masuk.index', 'barang-keluar.index']) ? 'active' : '' }}" href="#" id="barangDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Barang</a>
                                <ul class="dropdown-menu">
                                    <!-- PERUBAHAN: Daftar Item menjadi Daftar Produk -->
                                    <li><a class="dropdown-item fw-medium {{ in_array($routeName, ['item.index', 'item.create']) ? 'active' : '' }}" href="{{ route('item.index') }}">Daftar Produk</a></li>
                                    <li><a class="dropdown-item fw-medium {{ $routeName === 'barang-masuk.index' ? 'active' : '' }}" href="{{ route('barang-masuk.index') }}">Barang Masuk</a></li>
                                    <li><a class="dropdown-item fw-medium {{ $routeName === 'barang-keluar.index' ? 'active' : '' }}" href="{{ route('barang-keluar.index') }}">Barang Keluar</a></li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ in_array($routeName, ['laporan', 'laporan.arus', 'omzet.index','aset.index']) ? 'active' : '' }}" href="#" id="laporanDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Laporan</a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item fw-medium {{ $routeName === 'laporan' ? 'active' : '' }}" href="{{ route('laporan') }}">Laporan Stok</a></li>
                                    <li><a class="dropdown-item fw-medium {{ $routeName === 'laporan.arus' ? 'active' : '' }}" href="{{ route('laporan.arus') }}">Arus Barang</a></li>
                                    <li><a class="dropdown-item fw-medium {{ $routeName === 'aset.index' ? 'active' : '' }}" href="{{ route('aset.index') }}">Laporan Aset</a></li>
                                    <li><a class="dropdown-item fw-medium {{ $routeName === 'omzet.index' ? 'active' : '' }}" href="{{ route('omzet.index') }}">Omzet Penjualan</a></li>
                                </ul>
                            </li>
                        @elseif($role == 'viewer')
                            <li class="nav-item"><a class="nav-link {{ $routeName === 'laporan.stok.viewer' ? 'active' : '' }}" href="{{ route('laporan.stok.viewer') }}">Stok</a></li>
                        @endif
                    </ul>
                    
                    <!-- SUSUNAN KANAN: Teks -> Foto Profil -> Lonceng SVG -->
                    <ul class="navbar-nav mb-2 mb-lg-0 d-flex flex-row align-items-center mt-3 mt-lg-0 border-lg-start border-secondary ps-lg-4 gap-3">
                        
                        <!-- 1. Teks Info Akun -->
                        <li class="nav-item text-start text-lg-end">
                            <span class="text-white">Hi, <strong>{{ Auth::user()->name }}</strong></span><br>
                            <small style="color: #9ca3af; font-size:0.75rem;">Anda berperan sebagai {{ ucfirst(Auth::user()->role) }}</small>
                        </li>

                        <!-- 2. Foto Profil (Dropdown) -->
                        <li class="nav-item dropdown d-flex align-items-center">
                            <img src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) . '&background=f97316&color=fff' }}" 
                                 class="user-photo dropdown-toggle shadow-sm" alt="Avatar" data-bs-toggle="dropdown" aria-expanded="false" style="cursor:pointer;">
                            
                            <ul class="dropdown-menu dropdown-menu-end mt-3 border-0 py-2 shadow">
                                <li><a class="dropdown-item fw-medium py-2" href="{{ route('profile.show') }}"><i class="fa-solid fa-user-gear me-2 text-secondary"></i> Pengaturan Profil</a></li>
                                <li><hr class="dropdown-divider my-1"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button class="dropdown-item text-danger fw-bold py-2" type="submit"><i class="fa-solid fa-power-off me-2"></i> Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>

                        <!-- 3. Ikon Lonceng SVG -->
                        <li class="nav-item position-relative d-flex align-items-center ms-1">
                            <a href="{{ route('notifications.index') }}" class="nav-link p-0 text-decoration-none position-relative" title="Lihat Notifikasi">
                                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="currentColor" class="bell-icon" viewBox="0 0 16 16">
                                  <path d="M8 16a2 2 0 0 0 2-2H6a2 2 0 0 0 2 2zM8 1.918l-.797.161A4.002 4.002 0 0 0 4 6c0 .628-.134 2.197-.459 3.742-.16.767-.376 1.566-.663 2.258h10.244c-.287-.692-.502-1.49-.663-2.258C12.134 8.197 12 6.628 12 6a4.002 4.002 0 0 0-3.203-3.92L8 1.917zM14.22 12c.223.447.481.801.78 1H1c.299-.199.557-.553.78-1C2.68 10.2 3 6.88 3 6c0-2.42 1.72-4.44 4.005-4.901a1 1 0 1 1 1.99 0A5.002 5.002 0 0 1 13 6c0 .88.32 4.2 1.22 6z"/>
                                </svg>
                                
                                @php
                                    $unreadCount = 0;
                                    if (\Illuminate\Support\Facades\Schema::hasTable('notifications') && class_exists(\App\Models\Notification::class)) {
                                        $unreadCount = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->count();
                                    }
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-circle bg-danger border border-2 border-dark d-flex align-items-center justify-content-center text-white" style="width: 18px; height: 18px; font-size: 0.65rem; padding: 0;">
                                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                                    </span>
                                @endif
                            </a>
                        </li>
                    </ul>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container py-4" style="padding-top: 80px;">
        @yield('content')
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Modal Notifikasi Auto-Popup -->
    @if(Auth::check() && session('show_notification_popup'))
        @php
            session()->forget('show_notification_popup');
            $notifications = collect();
            if (\Illuminate\Support\Facades\Schema::hasTable('notifications') && class_exists(\App\Models\Notification::class)) {
                $notifications = \App\Models\Notification::where('user_id', Auth::id())->where('is_read', false)->get();
            }
        @endphp
        @if($notifications->count() > 0)
        <div class="modal fade" id="notifModalAuto" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header bg-dark text-white border-bottom border-warning border-3">
                        <h5 class="modal-title fw-bold"><i class="fa-solid fa-bell text-warning me-2"></i>Notifikasi Stok</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 bg-light">
                        <ul class="list-group list-group-flush rounded-3 shadow-sm">
                            @foreach($notifications as $notif)
                                <li class="list-group-item bg-white border-bottom fw-medium text-secondary py-3">
                                    <i class="fa-solid fa-circle-exclamation text-danger me-2"></i> {{ $notif->message }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="modal-footer border-0 pb-4 pe-4 bg-light">
                        <form method="POST" action="{{ route('notifications.markRead') ?? '#' }}">
                            @csrf
                            <button type="submit" class="btn btn-warning fw-bold px-4 shadow-sm text-dark"><i class="fa-solid fa-check-double me-1"></i> Tandai Dibaca</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var notifModalAuto = new bootstrap.Modal(document.getElementById('notifModalAuto'));
                notifModalAuto.show();
            });
        </script>
        @endif
    @endif
    
    @include('layouts.footer')
</body>
</html>