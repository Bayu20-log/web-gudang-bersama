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


    <!-- Font Awesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>




    <style>
         body { padding-top: 40px; }
        .nav-link.active, .dropdown-item.active {
            background-color: #272626 !important;
            color: #fff !important;
            font-weight: 600;
            border-radius: 0.375rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.2s ease-in-out;
        }


        .nav-link:hover:not(.active), .dropdown-item:hover:not(.active) {
            background-color: #f0f4ff;
            color: #edd078;
        }


        .navbar-nav .nav-link {
            padding: 0.5rem 1rem;
            transition: all 0.2s;
            border-radius: 0.375rem;
        }


        .dropdown-menu {
            border-radius: 0.5rem;
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.08);
        }


        .dropdown-item {
            border-radius: 0.375rem;
            transition: background-color 0.2s, color 0.2s;
        }


        .user-photo {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
        }
        html, body {
            height: 100%;
            margin: 0;
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main {
            flex: 1; /* isi konten fleksibel */
        }
        footer {
            background: #f8f9fa;
            padding: 15px 0;
            text-align: center;
            color: #666;
            font-size: 14px;
        }
    </style>
    </style>
</head>


@if(Auth::check())
<script>
    (function () {
        const IDLE_TIMEOUT = 900; // 15 menit
        let idleTime = 0;


        function resetIdleTime() {
            idleTime = 0;
        }


        function logoutViaPost() {
            fetch('{{ route('logout') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
            })
            .then(response => {
                window.location.href = '{{ route('welcome') }}';
            })
            .catch(error => {
                console.error('Logout error:', error);
            });
        }


        setInterval(() => {
            idleTime++;
            if (idleTime >= IDLE_TIMEOUT) {
                Swal.fire({
                    title: 'Auto Logout',
                    text: 'Anda telah logout otomatis karena tidak aktif selama 15 menit.',
                    icon: 'warning',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                }).then((result) => {
                    if (result.isConfirmed) {
                        logoutViaPost();
                    }
                });
            }
        }, 1000);


        // Reset idle timer saat ada aktivitas
        window.onload = resetIdleTime;
        document.onmousemove = resetIdleTime;
        document.onkeypress = resetIdleTime;
        document.onclick = resetIdleTime;
        document.onscroll = resetIdleTime;
    })();
</script>
@endif




<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-white bg-white shadow-sm fixed-top">

        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                <span style="color: #edd078">RAK</span><span style="color: #020705">SAKTI</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>


            <div class="collapse navbar-collapse" id="mainNavbar">
                @auth
                    @php $role = Auth::user()->role; $routeName = Route::currentRouteName(); @endphp


                    <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-lg-2">
                        <li class="nav-item">
                            <a class="nav-link {{ $routeName === 'home' ? 'active' : '' }}" href="{{ route('welcome') }}">Home</a>
                        </li>
                       


                        @if($role == 'superadmin')
                            <li class="nav-item">
                            <a class="nav-link {{ $routeName === 'dashboard.' . $role ? 'active' : '' }}" href="{{ route('dashboard.' . $role) }}">Dashboard</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $routeName === 'user.index' ? 'active' : '' }}" href="{{ route('user.index') }}">Kelola User</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $routeName === 'laporan.stok.admin' ? 'active' : '' }}" href="{{ route('laporan.stok.admin') }}">Laporan Stok</a>
                            </li>
                            


                        @elseif($role == 'gudang')
                            <li class="nav-item">
                            <a class="nav-link {{ $routeName === 'dashboard.' . $role ? 'active' : '' }}" href="{{ route('dashboard.' . $role) }}">Dashboard</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ in_array($routeName, ['kondisi.index', 'lokasi.index', 'kategori.index', 'satuan.index', 'pemasok.index']) ? 'active' : '' }}" href="#" id="masterDataDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Master Data
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item {{ $routeName === 'kondisi.index' ? 'active' : '' }}" href="{{ route('kondisi.index') }}">Kondisi Barang</a></li>
                                    <li><a class="dropdown-item {{ $routeName === 'lokasi.index' ? 'active' : '' }}" href="{{ route('lokasi.index') }}">Lokasi</a></li>
                                    <li><a class="dropdown-item {{ $routeName === 'kategori.index' ? 'active' : '' }}" href="{{ route('kategori.index') }}">Kategori</a></li>
                                    <li><a class="dropdown-item {{ $routeName === 'satuan.index' ? 'active' : '' }}" href="{{ route('satuan.index') }}">Satuan</a></li>
                                    <li><a class="dropdown-item {{ $routeName === 'pemasok.index' ? 'active' : '' }}" href="{{ route('pemasok.index') }}">Pemasok</a></li>
                                </ul>
                            </li>


                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ in_array($routeName, ['item.index', 'barang-masuk.index', 'barang-keluar.index']) ? 'active' : '' }}" href="#" id="barangDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Barang
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item {{ $routeName === 'item.index' ? 'active' : '' }}" href="{{ route('item.index') }}">Item</a></li>
                                    <li><a class="dropdown-item {{ $routeName === 'barang-masuk.index' ? 'active' : '' }}" href="{{ route('barang-masuk.index') }}">Barang Masuk</a></li>
                                    <li><a class="dropdown-item {{ $routeName === 'barang-keluar.index' ? 'active' : '' }}" href="{{ route('barang-keluar.index') }}">Barang Keluar</a></li>
                                </ul>
                            </li>


                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ in_array($routeName, ['laporan.stok', 'laporan.arus', 'omzet.index','aset.index']) ? 'active' : '' }}" href="#" id="laporanDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Laporan
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item {{ $routeName === 'laporan.stok' ? 'active' : '' }}" href="{{ route('laporan') }}">Laporan Stok</a></li>
                                    <li><a class="dropdown-item {{ $routeName === 'laporan.arus' ? 'active' : '' }}" href="{{ route('laporan.arus') }}">Arus Barang</a></li>
                                    <li><a class="dropdown-item {{ $routeName === 'aset.index' ? 'active' : '' }}" href="{{ route('aset.index') }}">Laporan Aset</a></li>
                                    <li><a class="dropdown-item {{ $routeName === 'omzet.index' ? 'active' : '' }}" href="{{ route('omzet.index') }}">Omzet</a></li>
                                </ul>
                            </li>

                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle {{ in_array($routeName, ['laporan.stok', 'laporan.arus', 'omzet.index','aset.index']) ? 'active' : '' }}" href="#" id="laporanDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Prediksi
                                </a>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item {{ $routeName === 'prediksi.index' ? 'active' : '' }}" href="{{ route('prediksi.index') }}">Prediksi</a></li>
                                </ul>
                            </li>






                        @elseif($role == 'viewer')
                        <li class="nav-item">
                                <a class="nav-link {{ $routeName === 'laporan.stok.viewer' ? 'active' : '' }}" href="{{ route('laporan.stok.viewer') }}">📦 Stok</a>
                            </li>

                        @endif
                    </ul>


                    <ul class="navbar-nav mb-2 mb-lg-0 d-flex align-items-center">
                        <li class="nav-item dropdown d-flex align-items-center me-3">
                            <a href="{{ route('profile.show') }}" class="me-2 fw-medium text-decoration-none">
                                <span style="color: #edd078">Hi,</span> <span style="color: #000000"><strong>{{ Auth::user()->name }}</strong>,</span> <span style="color: #edd078">Anda berperan sebagai</span> <span style="color: #000000"><strong>{{ ucfirst(Auth::user()->role) }}</strong></span>
                            </a>
                            <img
                                src="{{ Auth::user()->photo ? asset('storage/' . Auth::user()->photo) : 'https://placehold.co/50x50' }}"
                                class="user-photo"
                                alt="Avatar"
                                data-bs-toggle="dropdown"
                                aria-expanded="false"
                                style="cursor:pointer;">
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile.show') }}">Profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button class="dropdown-item" type="submit">Logout</button>
                                    </form>
                                </li>
                            </ul>
                        </li>


                    @if(Auth::user()->role === 'gudang')
                        <li class="nav-item position-relative">
                            <a href="{{ route('notifications.index') }}" class="text-dark" style="font-size: 20px;">
                                <i class="fa-solid fa-bell"></i>
                                @php
                                    $unreadCount = \App\Models\Notification::where('user_id', Auth::id()) // 🔹 filter sesuai user
                                                    ->where('is_read', false)
                                                    ->count();
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            </a>
                        </li>
                    @endif

                    </ul>


                @endauth
            </div>
        </div>
    </nav>


    <main class="container py-4" style="padding-top: 80px;">
        @yield('content')
    </main>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


    @if(Auth::check() && Auth::user()->role === 'gudang' && session('show_notification_popup'))
        @php
            session()->forget('show_notification_popup'); // Hapus biar gak muncul lagi
            $notifications = \App\Models\Notification::where('user_id', Auth::id()) // filter user
                        ->where('is_read', false)
                        ->get();
        @endphp
       
        @if($notifications->count() > 0)
        <div class="modal fade" id="notifModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title">Notifikasi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <ul>
                            @foreach($notifications as $notif)
                                <li>{{ $notif->message }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="modal-footer">
                        <form method="POST" action="{{ route('notifications.markRead') }}">
                            @csrf
                            <button type="submit" class="btn btn-primary">Tandai Dibaca</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var notifModal = new bootstrap.Modal(document.getElementById('notifModal'));
                notifModal.show();
            });
        </script>
        @endif
    @endif

@include('layouts.footer')
</body>
</html>