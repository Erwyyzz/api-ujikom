<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AlatKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: url('https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?w=1920&q=80') no-repeat center center/cover;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(30, 58, 95, 0.6);
            z-index: 0;
        }

        .login-wrapper {
            position: relative;
            z-index: 10;
            display: grid;
            grid-template-columns: 1fr 1fr;
            max-width: 1000px;
            width: 100%;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 32px;
            border: 1px solid rgba(255, 255, 255, 0.15);
            overflow: hidden;
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.4);
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        @keyframes slideUp {
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== LEFT PANEL ===== */
        .login-left {
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            color: #ffffff;
            background: linear-gradient(135deg, #1E3A5F 0%, #1E3A5F 60%, #E07A5F 100%);
        }

        .login-left .brand-icon {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #E07A5F, #F4A261);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            box-shadow: 0 8px 24px rgba(224, 122, 95, 0.3);
        }

        .login-left h1 {
            font-size: 36px;
            font-weight: 900;
            letter-spacing: -1px;
            margin-bottom: 8px;
        }

        .login-left h1 span {
            color: #F4A261;
        }

        .login-left .slogan {
            font-size: 18px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 12px;
        }

        .login-left .description {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.6);
            line-height: 1.6;
            max-width: 380px;
        }

        .login-left .features {
            margin-top: 24px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .login-left .features .feature-item {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
        }

        .login-left .features .feature-item i {
            color: #F4A261;
            font-size: 14px;
            width: 20px;
            text-align: center;
        }

        /* ===== RIGHT PANEL (FORM) ===== */
        .login-right {
            padding: 48px 40px;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            display: flex;
            flex-direction: column;
            justify-content: center;
            border-radius: 0 32px 32px 0;
        }

        .login-right h2 {
            font-size: 24px;
            font-weight: 700;
            color: #1E3A5F;
            margin-bottom: 4px;
        }

        .login-right .subtitle {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 24px;
        }

        .input-group {
            margin-bottom: 16px;
        }

        .input-group label {
            display: block;
            color: #1E3A5F;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .input-group .input-wrapper {
            position: relative;
        }

        .input-group .input-wrapper i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 14px;
            pointer-events: none;
        }

        .input-group input {
            width: 100%;
            padding: 10px 14px 10px 40px;
            background: #f1f5f9;
            border: 1.5px solid #e2e8f0;
            border-radius: 12px;
            color: #0f172a;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
        }

        .input-group input::placeholder {
            color: #94a3b8;
            font-size: 13px;
        }

        .input-group input:focus {
            border-color: #E07A5F;
            background: #ffffff;
            box-shadow: 0 0 0 3px rgba(224, 122, 95, 0.15);
        }

        .input-group .forgot-link {
            display: block;
            text-align: right;
            color: #94a3b8;
            font-size: 12px;
            margin-top: 4px;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .input-group .forgot-link:hover {
            color: #E07A5F;
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #E07A5F, #D96A4F);
            border: none;
            border-radius: 12px;
            color: #ffffff;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 4px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(224, 122, 95, 0.35);
        }

        .btn-login i {
            margin-left: 6px;
            transition: transform 0.3s ease;
        }

        .btn-login:hover i {
            transform: translateX(4px);
        }

        .login-footer-text {
            text-align: center;
            margin-top: 20px;
            color: #94a3b8;
            font-size: 12px;
            border-top: 1px solid #e2e8f0;
            padding-top: 16px;
        }

        .login-footer-text span {
            color: #1E3A5F;
            font-weight: 600;
        }

        .login-footer-text i {
            color: #E07A5F;
        }

        .alert {
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 12px;
            margin-bottom: 16px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }

        .alert-error i {
            color: #dc2626;
        }

        @media (max-width: 768px) {
            .login-wrapper {
                grid-template-columns: 1fr;
                border-radius: 24px;
            }
            .login-left {
                padding: 32px 28px;
                text-align: center;
            }
            .login-left .features {
                align-items: center;
            }
            .login-left .description {
                max-width: 100%;
            }
            .login-right {
                padding: 32px 28px;
                border-radius: 0 0 24px 24px;
            }
            .login-left h1 {
                font-size: 28px;
            }
        }

        @media (max-width: 480px) {
            .login-left { padding: 24px 20px; }
            .login-right { padding: 24px 20px; }
        }
    </style>
</head>
<body>

    <!-- ===== WRAPPER ===== -->
    <div class="login-wrapper">

        <!-- ===== LEFT PANEL ===== -->
        <div class="login-left">
            <div class="brand-icon">
                <i class="fas fa-toolbox text-white text-2xl"></i>
            </div>
            <h1><span>KU-Alat</span></h1>
            <p class="slogan">Kelengkapan Untuk Alat</p>
            <p class="description">
                Sistem manajemen peminjaman alat untuk laboratorium, workshop, dan kebutuhan operasional lainnya.
            </p>

            <div class="features">
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Kelola alat & kategori</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Peminjaman & pengembalian otomatis</span>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Laporan & riwayat transparan</span>
                </div>
            </div>
        </div>

        <!-- ===== RIGHT PANEL (FORM) ===== -->
        <div class="login-right">

            <h2>Log In</h2>
            <p class="subtitle">Masuk ke akun Anda</p>

            <!-- Alert -->
            @if(session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                {{ session('error') }}
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

            <!-- Form -->
            <form action="{{ route('login') }}" method="POST">
                @csrf

                <div class="input-group">
                    <label for="email">Email</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="admin@gmail.com" required>
                    </div>
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                    </div>
                    <a href="#" class="forgot-link">Lupa password?</a>
                </div>

                <button type="submit" class="btn-login">
                    Masuk <i class="fas fa-arrow-right"></i>
                </button>

            </form>

            <div class="login-footer-text">
                <i class="fas fa-toolbox"></i> <span>KU-Alat</span> — Sistem Peminjaman Alat
            </div>

        </div>

    </div>

</body>
</html>