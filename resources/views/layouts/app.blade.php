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
        /* ═══════════════════════════════════════════════════
           GLOBAL PREMIUM DESIGN SYSTEM — SIP-HPIK
        ═══════════════════════════════════════════════════ */
        :root {
            --tblr-font-sans-serif: 'Inter', sans-serif;
            --tblr-primary: #2563eb;
            --tblr-primary-rgb: 37, 99, 235;
            --sidebar-bg: #0f172a;
            --sidebar-hover: rgba(255,255,255,0.07);
            --sidebar-active: rgba(37,99,235,0.22);
            --card-shadow: 0 1px 3px rgba(0,0,0,.05), 0 4px 16px rgba(0,0,0,.04);
            --card-shadow-hover: 0 4px 12px rgba(0,0,0,.08), 0 12px 32px rgba(0,0,0,.05);
        }

        /* ── Base ── */
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            color: #1e293b;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Sidebar ── */
        .navbar-vertical { background: var(--sidebar-bg) !important; border-right: 1px solid rgba(255,255,255,0.05) !important; }
        .navbar-vertical .navbar-brand-text { font-weight: 700; font-size: 1.05rem; letter-spacing: -0.3px; }
        .navbar-vertical .nav-link {
            border-radius: 8px; margin: 1px 6px; padding: 8px 12px !important;
            font-size: 0.875rem; font-weight: 500; color: rgba(255,255,255,0.6) !important;
            transition: all 0.18s ease !important;
        }
        .navbar-vertical .nav-link:hover { color: #fff !important; background: var(--sidebar-hover) !important; }
        .navbar-vertical .nav-link.active { color: #fff !important; background: var(--sidebar-active) !important; font-weight: 600; }
        .navbar-vertical .nav-item-header {
            font-size: 0.65rem; font-weight: 700; letter-spacing: 0.1em;
            color: rgba(255,255,255,0.28) !important; padding: 14px 18px 4px !important;
        }
        .navbar-vertical hr { border-color: rgba(255,255,255,0.07) !important; margin: 8px 12px !important; }

        /* ── Cards ── */
        .card {
            border: 1px solid rgba(0,0,0,0.06) !important;
            border-radius: 12px !important;
            box-shadow: var(--card-shadow) !important;
            transition: box-shadow 0.2s ease !important;
        }
        .card:hover { box-shadow: var(--card-shadow-hover) !important; }
        .card-header { border-bottom: 1px solid rgba(0,0,0,0.06) !important; background: #fff !important; padding: 1rem 1.25rem !important; }
        .card-footer { border-top: 1px solid rgba(0,0,0,0.06) !important; background: #fafbfc !important; }

        /* ── Tables ── */
        .table-vcenter td, .table-vcenter th { vertical-align: middle; }
        .table thead th {
            background: #f8fafc !important; font-size: 0.7rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.06em; color: #64748b !important;
            border-bottom: 2px solid #e2e8f0 !important; padding: 10px 16px !important; white-space: nowrap;
        }
        .table tbody tr { transition: background 0.15s ease; }
        .table tbody tr:hover { background: rgba(37, 99, 235, 0.025) !important; }
        .table tbody td { padding: 12px 16px !important; border-bottom: 1px solid #f1f5f9 !important; font-size: 0.875rem; }
        .table tbody tr:last-child td { border-bottom: none !important; }

        /* ── Sort Buttons ── */
        .sort-th { padding: 0 !important; vertical-align: middle !important; }
        .sort-btn {
            display: flex !important; align-items: center; justify-content: space-between;
            padding: 10px 16px !important; color: #64748b !important; font-weight: 700 !important;
            font-size: 0.7rem !important; text-transform: uppercase !important; letter-spacing: 0.06em !important;
            text-decoration: none !important; width: 100%; background: #f8fafc; border: none; white-space: nowrap;
            transition: all 0.15s ease;
        }
        .sort-btn:hover { color: var(--tblr-primary) !important; background: rgba(37,99,235,0.04) !important; }
        .sort-active { color: var(--tblr-primary) !important; background: rgba(37,99,235,0.06) !important; }
        .sort-icon { display: inline-flex; align-items: center; margin-left: 6px; opacity: 0.5; }
        .sort-active .sort-icon { opacity: 1; color: var(--tblr-primary); }

        /* ── Buttons ── */
        .btn { border-radius: 8px !important; font-weight: 500 !important; font-size: 0.875rem !important; transition: all 0.18s ease !important; }
        .btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,.12); }
        .btn:active { transform: translateY(0); }
        .btn-sm { font-size: 0.8rem !important; padding: 4px 10px !important; }
        .btn-pill { border-radius: 50px !important; }

        /* ── Badges ── */
        .badge { font-size: 0.68rem; font-weight: 600; letter-spacing: 0.03em; border-radius: 6px; padding: 3px 8px; }

        /* ── Form Controls ── */
        .form-control, .form-select {
            border-radius: 8px !important; border: 1.5px solid #e2e8f0 !important;
            font-size: 0.875rem !important; transition: all 0.15s !important;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--tblr-primary) !important;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1) !important;
        }
        .input-icon .form-control { padding-left: 2.5rem !important; }

        /* ── Page Header ── */
        .page-header { background: #fff; border-bottom: 1px solid #e2e8f0; padding: 16px 0 !important; }
        .page-title { font-size: 1.2rem !important; font-weight: 700 !important; color: #0f172a !important; margin: 0 !important; }

        /* ── Flash Alert ── */
        .alert { border-radius: 10px !important; font-size: 0.875rem; }

        /* ── Pagination ── */
        .pagination .page-link { border-radius: 7px !important; margin: 0 2px; font-size: 0.8rem; font-weight: 500; }

        /* ── Role Badge ── */
        .role-badge { font-size: 0.62rem; font-weight: 700; letter-spacing: 0.05em; padding: 2px 7px; border-radius: 20px; }

        /* ── Upload Zone ── */
        .upload-zone { border: 2px dashed #cbd5e1 !important; transition: all 0.2s ease; }
        .upload-zone:hover { border-color: var(--tblr-primary) !important; background: rgba(37,99,235,0.02); }

        /* ── Empty State ── */
        .empty-state { padding: 4rem 2rem; text-align: center; }
        .empty-state-icon {
            width: 72px; height: 72px; background: #f1f5f9; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            margin-bottom: 1.25rem; font-size: 1.75rem;
        }
        .empty-state h4 { font-weight: 700; color: #475569; margin-bottom: 0.5rem; }
        .empty-state p { color: #94a3b8; font-size: 0.875rem; max-width: 300px; margin: 0 auto 1.25rem; }

        /* ── List group ── */
        .list-group-item { border-color: #f1f5f9 !important; transition: background 0.15s; }
        .list-group-item:hover { background: #f8fafc !important; }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

        /* ── Page enter animation ── */
        @keyframes fadeInUp {
            from { opacity:0; transform:translateY(10px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .page-body > .container-xl { animation: fadeInUp 0.22s ease both; }
    </style>
    @yield('styles')
    @stack('styles')
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

                        {{-- 1. Perencanaan --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('perencanaan*') ? 'active' : '' }}" href="{{ route('perencanaan.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-clipboard-list" style="font-size:1.2rem;"></i>
                                </span>
                                <span class="nav-link-title">Perencanaan</span>
                            </a>
                        </li>

                        {{-- 2. Pelaksanaan --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('pelaksanaan*') || request()->is('laboratorium*') ? 'active' : '' }}" href="{{ route('pelaksanaan.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-map-pin" style="font-size:1.2rem;"></i>
                                </span>
                                <span class="nav-link-title">Pelaksanaan</span>
                            </a>
                        </li>

                        {{-- 3. Pelaporan (upload file seminar) --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('pelaporan*') ? 'active' : '' }}" href="{{ route('pelaporan.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-report" style="font-size:1.2rem;"></i>
                                </span>
                                <span class="nav-link-title">Pelaporan</span>
                            </a>
                        </li>

                        {{-- 4. Evaluasi (upload file seminar) --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('evaluasi*') ? 'active' : '' }}" href="{{ route('evaluasi.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-chart-bar" style="font-size:1.2rem;"></i>
                                </span>
                                <span class="nav-link-title">Evaluasi</span>
                            </a>
                        </li>

                        {{-- 5. Export Data (dropdown: Peta GIS + Laporan & Ekspor) --}}
                        <li class="nav-item {{ request()->is('peta*') || request()->is('laporan*') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle {{ request()->is('peta*') || request()->is('laporan*') ? 'active' : '' }}"
                               href="#exportDataSubmenu" data-bs-toggle="collapse"
                               aria-expanded="{{ request()->is('peta*') || request()->is('laporan*') ? 'true' : 'false' }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-file-export" style="font-size:1.2rem;"></i>
                                </span>
                                <span class="nav-link-title">Export Data</span>
                            </a>
                            <div class="collapse {{ request()->is('peta*') || request()->is('laporan*') ? 'show' : '' }}" id="exportDataSubmenu">
                                <ul class="nav nav-sm flex-column ms-3 border-start border-secondary ps-2 mt-1">
                                    <li class="nav-item">
                                        <a class="nav-link py-1 {{ request()->is('peta*') ? 'active' : '' }}" href="{{ route('peta.index') }}">
                                            <i class="ti ti-map me-1" style="font-size:0.9rem;"></i>
                                            Peta GIS
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link py-1 {{ request()->is('laporan*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
                                            <i class="ti ti-file-spreadsheet me-1" style="font-size:0.9rem;"></i>
                                            Laporan & Ekspor
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>

                        {{-- Manajemen Akun & Master Data --}}
                        @php $user = Auth::user(); @endphp
                        
                        {{-- ADMIN Section Header — hanya Pusat --}}
                        @if($user->isPusat())
                            <li class="nav-item mt-2">
                                <span class="nav-link-title text-muted" style="font-size:0.65rem; text-transform:uppercase; letter-spacing:0.08em; padding: 0.5rem 0.75rem;">ADMIN</span>
                            </li>
                        @endif

                        {{-- Notifikasi (semua role) --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('notifikasi*') ? 'active' : '' }}" href="{{ route('notifikasi.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block position-relative">
                                    <i class="ti ti-bell" style="font-size:1.2rem;"></i>
                                    <span id="sidebar-notif-badge" class="position-absolute badge bg-danger d-none"
                                          style="top:-4px;right:-6px;font-size:.55rem;min-width:16px;height:16px;padding:2px 4px;border-radius:8px;"></span>
                                </span>
                                <span class="nav-link-title d-flex align-items-center justify-content-between">
                                    Notifikasi
                                    <span id="sidebar-notif-count" class="badge bg-danger ms-1 d-none" style="font-size:.65rem;"></span>
                                </span>
                            </a>
                        </li>

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
            @if(!View::hasSection('no_header'))
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
            @endif

            {{-- Page Body --}}
            <div class="page-body">
                <div class="container-xl">

                    {{-- Flash Messages (auto-dismiss 5s) --}}
                    @if(session('success'))
                    <div class="alert alert-dismissible animate-fade-in mb-4" role="alert" id="flash-msg"
                         style="background:#dcfce7; border:1.5px solid #16a34a; color:#14532d; border-radius:12px; padding:14px 18px;">
                        <div class="d-flex align-items-center gap-3">
                            <i class="ti ti-circle-check" style="font-size:1.5rem;color:#16a34a;flex-shrink:0;"></i>
                            <div class="fw-semibold" style="color:#14532d;">{{ session('success') }}</div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"
                           style="filter:brightness(0.3);"></a>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-dismissible animate-fade-in mb-4" role="alert" id="flash-msg-err"
                         style="background:#fee2e2; border:1.5px solid #dc2626; color:#7f1d1d; border-radius:12px; padding:14px 18px;">
                        <div class="d-flex align-items-center gap-3">
                            <i class="ti ti-alert-triangle" style="font-size:1.5rem;color:#dc2626;flex-shrink:0;"></i>
                            <div class="fw-semibold" style="color:#7f1d1d;">{{ session('error') }}</div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"
                           style="filter:brightness(0.3);"></a>
                    </div>
                    @endif

                    @if(session('warning'))
                    <div class="alert alert-dismissible animate-fade-in mb-4" role="alert" id="flash-msg-warn"
                         style="background:#fef9c3; border:1.5px solid #ca8a04; color:#713f12; border-radius:12px; padding:14px 18px;">
                        <div class="d-flex align-items-center gap-3">
                            <i class="ti ti-alert-circle" style="font-size:1.5rem;color:#ca8a04;flex-shrink:0;"></i>
                            <div class="fw-semibold" style="color:#713f12;">{{ session('warning') }}</div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"
                           style="filter:brightness(0.3);"></a>
                    </div>
                    @endif

                    @if(session('info'))
                    <div class="alert alert-dismissible animate-fade-in mb-4" role="alert" id="flash-msg-info"
                         style="background:#dbeafe; border:1.5px solid #2563eb; color:#1e3a8a; border-radius:12px; padding:14px 18px;">
                        <div class="d-flex align-items-center gap-3">
                            <i class="ti ti-info-circle" style="font-size:1.5rem;color:#2563eb;flex-shrink:0;"></i>
                            <div class="fw-semibold" style="color:#1e3a8a;">{{ session('info') }}</div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"
                           style="filter:brightness(0.3);"></a>
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
    @stack('scripts')

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
        method   = (method || 'DELETE').toUpperCase();
        btnClass = btnClass || (method === 'DELETE' ? 'btn-danger' : 'btn-primary');
        
        // Default emoji & title based on method if not provided
        if (!emoji) {
            emoji = (method === 'DELETE') ? '🗑️' : '🚀';
        }
        if (!title) {
            title = (method === 'DELETE') ? 'Hapus Data?' : 'Konfirmasi Tindakan';
        }

        document.getElementById('confirmMessage').textContent = message || 'Apakah Anda yakin?';
        document.getElementById('confirmTitle').textContent   = title;
        document.getElementById('confirmEmoji').textContent   = emoji;
        
        // Handle Laravel Method Spoofing correctly
        var methodInput = document.getElementById('confirmMethod');
        if (method === 'POST') {
            methodInput.disabled = true; // Disable _method input for real POST request
        } else {
            methodInput.disabled = false;
            methodInput.value = method;
        }

        document.getElementById('confirmForm').action = url || '#';

        var btn = document.getElementById('confirmBtn');
        btn.className = 'btn flex-fill ' + btnClass;
        btn.textContent = (method === 'DELETE') ? 'Ya, Hapus' : 'Ya, Lanjutkan';
        
        // RESET onclick to default behavior (in case it was overridden by page-specific logic)
        btn.onclick = function() { submitConfirmForm(); };

        var modal = new bootstrap.Modal(document.getElementById('confirmModal'));
        modal.show();
    }

    function submitConfirmForm() {
        document.getElementById('confirmForm').submit();
    }

    // Auto-dismiss flash messages after 5 seconds
    setTimeout(function() {
        ['flash-msg','flash-msg-err','flash-msg-warn','flash-msg-info'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el) {
                var instance = bootstrap.Alert.getOrCreateInstance(el);
                if (instance) instance.close();
            }
        });
    }, 5000);
    </script>
    {{-- ──────────────────────────────────────────────────────────────────────── --}}
<script>
// ── Notification Polling ──────────────────────────────────────
(function() {
    @auth
    function updateNotifBadge() {
        fetch('{{ route("notifikasi.jumlah") }}', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            const count = data.count || 0;
            const badge     = document.getElementById('sidebar-notif-badge');
            const badgeText = document.getElementById('sidebar-notif-count');
            if (badge) {
                badge.textContent = count > 9 ? '9+' : count;
                badge.classList.toggle('d-none', count === 0);
            }
            if (badgeText) {
                badgeText.textContent = count > 9 ? '9+' : count;
                badgeText.classList.toggle('d-none', count === 0);
            }
        })
        .catch(() => {});
    }
    updateNotifBadge();
    setInterval(updateNotifBadge, 30000); // tiap 30 detik
    @endauth
})();
</script>

</body>
</html>
