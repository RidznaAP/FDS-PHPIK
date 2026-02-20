<!doctype html>
<html lang="id" style="height:100%;margin:0;padding:0;">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>@yield('title', 'Login') — FDS-HPIK</title>
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
            flex:0 0 50%;
            background: linear-gradient(135deg, #0a1628 0%, #122a46 40%, #1565c0 100%);
            display:flex;
            flex-direction:column;
            align-items:center;
            justify-content:center;
            padding:3rem;
            position:relative;
            overflow:hidden;
        }
        .login-left::before {
            content:'';
            position:absolute;
            width:500px; height:500px;
            background:radial-gradient(circle, rgba(59,130,246,0.15) 0%, transparent 70%);
            top:-100px; right:-100px;
            border-radius:50%;
        }
        .login-left::after {
            content:'';
            position:absolute;
            width:300px; height:300px;
            background:radial-gradient(circle, rgba(16,185,129,0.1) 0%, transparent 70%);
            bottom:-50px; left:-50px;
            border-radius:50%;
        }

        .brand-box { position:relative; z-index:1; text-align:center; max-width:380px; }
        .brand-icon { font-size:4.5rem; display:block; margin-bottom:1rem; filter:drop-shadow(0 4px 20px rgba(59,130,246,0.4)); }
        .brand-name { color:#fff; font-size:2rem; font-weight:800; letter-spacing:-0.5px; margin-bottom:0.5rem; }
        .brand-desc { color:rgba(255,255,255,0.6); font-size:0.95rem; line-height:1.6; margin-bottom:2rem; }
        .brand-desc strong { color:rgba(255,255,255,0.85); }

        .feat-list { list-style:none; padding:0; text-align:left; width:100%; }
        .feat-list li {
            padding:0.55rem 0.8rem; color:rgba(255,255,255,0.75); font-size:0.88rem;
            display:flex; align-items:center; gap:0.75rem; border-radius:8px;
            transition:background 0.2s;
        }
        .feat-list li:hover { background:rgba(255,255,255,0.06); }
        .feat-list li i { color:#4ade80; font-size:1.15rem; flex-shrink:0; }

        .brand-copy {
            position:absolute; bottom:1.5rem; color:rgba(255,255,255,0.25); font-size:0.75rem;
        }

        /* ===== KANAN: Form 50% ===== */
        .login-right {
            flex:0 0 50%;
            background:#f8fafc;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:2rem;
            overflow-y:auto;
        }

        .form-box { width:100%; max-width:400px; }

        /* Card */
        .auth-card {
            background:#fff;
            border:1px solid #e2e8f0;
            border-radius:12px;
            box-shadow:0 1px 3px rgba(0,0,0,0.06);
        }
        .auth-card-body { padding:2rem; }
        .auth-card-footer {
            padding:1rem 2rem;
            background:#f8fafc;
            border-top:1px solid #e2e8f0;
            text-align:center;
            font-size:0.9rem;
            color:#64748b;
            border-radius:0 0 12px 12px;
        }
        .auth-card-footer a { color:#2563eb; text-decoration:none; font-weight:500; }
        .auth-card-footer a:hover { text-decoration:underline; }

        .auth-title {
            text-align:center; font-size:1.5rem; font-weight:700; color:#1e293b; margin-bottom:1.5rem;
        }

        /* Form Elements */
        .form-group { margin-bottom:1rem; }
        .form-lbl {
            display:flex; justify-content:space-between; align-items:center;
            font-size:0.85rem; font-weight:500; color:#475569; margin-bottom:0.4rem;
        }
        .form-lbl a { color:#2563eb; text-decoration:none; font-size:0.8rem; }
        .form-lbl a:hover { text-decoration:underline; }
        .form-input {
            width:100%; padding:0.65rem 0.85rem; font-size:0.9rem; font-family:inherit;
            border:1px solid #cbd5e1; border-radius:8px; outline:none;
            transition:border-color 0.2s, box-shadow 0.2s;
        }
        .form-input:focus {
            border-color:#3b82f6; box-shadow:0 0 0 3px rgba(59,130,246,0.15);
        }
        .form-input.error { border-color:#ef4444; }
        .error-msg { color:#ef4444; font-size:0.78rem; margin-top:0.25rem; }

        .input-group-pw { position:relative; }
        .input-group-pw .form-input { padding-right:2.8rem; }
        .pw-toggle {
            position:absolute; right:0.7rem; top:50%; transform:translateY(-50%);
            background:none; border:none; cursor:pointer; color:#94a3b8; font-size:1.1rem;
        }
        .pw-toggle:hover { color:#475569; }

        .check-row { display:flex; align-items:center; gap:0.5rem; margin:0.8rem 0; }
        .check-row input[type=checkbox] { width:16px; height:16px; accent-color:#3b82f6; cursor:pointer; }
        .check-row label { font-size:0.85rem; color:#64748b; cursor:pointer; }

        .btn-submit {
            width:100%; padding:0.7rem; font-size:0.95rem; font-weight:600; font-family:inherit;
            background:linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            color:#fff; border:none; border-radius:8px; cursor:pointer;
            transition:opacity 0.2s, transform 0.15s;
        }
        .btn-submit:hover { opacity:0.9; transform:translateY(-1px); }
        .btn-submit:active { transform:translateY(0); }

        .divider {
            display:flex; align-items:center; gap:0.8rem;
            margin:1.2rem 0; color:#94a3b8; font-size:0.8rem; text-transform:uppercase;
        }
        .divider::before, .divider::after {
            content:''; flex:1; height:1px; background:#e2e8f0;
        }

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
                <div class="brand-name">FDS-HPIK</div>
                <div class="brand-desc">
                    Sistem Informasi Pemantauan<br>
                    <strong>Hama & Penyakit Ikan Karantina</strong>
                </div>
                <ul class="feat-list">
                    <li><i class="ti ti-clipboard-check"></i> Perencanaan & pelaksanaan pemantauan</li>
                    <li><i class="ti ti-flask"></i> Pencatatan hasil uji laboratorium</li>
                    <li><i class="ti ti-map"></i> Pemetaan GIS sebaran penyakit</li>
                    <li><i class="ti ti-file-spreadsheet"></i> Laporan & ekspor data Excel</li>
                    <li><i class="ti ti-shield-check"></i> Multi-level role: UPT, BBKHIT, Pusat</li>
                </ul>
            </div>
            <div class="brand-copy">Direktorat Jenderal Perikanan &copy; {{ date('Y') }}</div>
        </div>

        <!-- ===== KANAN 50%: Form ===== -->
        <div class="login-right">
            <div class="form-box">
                @yield('content')
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            var x = document.getElementById("password");
            var icon = document.getElementById("eye-icon");
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
