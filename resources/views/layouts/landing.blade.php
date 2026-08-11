<!DOCTYPE html>
<html lang="id" data-bs-spy="scroll" data-bs-target="#navbarNav" data-bs-offset="80" tabindex="0">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RAKSAKTI</title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- AOS Animate On Scroll -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- AlpineJS -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fc;
            scroll-behavior: smooth;
        }

        .navbar-brand span {
            color: #003322;
        }

        .nav-link {
            position: relative;
            transition: color 0.3s;
        }

        .nav-link:hover {
            color: #003322;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 0;
            height: 2px;
            background-color: #003322;
            transition: width 0.3s;
        }

        .nav-link:hover::after,
        .nav-link.active::after {
            width: 100%;
        }

        .btn-animated {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .btn-animated:disabled .spinner-border {
            display: inline-block;
        }

        .btn-animated .spinner-border {
            display: none;
            position: absolute;
            top: 50%;
            left: 50%;
            width: 1rem;
            height: 1rem;
            margin-top: -0.5rem;
            margin-left: -0.5rem;
        }

        .parallax-section {
            background-image: url("{{ asset('images/bg-parallax.jpg') }}");
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            padding: 100px 0;
            color: white;
            text-align: center;
        }

        .card:hover {
            transform: translateY(-5px);
            transition: 0.3s;
        }
        
        .breadcrumb-item,
        .breadcrumb-item a,
        .breadcrumb-item.active {
            color: #e2bd61 !important; /* hijau Bootstrap "success" */
        }
        .nav-pills .nav-link {
        color: #ffffff !important;   /* teks hijau */
        background-color: transparent !important; /* hapus background */
        border-radius: 0 !important; /* biar rata */
    }

    /* Hover effect */
    .nav-pills .nav-link:hover {
        color: #1e7e34 !important; /* hijau tua pas hover */
    }

    /* Tab aktif: kasih underline */
    .nav-pills .nav-link.active {
        font-weight: bold; /* tebalkan biar beda */
        border-bottom: 2px solid #18231b; /* underline hijau */
        color: #ffffff !important; /* tetap hijau */
        background-color: transparent !important;
    }

    

    



        footer {
            background-color: #f1f3f5;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        #mainNavbar.scrolled {
            background-color: #ffffff !important;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease-in-out;
        }

        .transition {
            transition: all 0.3s ease-in-out;
        }

        .feature-box {
            border: none;
            background-color: #ffffff;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
        }

        @media (min-width: 768px) {
            .accordion-desktop {
                display: none;
            }
        }

        @media (max-width: 767.98px) {
            .card-grid-desktop {
                display: none;
            }
        }

        @media (min-width: 992px) {
            .navbar .dropdown:hover .dropdown-menu {
                display: block;
                margin-top: 0;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav id="mainNavbar" class="navbar navbar-expand-lg navbar-light bg-white shadow-sm fixed-top transition">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ url('/') }}">
                <span style="color: #f1d787">RAK</span><span style="color: #030404">SAKTI</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('profil') }}">Profil GudangKu</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="{{ url('/#fitur') }}" id="navbarFitur" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Fitur
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarFitur">
                            <li><a class="dropdown-item" href="{{ route('gudang') }}">Gudang</a></li>
                            <li><a class="dropdown-item" href="{{ route('superadmin') }}">Superadmin</a></li>
                            <li><a class="dropdown-item" href="{{ route('viewer') }}">Viewer</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('faq') }}">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('galeri') }}">Galeri</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('kontak') }}">Kontak Kami</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    @auth
                        @php
                            $role = Auth::user()->role;
                            $dashboardRoute = match ($role) {
                                'superadmin' => route('dashboard.superadmin'),
                                'viewer' => route('dashboard.viewer'),
                                'gudang' => route('dashboard.gudang'),
                                default => route('dashboard'), // fallback
                            };
                        @endphp
                        <li class="nav-item me-3">
                            <a class="nav-link" href="{{ $dashboardRoute }}">
                                <i class="bi bi-person-circle"></i> Akun Saya
                            </a>
                        </li>
                    @else
                        <li class="nav-item me-3">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="bi bi-person-circle"></i> Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="bi bi-person-plus"></i> Register
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-1" style="margin-top: 90px;" data-aos="fade-up" data-aos-duration="800">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="text-center py-4 mt-5 border-top">
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} GudangKu. All rights reserved.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            AOS.init();
        });

        // Sticky navbar animation
        window.addEventListener('scroll', function () {
            const navbar = document.getElementById('mainNavbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Remove dropdown toggle on desktop to allow hover dropdown
        document.addEventListener('DOMContentLoaded', function () {
            if (window.innerWidth >= 992) {
                const dropdowns = document.querySelectorAll('.navbar .dropdown-toggle');
                dropdowns.forEach(dd => {
                    dd.removeAttribute('data-bs-toggle');
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>