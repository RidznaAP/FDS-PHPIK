<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>@yield('title', 'FDS-HPIK') — Dashboard Pemantauan HPIK</title>

    {{-- Tabler CSS --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">

    <style>
        :root {
            --tblr-font-sans-serif: 'Inter', sans-serif;
            --tblr-primary: #0d6efd;
        }
        body { font-family: 'Inter', sans-serif; }
        .navbar-brand-text { font-weight: 700; letter-spacing: -0.5px; }
        .nav-link.active { background-color: rgba(255,255,255,0.1) !important; border-radius: 6px; }
        .role-badge { font-size: 0.7rem; }
    </style>
    @yield('styles')
</head>
<body class="antialiased">
    <div class="wrapper">

        {{-- ============================================================ --}}
        {{-- SIDEBAR                                                       --}}
        {{-- ============================================================ --}}
        <aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark" style="background: linear-gradient(180deg, #0a1628 0%, #0d2137 100%);">
            <div class="container-fluid">

                {{-- Logo / Brand --}}
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark">
                    <a href="{{ route('home') }}" class="text-decoration-none text-white d-flex align-items-center gap-2">
                        <span style="font-size:1.5rem;">🐟</span>
                        <span class="navbar-brand-text" style="font-size:1.1rem;">FDS-HPIK</span>
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
                            <a class="nav-link {{ request()->is('home') ? 'active' : '' }}" href="{{ route('home') }}">
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
                    </ul>

                    {{-- Bottom: User Profile --}}
                    <div class="mt-auto border-top pt-3 pb-2" style="border-color: rgba(255,255,255,0.1) !important;">
                        <div class="d-flex align-items-center gap-2 px-2">
                            <span class="avatar avatar-sm flex-shrink-0" style="background: linear-gradient(135deg, #3b82f6, #8b5cf6);">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            <div class="flex-fill text-truncate" style="min-width:0;">
                                <div class="text-white fw-semibold small text-truncate">{{ Auth::user()->name }}</div>
                                <div class="mt-1">
                                    @if(Auth::user()->isUpt())
                                        <span class="badge badge-sm role-badge" style="background:#10b981;">UPT</span>
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
                                @yield('page_subtitle', 'Sistem Pemantauan HPIK — FDS')
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

                    {{-- Flash Messages --}}
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible mb-3" role="alert">
                        <div class="d-flex">
                            <div><i class="ti ti-check me-2"></i>{{ session('success') }}</div>
                        </div>
                        <a class="btn-close" data-bs-dismiss="alert" aria-label="close"></a>
                    </div>
                    @endif
                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible mb-3" role="alert">
                        <div class="d-flex">
                            <div><i class="ti ti-alert-circle me-2"></i>{{ session('error') }}</div>
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
                            <small class="text-muted">FDS-HPIK &copy; {{ date('Y') }} — Direktorat Jenderal Perikanan</small>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    {{-- Tabler JS --}}
    <script src="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/js/tabler.min.js"></script>
    @yield('scripts')
</body>
</html>
