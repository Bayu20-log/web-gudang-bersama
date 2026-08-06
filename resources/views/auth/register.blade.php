@php($title = 'Register')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>


    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">


    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <style>
        /* ----------  GLOBAL  ---------- */
        *{box-sizing:border-box;margin:0;padding:0}
        body{
            font-family:"Segoe UI",Tahoma,sans-serif;
            background:#fff;
        }


        /* ----------  WRAPPER  ---------- */
        .auth-wrapper{
            display:flex;
            min-height:100vh;
            flex-wrap:wrap;
        }


        /* ----------  LEFT  ---------- */
        .auth-left{
            flex:1 1 45%;
            min-width:280px;
            background:linear-gradient(135deg,#4a00e0,#8e2de2);
            color:#fff;
            padding:100px 50px;
            display:flex;
            flex-direction:column;
            justify-content:center;
        }
        .auth-left h1{font-size:48px;margin-bottom:20px}
        .auth-left p{font-size:16px;line-height:1.6}


        /* ----------  RIGHT  ---------- */
        .auth-right{
            flex:1 1 55%;
            min-width:320px;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:40px 20px;
        }
        .auth-card{
            width:100%;
            max-width:500px;
            background:#f9f9f9;
            border-radius:12px;
            padding:35px 30px;
            box-shadow:0 4px 12px rgba(0,0,0,.08);
        }
        .auth-card h2{
            font-size:28px;
            text-align:center;
            margin-bottom:20px;
        }


        /* ----------  FORM  ---------- */
        .mb-3{margin-bottom:15px}
        label{display:block;font-weight:600;margin-bottom:6px}
        input,select,textarea{
            width:100%;padding:10px 12px;
            border:1px solid #ccc;
            border-radius:8px;font-size:14px
        }
        textarea{resize:vertical}
        .btn-submit{
            width:100%;padding:12px 0;margin-top:15px;
            border:none;border-radius:8px;
            background:linear-gradient(90deg,#ff416c,#ff4b2b);
            color:#fff;font-weight:700;font-size:16px;cursor:pointer
        }
        .btn-submit:hover{opacity:.9}


        /* ----------  ALERT / ERROR  ---------- */
        .alert{
            padding:12px;border-radius:8px;margin-bottom:18px;font-size:14px;
        }
        .alert-error{background:#ffebee;color:#c62828}
        .alert-success{background:#e0f7fa;color:#00796b}
        .error-text{color:#c62828;font-size:13px;margin-top:4px}


        /* ----------  SWITCH LINK  ---------- */
        .auth-switch{
            text-align:center;
            margin-top:20px;font-size:14px
        }
        .auth-switch a{
            color:#ff4b2b;font-weight:600;text-decoration:none
        }
        .auth-switch a:hover{text-decoration:underline}


        /* ----------  RESPONSIVE  ---------- */
        @media(max-width:768px){
            .auth-wrapper{flex-direction:column}
            .auth-left,.auth-right{flex:none;width:100%}
            .auth-left{padding:60px 25px;text-align:center}
            .auth-left h1{font-size:36px}
            .auth-left p{font-size:14px}
            .auth-card{margin-top:20px;padding:30px 20px}
        }


.password-wrapper {
    position: relative;
}
.password-wrapper input {
    padding-right: 40px;
}
.toggle-password {
    position: absolute;
    top: 50%;
    right: 12px;
    transform: translateY(-50%);
    cursor: pointer;
    color: #6c757d;
    font-size: 18px;
}
.toggle-password:hover {
    color: #333;
}


    </style>
</head>
<body>


<div class="auth-wrapper">
    <!-- LEFT PANEL -->
    <div class="auth-left">
        <h1>Join Us!</h1>
        <h2>Silakan isi form untuk melihat stok tersedia</h2>
    </div>


    <!-- RIGHT PANEL -->
    <div class="auth-right">
        <div class="auth-card">
            <h2>Create Account</h2>


            {{-- FLASH & VALIDATION ERRORS --}}
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif
            @if (session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-error">
                    <ul style="margin:0 0 0 18px">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data">
                @csrf


                <!-- Name -->
                <div class="mb-3">
                    <label for="name">Nama Lengkap</label>
                    <input id="name" name="name" type="text" required
                           value="{{ old('name') }}"
                           style="@error('name') border-color:#c62828; @enderror">
                    @error('name') <div class="error-text">{{ $message }}</div> @enderror
                </div>


                <!-- Username -->
                <div class="mb-3">
                    <label for="username">Username</label>
                    <input id="username" name="username" type="text" required
                           value="{{ old('username') }}"
                           style="@error('username') border-color:#c62828; @enderror">
                    @error('username') <div class="error-text">{{ $message }}</div> @enderror
                </div>


                <!-- Email -->
                <div class="mb-3">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" required
                           value="{{ old('email') }}"
                           style="@error('email') border-color:#c62828; @enderror">
                    @error('email') <div class="error-text">{{ $message }}</div> @enderror
                </div>


                <!-- Phone -->
                <div class="mb-3">
                    <label for="phone">Telepon</label>
                    <input id="phone" name="phone" type="text" required
                           value="{{ old('phone') }}"
                           style="@error('phone') border-color:#c62828; @enderror">
                    @error('phone') <div class="error-text">{{ $message }}</div> @enderror
                </div>


                <!-- Role -->
                <div class="mb-3">
                    <label for="role">Role</label>
                    <select id="role" name="role" required
                            style="@error('role') border-color:#c62828; @enderror">
                        <option value="Viewer" {{ old('role') == 'Viewer' ? 'selected' : '' }}>Viewer</option>
                    </select>
                    @error('role') <div class="error-text">{{ $message }}</div> @enderror
                </div>


                <!-- Position -->
                


                <!-- Status -->
                


                <!-- Photo -->
                


                <!-- Note -->
                


                <!-- Password -->
                <div class="mb-3">
                    <label for="password">Password</label>
                    <div class="password-wrapper">
                        <input id="password" name="password" type="password" required
                            style="@error('password') border-color:#c62828; @enderror">
                        <span class="toggle-password" onclick="togglePassword('password', this)">
                            <i class="bi bi-eye-slash"></i>
                        </span>
                    </div>
                    @error('password') <div class="error-text">{{ $message }}</div> @enderror
                </div>


                <!-- Confirm -->
                <div class="mb-3">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <div class="password-wrapper">
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                            style="@error('password_confirmation') border-color:#c62828; @enderror">
                        <span class="toggle-password" onclick="togglePassword('password_confirmation', this)">
                            <i class="bi bi-eye-slash"></i>
                        </span>
                    </div>
                    @error('password_confirmation') <div class="error-text">{{ $message }}</div> @enderror
                </div>


                <button type="submit" class="btn-submit">Register</button>
            </form>


            <!-- switch link -->
            <div class="auth-switch">
                Sudah punya akun? <a href="{{ route('login') }}">Login</a>
            </div>
        </div>
    </div>
</div>


<script>
    function togglePassword(inputId, el) {
        const input = document.getElementById(inputId);
        const icon = el.querySelector('i');


        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
    }
</script>


</body>
</html>