<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Peminjaman Alat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .login-card {
            width: 100%;
            max-width: 400px;
            background: #ffffff;
            border-radius: 24px;
            padding: 40px 36px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06), 0 1px 3px rgba(0,0,0,0.04);
            border: 1px solid #e9edf4;
            animation: fadeUp 0.5s ease forwards;
            opacity: 0;
            transform: translateY(20px);
        }
        @keyframes fadeUp {
            to { opacity: 1; transform: translateY(0); }
        }
        .login-card .logo-icon {
            width: 56px;
            height: 56px;
            background: #2563eb;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
        }
        .login-card h3 {
            font-size: 22px;
            font-weight: 700;
            color: #0f172a;
            text-align: center;
            margin-bottom: 4px;
        }
        .login-card .subtitle {
            text-align: center;
            color: #64748b;
            font-size: 14px;
            margin-bottom: 28px;
        }
        .input-group {
            margin-bottom: 18px;
        }
        .input-group label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #1e293b;
            margin-bottom: 5px;
        }
        .input-group .input-wrapper {
            position: relative;
        }
        .input-group .input-wrapper i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 15px;
            pointer-events: none;
        }
        .input-group input {
            width: 100%;
            padding: 12px 16px 12px 44px;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            font-size: 14px;
            color: #0f172a;
            background: #fafbfc;
            outline: none;
            transition: all 0.2s ease;
        }
        .input-group input::placeholder {
            color: #94a3b8;
        }
        .input-group input:focus {
            border-color: #2563eb;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.08);
        }

        /* Password wrapper dengan icon mata di luar */
        .password-wrapper {
            display: flex;
            align-items: center;
            gap: 0;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            background: #fafbfc;
            transition: all 0.2s ease;
        }
        .password-wrapper:focus-within {
            border-color: #2563eb;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.08);
        }
        .password-wrapper input {
            border: none;
            border-radius: 12px 0 0 12px;
            padding: 12px 12px 12px 44px;
            background: transparent;
            flex: 1;
            font-size: 14px;
            color: #0f172a;
            outline: none;
            min-width: 0;
        }
        .password-wrapper input::placeholder {
            color: #94a3b8;
        }
        .password-wrapper .toggle-password {
            padding: 12px 16px 12px 12px;
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            font-size: 16px;
            border-radius: 0 12px 12px 0;
            transition: color 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .password-wrapper .toggle-password:hover {
            color: #475569;
        }
        .password-wrapper .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 15px;
            pointer-events: none;
        }
        .password-relative {
            position: relative;
            flex: 1;
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: #2563eb;
            border: none;
            border-radius: 12px;
            color: #ffffff;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 6px;
        }
        .btn-login:hover {
            background: #1d4ed8;
            box-shadow: 0 4px 12px rgba(37,99,235,0.25);
        }
        .btn-login:active { transform: scale(0.98); }
        .btn-login i { margin-left: 6px; transition: transform 0.2s ease; }
        .btn-login:hover i { transform: translateX(3px); }
        .alert {
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }
        .alert-error i { color: #dc2626; }
        .login-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: #94a3b8;
            border-top: 1px solid #f1f5f9;
            padding-top: 20px;
        }
        .login-footer i { margin-right: 4px; }
        @media (max-width: 480px) {
            .login-card { padding: 28px 20px; border-radius: 20px; }
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="logo-icon">
        <i class="fas fa-toolbox text-white text-xl"></i>
    </div>
    <h3>Login Sistem</h3>
    <p class="subtitle">Masuk untuk mengakses panel peminjaman alat</p>

    @if(session('error'))
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <ul class="list-disc pl-4 m-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('login') }}" method="POST">
        @csrf

        <!-- Input Email (biasa) -->
        <div class="input-group">
            <label for="email">Email</label>
            <div class="input-wrapper">
                <i class="fas fa-envelope"></i>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="admin@gmail.com" required>
            </div>
        </div>

        <!-- Input Password (dengan icon mata di luar) -->
        <div class="input-group">
            <label for="password">Password</label>
            <div class="password-wrapper">
                <div class="password-relative">
                    <i class="fas fa-lock input-icon"></i>
                    <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                </div>
                <button type="button" class="toggle-password" onclick="togglePassword()">
                    <i class="fas fa-eye" id="eyeIcon"></i>
                </button>
            </div>
        </div>

        <button type="submit" class="btn-login">
            Masuk <i class="fas fa-arrow-right"></i>
        </button>
    </form>

    <div class="login-footer">
        <i class="fas fa-shield-alt"></i> Sistem Peminjaman Alat
    </div>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>

</body>
</html>