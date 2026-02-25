<!doctype html>
<html lang="id" style="height:100%;margin:0;padding:0;">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>@yield('title', 'Login') — SIP-HPIK</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    {{-- HANYA icons, bukan tabler core CSS (menghindari konflik) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        *, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
        html, body { height:100%; font-family:'Inter',sans-serif; }

        .login-wrapper {
            display:flex !important;
            flex-direction:row !important;
            height:100vh;
            width:100vw;
        }

        /* ===== KIRI: Branding 50% ===== */
        .login-left {
            flex: 0 0 50%;
            background: radial-gradient(circle at top left, #1e3a8a, #0f172a);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 4rem;
            position: relative;
            overflow: hidden;
        }
        .login-left::before {
            content: '';
            position: absolute;
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
            top: -200px; right: -200px;
            border-radius: 50%;
        }

        .brand-box { position: relative; z-index: 10; text-align: center; }
        .brand-icon { font-size: 5rem; display: block; margin-bottom: 1.5rem; filter: drop-shadow(0 0 30px rgba(59, 130, 246, 0.5)); animation: float 6s ease-in-out infinite; }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-15px); } }

        .brand-name { color: #fff; font-size: 2.5rem; font-weight: 800; letter-spacing: -1px; margin-bottom: 0.5rem; }
        .brand-desc { color: rgba(255, 255, 255, 0.7); font-size: 1.1rem; line-height: 1.6; margin-bottom: 3rem; }
        .brand-copy { color: #ffffff; font-size: 0.85rem; margin-top: 2rem; }

        .feat-list { list-style: none; padding: 0; text-align: left; vstack gap: 3; }
        .feat-list li {
            padding: 0.8rem 1.2rem; color: #fff; font-size: 0.95rem;
            display: flex; align-items: center; gap: 1rem; border-radius: 12px;
            background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 0.8rem; backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }
        .feat-list li:hover { background: rgba(255, 255, 255, 0.1); transform: translateX(10px); }
        .feat-list li i { color: #4ade80; font-size: 1.25rem; }

        /* ===== KANAN: Form 50% ===== */
        .login-right {
            flex: 0 0 50%;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3rem;
            background-image: radial-gradient(#e5e7eb 1px, transparent 1px);
            background-size: 24px 24px;
        }

        .form-box { width: 100%; max-width: 440px; animation: fadeIn 0.6s ease-out; }

        .auth-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid #e5e7eb;
            border-radius: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            overflow: hidden;
        }
        .auth-card-body { padding: 3rem 2.5rem; }
        .auth-card-footer {
            padding: 1.5rem;
            background: #f9fafb;
            border-top: 1px solid #f3f4f6;
            text-align: center;
            color: #6b7280;
        }
        .auth-card-footer a { color: #2563eb; font-weight: 600; text-decoration: none; }

        .auth-title { font-size: 1.75rem; font-weight: 800; color: #111827; text-align: center; margin-bottom: 2rem; letter-spacing: -0.5px; }

        .form-group { margin-bottom: 1.25rem; }
        .form-lbl { font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem; display: block; }
        .form-input {
            width: 100%; padding: 0.75rem 1rem; border-radius: 12px; border: 1px solid #d1d5db;
            transition: all 0.2s; font-size: 0.95rem; background: #f8fafc;
        }
        .form-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); outline: none; background: #fff; }

        /* Password Group - Icon Inside */
        .input-group-pw { position: relative; display: flex; align-items: center; }
        .input-group-pw .form-input { padding-right: 3rem; }
        .pw-toggle {
            position: absolute; right: 0.75rem; background: none; border: none;
            color: #94a3b8; cursor: pointer; padding: 0.5rem; display: flex;
            align-items: center; justify-content: center; transition: color 0.2s;
        }
        .pw-toggle:hover { color: #475569; }

        /* Remember Me Row */
        .check-row { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; user-select: none; cursor: pointer; }
        .check-row input[type="checkbox"] { width: 1.1rem; height: 1.1rem; border-radius: 4px; cursor: pointer; accent-color: #2563eb; }
        .check-row label { font-size: 0.9rem; color: #475569; cursor: pointer; font-weight: 500; }

        .btn-submit {
            width: 100%; padding: 0.875rem; background: #2563eb; color: white; border: none; border-radius: 12px;
            font-weight: 700; font-size: 1rem; cursor: pointer; transition: all 0.2s;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2); margin-top: 0.5rem;
        }
        .btn-submit:hover { background: #1d4ed8; transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(37, 99, 235, 0.3); }
        .btn-submit:active { transform: translateY(0); }

        /* Divider or */
        .divider {
            display: flex; align-items: center; text-align: center; color: #94a3b8;
            font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.1em;
            margin: 1.5rem 0; font-weight: 600;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; border-bottom: 1px solid #e2e8f0;
        }
        .divider:not(:empty)::before { margin-right: 1rem; }
        .divider:not(:empty)::after { margin-left: 1rem; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        /* Mobile: sembunyikan kiri */
        @media (max-width:991px) {
            .login-left { display:none !important; }
            .login-right { flex:0 0 100% !important; }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <!-- ===== KIRI 50%: Branding ===== -->
        <div class="login-left">
            <div class="brand-box">
                <span class="brand-icon">🐟</span>
                <div class="brand-name">SIP-HPIK</div>
                <div class="brand-desc">
                    Sistem Informasi Pemantauan<br>
                    <strong>Hama & Penyakit Ikan Karantina</strong>
                </div>
                <ul class="feat-list">
                    <li><i class="ti ti-clipboard-check"></i> Perencanaan & pelaksanaan pemantauan</li>
                    <li><i class="ti ti-flask"></i> Pencatatan hasil uji laboratorium</li>
                    <li><i class="ti ti-map"></i> Pemetaan GIS sebaran penyakit</li>
                    <li><i class="ti ti-file-spreadsheet"></i> Laporan & ekspor data Excel</li>
                    <li><i class="ti ti-shield-check"></i> Multi-level role: BKHIT, BBKHIT, Pusat</li>
                </ul>
            </div>
            <div class="brand-copy">Deputi Karantina Ikan &copy; {{ date('Y') }}</div>
        </div>

        <!-- ===== KANAN 50%: Form ===== -->
        <div class="login-right">
            <div class="form-box">
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId = "password", iconId = "eye-icon") {
            var x = document.getElementById(inputId);
            var icon = document.getElementById(iconId);
            if (x.type === "password") {
                x.type = "text";
                icon.classList.replace("ti-eye", "ti-eye-off");
            } else {
                x.type = "password";
                icon.classList.replace("ti-eye-off", "ti-eye");
            }
        }
    </script>
</body>
</html>
