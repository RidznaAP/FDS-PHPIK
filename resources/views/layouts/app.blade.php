<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>@yield('title', 'SIP-HPIK') — Dashboard Pemantauan HPIK</title>

    {{-- Tabler CSS --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        :root {
            --tblr-font-sans-serif: 'Inter', sans-serif;
            --tblr-primary: #206bc4;
        }
        body { 
            font-family: 'Inter', sans-serif; 
            background-color: #f6f8fb;
        }
        .navbar-brand-text { font-weight: 700; letter-spacing: -0.5px; }
        .role-badge { font-size: 0.7rem; font-weight: 600; }
        
        /* Sidebar Styling Overrides */
        .navbar-vertical {
            background: #1e293b !important;
        }
    </style>
    @yield('styles')
</head>
<body class="antialiased layout-vertical">
    <div class="page">

        {{-- ============================================================ --}}
        {{-- SIDEBAR                                                       --}}
        {{-- ============================================================ --}}
        <aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
            <div class="container-fluid">

                {{-- Logo / Brand --}}
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark">
                    <a href="{{ route('home') }}" class="text-decoration-none text-white d-flex align-items-center gap-2">
                        <span style="font-size:1.5rem;">🐟</span>
                        <span class="navbar-brand-text" style="font-size:1.1rem;">SIP-HPIK</span>
                    </a>
                </h1>

                {{-- User Info (mobile) --}}
                @auth
                <div class="navbar-nav flex-row d-lg-none">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                            <span class="avatar avatar-sm" style="background-color: #3b82f6;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                            <div class="dropdown-header">{{ Auth::user()->name }}</div>
                            <form method="POST" action="{{ route('logout') }}">@csrf
                                <button type="submit" class="dropdown-item text-danger">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endauth

                {{-- Sidebar Nav --}}
                <div class="collapse navbar-collapse" id="sidebar-menu">
                    @auth
                    <ul class="navbar-nav pt-lg-3">

                        {{-- Dashboard --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-dashboard" style="font-size:1.2rem;"></i>
                                </span>
                                <span class="nav-link-title">Dashboard</span>
                            </a>
                        </li>

                        <li class="nav-item mt-2">
                            <span class="nav-link-title text-muted" style="font-size:0.65rem; text-transform:uppercase; letter-spacing:0.08em; padding: 0.5rem 0.75rem;">MODUL</span>
                        </li>

                        {{-- Perencanaan --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('perencanaan*') ? 'active' : '' }}" href="{{ route('perencanaan.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-clipboard-list" style="font-size:1.2rem;"></i>
                                </span>
                                <span class="nav-link-title">Perencanaan</span>
                            </a>
                        </li>

                        {{-- Pelaksanaan --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('pelaksanaan*') ? 'active' : '' }}" href="{{ route('pelaksanaan.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-map-pin" style="font-size:1.2rem;"></i>
                                </span>
                                <span class="nav-link-title">Pelaksanaan</span>
                            </a>
                        </li>

                        {{-- Laboratorium --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('laboratorium*') ? 'active' : '' }}" href="{{ route('laboratorium.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-flask" style="font-size:1.2rem;"></i>
                                </span>
                                <span class="nav-link-title">Laboratorium</span>
                            </a>
                        </li>

                        {{-- Evaluasi --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('evaluasi*') ? 'active' : '' }}" href="{{ route('evaluasi.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-chart-bar" style="font-size:1.2rem;"></i>
                                </span>
                                <span class="nav-link-title">Evaluasi</span>
                            </a>
                        </li>

                        <li class="nav-item mt-2">
                            <span class="nav-link-title text-muted" style="font-size:0.65rem; text-transform:uppercase; letter-spacing:0.08em; padding: 0.5rem 0.75rem;">LAPORAN</span>
                        </li>

                        {{-- Peta GIS --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('peta*') ? 'active' : '' }}" href="{{ route('peta.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-map" style="font-size:1.2rem;"></i>
                                </span>
                                <span class="nav-link-title">Peta GIS</span>
                            </a>
                        </li>

                        {{-- Laporan --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('laporan*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-file-spreadsheet" style="font-size:1.2rem;"></i>
                                </span>
                                <span class="nav-link-title">Laporan & Ekspor</span>
                            </a>
                        </li>

                        {{-- Manajemen Akun & Master Data --}}
                        @php $user = Auth::user(); @endphp
                        
                        {{-- ADMIN Section Header — hanya Pusat --}}
                        @if($user->isPusat())
                            <li class="nav-item mt-2">
                                <span class="nav-link-title text-muted" style="font-size:0.65rem; text-transform:uppercase; letter-spacing:0.08em; padding: 0.5rem 0.75rem;">ADMIN</span>
                            </li>
                        @endif

                        {{-- Manajemen Akun — hanya Pusat --}}
                        @if($user->isPusat())
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('pengguna*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-users" style="font-size:1.2rem;"></i>
                                    </span>
                                    <span class="nav-link-title">Manajemen Akun</span>
                                </a>
                            </li>
                        @endif

                        {{-- Master Data — hanya Pusat --}}
                        @if($user->isPusat())
                            <li class="nav-item dropdown {{ request()->is('master*') ? 'active' : '' }}">
                                <a class="nav-link dropdown-toggle {{ request()->is('master*') ? 'active' : '' }}"
                                   href="#masterSubmenu" data-bs-toggle="collapse"
                                   aria-expanded="{{ request()->is('master*') ? 'true' : 'false' }}">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-database" style="font-size:1.2rem;"></i>
                                    </span>
                                    <span class="nav-link-title">Master Data</span>
                                </a>
                                <div class="collapse {{ request()->is('master*') ? 'show' : '' }}" id="masterSubmenu">
                                    <ul class="nav nav-sm flex-column ms-3 border-start border-secondary ps-2 mt-1">
                                        <li class="nav-item">
                                            <a class="nav-link py-1 {{ request()->is('master/media-pembawa*') ? 'active' : '' }}"
                                               href="{{ route('master.media-pembawa.index') }}">
                                                <i class="ti ti-fish me-1" style="font-size:0.9rem;"></i>
                                                Media Pembawa
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link py-1 {{ request()->is('master/jenis-penyakit*') ? 'active' : '' }}"
                                               href="{{ route('master.jenis-penyakit.index') }}">
                                                <i class="ti ti-virus me-1" style="font-size:0.9rem;"></i>
                                                Jenis Penyakit
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        @endif
                    </ul>

                    {{-- Bottom: User Profile --}}
                    {{-- Bottom: User Profile --}}
                    <div class="mt-auto border-top pt-3 pb-2" style="border-color: rgba(255,255,255,0.1) !important;">
                        {{-- Notification badge for BBKHIT/Pusat --}}
                        @if(Auth::user()->isBbkhit() || Auth::user()->isPusat())
                            @php 
                                $user = Auth::user();
                                $pendingCount = \App\Models\Perencanaan::where('status', 'waiting')
                                    ->when($user->isBbkhit(), function($q) use ($user) {
                                        $q->whereIn('user_id', function($rq) use ($user) {
                                            $rq->select('id')->from('users')
                                              ->where('id', $user->id)
                                              ->orWhere('parent_id', $user->id);
                                        });
                                    })
                                    ->count(); 
                            @endphp
                            @if($pendingCount > 0)
                                <a href="{{ route('perencanaan.index') }}?status=waiting" class="d-flex align-items-center gap-2 px-3 py-2 mb-2 text-decoration-none" style="background:rgba(251,191,36,0.12);border-radius:8px;">
                                    <i class="ti ti-bell-ringing text-warning" style="font-size:1.2rem;"></i>
                                    <span class="text-warning small fw-semibold">{{ $pendingCount }} menunggu approval</span>
                                </a>
                            @endif
                        @endif

                        <div class="d-flex align-items-center gap-2 px-2">
                            <a href="{{ route('profile.index') }}" class="text-decoration-none">
                                <span class="avatar avatar-sm flex-shrink-0" style="background: linear-gradient(135deg, #3b82f6, #8b5cf6);">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                            </a>
                            <div class="flex-fill text-truncate" style="min-width:0;">
                                <a href="{{ route('profile.index') }}" class="text-decoration-none">
                                    <div class="text-white fw-semibold small text-truncate">{{ Auth::user()->name }}</div>
                                </a>
                                <div class="mt-1">
                                    @if(Auth::user()->isUpt())
                                        <span class="badge badge-sm role-badge" style="background:#10b981;">BKHIT</span>
                                    @elseif(Auth::user()->isBbkhit())
                                        <span class="badge badge-sm role-badge" style="background:#f59e0b;">BBKHIT</span>
                                    @else
                                        <span class="badge badge-sm role-badge" style="background:#8b5cf6;">PUSAT</span>
                                    @endif
                                </div>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">@csrf
                                <button type="submit" class="btn btn-sm btn-ghost-light px-2" title="Logout">
                                    <i class="ti ti-logout"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endauth
                </div>
            </div>
        </aside>

        {{-- ============================================================ --}}
        {{-- PAGE WRAPPER                                                  --}}
        {{-- ============================================================ --}}
        <div class="page-wrapper">

            {{-- Page Header --}}
            <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <h2 class="page-title">
                                @yield('page_title', 'Dashboard')
                            </h2>
                            <div class="text-secondary mt-1 small">
                                @yield('page_subtitle', 'Sistem Informasi Pemantauan HPIK')
                            </div>
                        </div>
                        <div class="col-auto ms-auto d-print-none">
                            @yield('page_actions')
                        </div>
                    </div>
                </div>
            </div>

            {{-- Page Body --}}
            <div class="page-body">
                <div class="container-xl">

                    {{-- Flash Messages (auto-dismiss 4s) --}}
                    @if(session('success'))
                    <div class="alert alert-success alert-important alert-dismissible animate-fade-in mb-4" role="alert" id="flash-msg" style="background: rgba(43, 172, 83, 0.05); border: 1px solid rgba(43, 172, 83, 0.2); backdrop-filter: blur(10px);">
                        <div class="d-flex">
                            <i class="ti ti-circle-check fs-2 me-3 text-success"></i>
                            <div>{{ session('success') }}</div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                    @endif
                    @if(session('error'))
                    <div class="alert alert-danger alert-important alert-dismissible animate-fade-in mb-4" role="alert" id="flash-msg" style="background: rgba(214, 57, 57, 0.05); border: 1px solid rgba(214, 57, 57, 0.2); backdrop-filter: blur(10px);">
                        <div class="d-flex">
                            <i class="ti ti-alert-triangle fs-2 me-3 text-danger"></i>
                            <div>{{ session('error') }}</div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                    @endif

                    @yield('content')
                </div>
            </div>

            {{-- Footer --}}
            <footer class="footer footer-transparent d-print-none">
                <div class="container-xl">
                    <div class="row text-center align-items-center flex-row-reverse">
                        <div class="col-lg-auto ms-lg-auto">
                            <small class="text-muted">SIP-HPIK &copy; {{ date('Y') }} — Deputi Karantina Ikan</small>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    {{-- Tabler JS --}}
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
    @yield('scripts')

    {{-- ── #12: Global Modal Konfirmasi ──────────────────────────────────────── --}}
    <div class="modal modal-blur fade" id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body py-4">
                    <div class="text-center">
                        <div class="mb-3" style="font-size:2.5rem;" id="confirmEmoji">⚠️</div>
                        <h5 class="mb-2" id="confirmTitle">Konfirmasi</h5>
                        <p class="text-muted mb-0 small" id="confirmMessage">Apakah Anda yakin?</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-link flex-fill" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn flex-fill" id="confirmBtn" onclick="submitConfirmForm()">Ya, Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Hidden form untuk submit --}}
    <form id="confirmForm" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="_method" id="confirmMethod" value="DELETE">
    </form>

    <script>
    // Ganti semua onclick="confirm()" dengan confirmAction(url, msg, method, btnClass, emoji, title)
    function confirmAction(url, message, method, btnClass, emoji, title) {
        method   = method   || 'DELETE';
        btnClass = btnClass || 'btn-danger';
        emoji    = emoji    || '🗑️';
        title    = title    || 'Hapus Data?';

        document.getElementById('confirmMessage').textContent = message || 'Apakah Anda yakin?';
        document.getElementById('confirmTitle').textContent   = title;
        document.getElementById('confirmEmoji').textContent   = emoji;
        document.getElementById('confirmMethod').value        = method.toUpperCase();
        document.getElementById('confirmForm').action         = url;

        var btn = document.getElementById('confirmBtn');
        btn.className = 'btn flex-fill ' + btnClass;
        btn.textContent = title;

        var modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        modal.show();
    }

    function submitConfirmForm() {
        document.getElementById('confirmForm').submit();
    }

    // Auto-dismiss flash messages after 4 seconds
    setTimeout(function() {
        var flash = document.getElementById('flash-msg');
        if (flash) {
            var alert = bootstrap.Alert.getOrCreateInstance(flash);
            if (alert) alert.close();
        }
    }, 4000);
    </script>
    {{-- ──────────────────────────────────────────────────────────────────────── --}}
</body>
</html>
