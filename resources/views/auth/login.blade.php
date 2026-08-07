@php($title = 'Login')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>


    {{-- Bootstrap Icons CDN --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">


    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
        }


        .auth-wrapper {
            display: flex;
            height: 100vh;
            align-items: center;
            justify-content: center;
        }


        .auth-center {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }


        .auth-card {
            width: 100%;
            max-width: 460px;
            background: #f9f9f9;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            min-height: 400px;
            transition: all 0.3s ease;
        }


        .auth-card h2 {
            margin-bottom: 1.5rem;
            color: #333;
        }


        .mb-3 {
            margin-bottom: 1rem;
        }


        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
        }


        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            box-sizing: border-box;
            height: 40px;
        }


        .form-check {
            display: flex;
            align-items: center;
        }


        .form-check-input {
            margin-right: 0.5rem;
        }


        .d-flex {
            display: flex;
        }


        .justify-content-between {
            justify-content: space-between;
        }


        .align-items-center {
            align-items: center;
        }


        .btn-submit {
            width: 100%;
            background: linear-gradient(to right, #e1a243, #dcc175);
            color: white;
            padding: 0.75rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 1rem;
        }


        .small-link {
            font-size: 0.875rem;
            color: #2575fc;
            text-decoration: none;
        }


        .auth-switch {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }


        .auth-switch a {
            color: #ff4b2b;
            font-weight: bold;
            text-decoration: none;
        }


        .auth-switch a:hover {
            text-decoration: underline;
        }


        .alert {
            padding: 0.75rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            font-size: 0.875rem;
        }


        .alert-error {
            background-color: #ffebee;
            color: #c62828;
        }


        .alert-success {
            background-color: #e0f7fa;
            color: #00796b;
        }


        .go-back {
            margin-top: 1rem;
            text-align: center;
        }


        .btn-back {
            display: inline-block;
            background-color: #e0e0e0;
            color: #333;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.2s ease;
        }


        .btn-back:hover {
            background-color: #d4d4d4;
        }


        /* Password wrapper */
        .password-wrapper {
            position: relative;
        }


        .password-wrapper input {
            padding-right: 2.75rem;
        }


        .toggle-password {
            position: absolute;
            top: 70%;
            right: 12px;
            transform: translateY(-50%);
            font-size: 1.25rem;
            color: #666;
            cursor: pointer;
            user-select: none;
        }


        .input-error {
            border-color: #c62828;
        }


        @media (max-width: 768px) {
            .auth-wrapper {
                flex-direction: column;
            }


            .auth-left {
                display: none;
            }


            .auth-right {
                flex: none;
                padding: 2rem 1rem;
                justify-content: center;
            }


            .auth-card {
                width: 100%;
                max-width: 90%;
                min-height: auto;
                box-shadow: none;
            }


            .auth-switch {
                font-size: 13px;
            }


            .btn-submit {
                padding: 0.65rem;
            }
        }
    </style>
</head>
<body>
    <div class="auth-wrapper">
        <div class="auth-center">
            <div class="auth-card">
                <h2>Sign in</h2>


                {{-- Alert --}}
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif


                @if (session('error'))
                    <div class="alert alert-error">
                        {{ session('error') }}
                    </div>
                @endif


                @if ($errors->any())
                    <div class="alert alert-error">
                        <ul style="margin: 0; padding-left: 1.2rem;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                <form method="POST" action="{{ route('login') }}">
                    @csrf


                    {{-- Email --}}
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
                    </div>


                    {{-- Password --}}
                    <div class="mb-3 password-wrapper">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" name="password"
                               class="{{ $errors->has('password') ? 'input-error' : '' }}" required>
                        <i class="bi bi-eye-slash toggle-password" id="togglePassword"></i>
                        @error('password')
                            <div style="color: #c62828; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                        @enderror
                    </div>


                    {{-- Remember + Forgot --}}
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="remember" name="remember">
                            <label class="form-check-label" for="remember">Remember me</label>
                        </div>
                        @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="small-link">Forgot password?</a>
                        @endif
                    </div>


                    <button type="submit" class="btn-submit">Submit</button>
                </form>


                {{-- Register link --}}
                <div class="auth-switch">
                    Belum punya akun? <a href="{{ route('register') }}">Register</a>
                </div>
            </div>


            {{-- Tombol kembali --}}
            <div class="go-back">
                <a href="{{ route('welcome') }}" class="btn-back">Kembali ke Home</a>
            </div>
        </div>
    </div>


    <script>
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');


        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    </script>
</body>
</html>


