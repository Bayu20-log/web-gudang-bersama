@php($title = 'Forgot Password')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>


    {{-- Bootstrap Icons --}}
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


        .auth-card {
            width: 100%;
            max-width: 460px;
            background: #f9f9f9;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            min-height: 300px;
            text-align: center;
        }


        .auth-card h2 {
            margin-bottom: 1rem;
            color: #333;
        }


        .instruction {
            font-size: 1rem;
            color: #555;
            margin-bottom: 1.5rem;
        }


        .wa-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background-color: #25D366;
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 5px;
            font-weight: bold;
            text-decoration: none;
            transition: background 0.3s ease;
        }


        .wa-button:hover {
            background-color: #1ebe5c;
        }


        .go-back {
            margin-top: 1.5rem;
        }


        .btn-back {
            display: inline-block;
            background-color: #e0e0e0;
            color: #333;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            text-decoration: none;
            font-size: 0.9rem;
        }


        .btn-back:hover {
            background-color: #d4d4d4;
        }
    </style>
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card">
        <h2>Lupa Password?</h2>
        <p class="instruction">
            Jika Anda lupa password, silakan hubungi Superadmin untuk bantuan reset password.
        </p>
        <p style="margin-bottom: 1.5rem; font-weight: bold; color: #333;">
            📞 +62 812-3456-7890
        </p>
        <a href="https://wa.me/6281234567890?text=Halo%20Superadmin,%20saya%20lupa%20password.%20Mohon%20bantuannya."
           class="wa-button" target="_blank">
            <i class="bi bi-whatsapp"></i> Hubungi via WhatsApp
        </a>


        <div class="go-back">
            <a href="{{ route('login') }}" class="btn-back">Kembali ke Login</a>
        </div>
    </div>
</div>
</body>
</html>
