<!doctype html>
<html lang="id" style="height:100%;margin:0;padding:0;">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>@yield('title', 'Login') — SIP-HPIK</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-instansi.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    {{-- HANYA icons, bukan tabler core CSS (menghindari konflik) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { height: 100%; font-family: 'Inter', sans-serif; }

        body {
            background-color: #0f172a;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(59, 130, 246, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(139, 92, 246, 0.15) 0%, transparent 40%),
                url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%231e293b' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
        }

        .login-wrapper {
            display: flex;
            width: 100vw;
            height: 100vh;
            background: transparent;
            overflow: hidden;
            animation: fadeInBody 0.8s ease-out forwards;
        }

        @keyframes fadeInBody {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* ===== KIRI: Branding 65% (Kira-kira 2/3) ===== */
        .login-left {
            flex: 0 0 65%;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.4), rgba(30, 58, 138, 0.4));
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 4rem;
            position: relative;
            text-align: center;
        }
        
        .brand-box { position: relative; z-index: 10; }
        .brand-icon { font-size: 5.5rem; display: block; filter: drop-shadow(0 0 30px rgba(59, 130, 246, 0.8)); animation: float 6s ease-in-out infinite; }
        @keyframes float { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-15px); } }

        .brand-name { color: #fff; font-size: 4.5rem; font-weight: 800; letter-spacing: -3px; margin-bottom: 0.25rem; text-shadow: 0 4px 10px rgba(0,0,0,0.3); line-height: 1.1; }
        .brand-subtitle { color: #7dd3fc; font-size: 1rem; font-weight: 700; letter-spacing: 0.25em; text-transform: uppercase; margin-bottom: 2.5rem; }
        .brand-desc { color: rgba(255, 255, 255, 0.8); font-size: 1.25rem; line-height: 1.7; font-weight: 300; max-width: 600px; margin: 0 auto; }
        .brand-copy { position: absolute; bottom: 2rem; color: rgba(255, 255, 255, 0.4); font-size: 0.85rem; z-index: 10; }

        /* Isometric Map Elements */
        .map-overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 1; pointer-events: none;
            background-image: linear-gradient(rgba(56, 189, 248, 0.2) 1px, transparent 1px),
                              linear-gradient(90deg, rgba(56, 189, 248, 0.2) 1px, transparent 1px);
            background-size: 60px 60px;
            transform-origin: center;
            transform: perspective(1000px) rotateX(60deg) translateY(-100px) scale(2);
            opacity: 0.2;
            mask-image: radial-gradient(circle at center, black 0%, transparent 70%);
            -webkit-mask-image: radial-gradient(circle at center, black 0%, transparent 70%);
        }

        .node-container { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 2; pointer-events: none; }
        .ping-node {
            position: absolute; width: 10px; height: 10px; background: #38bdf8;
            border-radius: 50%; box-shadow: 0 0 15px 2px rgba(56, 189, 248, 0.8);
        }
        .ping-node::after {
            content: ''; position: absolute; top: -15px; left: -15px;
            width: 40px; height: 40px; border: 2px solid #38bdf8; border-radius: 50%;
            opacity: 0; box-shadow: 0 0 10px rgba(56, 189, 248, 0.5);
        }
        .ping-node.delay-1::after { animation: radarPing 3s infinite cubic-bezier(0.1, 0.7, 0.1, 1); }
        .ping-node.delay-2::after { animation: radarPing 3.5s infinite cubic-bezier(0.1, 0.7, 0.1, 1) 1.2s; }
        .ping-node.delay-3::after { animation: radarPing 4s infinite cubic-bezier(0.1, 0.7, 0.1, 1) 2.5s; background-color: #ef4444; border-color: #ef4444; box-shadow: 0 0 15px rgba(239, 68, 68, 0.8); }
        .ping-node.delay-3 { background: #ef4444; box-shadow: 0 0 15px 2px rgba(239, 68, 68, 0.8); }

        @keyframes radarPing {
            0% { transform: scale(0.1); opacity: 1; }
            100% { transform: scale(3.5); opacity: 0; }
        }

        /* ===== KANAN: Form 35% (Kira-kira 1/3) ===== */
        .login-right {
            flex: 0 0 35%;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 3.5rem;
            position: relative;
        }

        .form-box { width: 100%; max-width: 360px; animation: fadeInRight 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards 0.2s; opacity: 0; }
        @keyframes fadeInRight { from { opacity: 0; transform: translateX(20px); } to { opacity: 1; transform: translateX(0); } }

        .auth-card { background: transparent; border: none; box-shadow: none; border-radius: 0; }
        .auth-card-body { padding: 0; }
        .auth-card-footer { margin-top: 2rem; padding: 0; background: transparent; border-top: none; text-align: center; color: #64748b; font-size: 0.85rem; }
        .auth-card-footer a { color: #0f172a; font-weight: 600; text-decoration: none; }

        .auth-title { font-size: 1.75rem; font-weight: 800; color: #0f172a; margin-bottom: 2rem; letter-spacing: -0.02em; text-align: left; }

        .form-group { margin-bottom: 1.5rem; }
        .form-lbl { font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 0.5rem; display: block; text-transform: uppercase; letter-spacing: 0.05em; }
        .form-input {
            width: 100%; padding: 0.875rem 1rem; border-radius: 10px; border: 1px solid #e2e8f0;
            transition: all 0.3s ease; font-size: 1rem; background: #fff; color: #0f172a; font-weight: 500;
        }
        .form-input::placeholder { color: #cbd5e1; font-weight: 400; text-transform: none; }
        .form-input:focus { border-color: #3b82f6; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1); outline: none; }

        .input-group-pw { position: relative; display: flex; align-items: center; }
        .input-group-pw .form-input { padding-right: 3rem; }
        .pw-toggle {
            position: absolute; right: 0.75rem; background: none; border: none;
            color: #94a3b8; cursor: pointer; padding: 0.5rem; display: flex;
            align-items: center; justify-content: center; transition: color 0.2s;
        }
        .pw-toggle:hover { color: #0f172a; }

        .check-row { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 2.5rem; user-select: none; cursor: pointer; }
        .check-row input[type="checkbox"] { width: 1.25rem; height: 1.25rem; border-radius: 4px; cursor: pointer; accent-color: #0f172a; border: 1px solid #cbd5e1; }
        .check-row label { font-size: 0.95rem; color: #475569; cursor: pointer; font-weight: 500; }

        .btn-submit {
            width: 100%; padding: 1.1rem; background: #0f172a; color: white; border: none; border-radius: 10px;
            font-weight: 600; font-size: 1rem; cursor: pointer; transition: all 0.3s ease; letter-spacing: 0.02em;
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
        }
        .btn-submit:hover { background: #1e40af; transform: translateY(-2px); box-shadow: 0 10px 20px -5px rgba(30, 64, 175, 0.4); }
        .btn-submit:active { transform: translateY(0); box-shadow: none; }

        /* Tablet & Mobile Responsiveness */
        @media (max-width:1100px) {
            .brand-name { font-size: 3.5rem; }
            .brand-desc { font-size: 1.1rem; }
            .login-left { padding: 3rem; }
            .login-right { padding: 2.5rem; }
        }
        @media (max-width:991px) {
            body { align-items: flex-start;     overflow-y: auto; }
            .login-wrapper { 
                flex-direction: column !important; 
                width: 100% !important; 
                height: auto !important; 
                min-height: 100vh !important;
                overflow: visible !important;
            }
            .login-left { 
                flex: none !important; 
                width: 100%; 
                padding: 4rem 2rem 5rem 2rem; 
                border-right: none; 
                min-height: auto;
            }
            .login-right { 
                flex: none !important; 
                width: 100%; 
                padding: 4rem 2rem; 
                border-top-left-radius: 2rem; 
                border-top-right-radius: 2rem; 
                margin-top: -2.5rem; 
                z-index: 20; 
                box-shadow: 0 -20px 40px rgba(0,0,0,0.25);
            }
            .brand-icon { font-size: 4rem; margin-bottom: 1rem; }
            .brand-name { font-size: 2.75rem; letter-spacing: -1.5px; }
            .brand-subtitle { font-size: 0.8rem; margin-bottom: 1.5rem; }
            .brand-desc { font-size: 1rem; max-width: 95%; }
            .brand-copy { position: relative; bottom: 0; margin-top: 2rem; }
            .form-box { max-width: 100%; margin: 0 auto; }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-left">
            <div class="map-overlay"></div>
            <div class="node-container">
                <div class="ping-node delay-1" style="top:25%; left:30%;"></div>
                <div class="ping-node delay-2" style="top:60%; left:75%;"></div>
                <div class="ping-node delay-3" style="top:75%; left:25%;" title="Deteksi Resiko Positif HPIK"></div>
                <div class="ping-node delay-1" style="top:15%; left:85%;"></div>
                <div class="ping-node delay-2" style="top:85%; left:65%;"></div>
            </div>
            
            <div class="brand-box">
                {{-- Logo Instansi Pemerintah --}}
                @php
                    $logoB64 = Cache::rememberForever('logo_instansi_b64', function() {
                        $path = public_path('images/logo-instansi.png');
                        if (file_exists($path)) {
                            return 'data:' . mime_content_type($path) . ';base64,' . base64_encode(file_get_contents($path));
                        }
                        return null;
                    });
                @endphp
                @if($logoB64)
                    <img src="{{ $logoB64 }}" alt="Logo Deputi Karantina Ikan"
                         style="height:150px;width:auto;object-fit:contain;filter:drop-shadow(0 10px 20px rgba(0,0,0,0.4)); margin-bottom:1.5rem;" decoding="async">
                @else
                    <span class="brand-icon">🐟</span>
                @endif
                <div class="brand-name" style="font-size:3.5rem;letter-spacing:-2px;line-height:1;margin-bottom:0.5rem;">SIP-HPIK</div>
                <div class="fw-bold" style="color:#7dd3fc; margin-bottom:2rem; letter-spacing: 0.15em; text-transform:uppercase; font-size:1rem;">Deputi Karantina Ikan</div>
                <div class="brand-desc" style="font-size:1.25rem;opacity:0.9; margin-bottom:0;">
                    Sistem Informasi Pemantauan<br>
                    Hama &amp; Penyakit Ikan Karantina
                </div>
            </div>
            <div class="brand-copy" style="opacity:0.75;">Deputi Karantina Ikan &copy; {{ date('Y') }}</div>
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
