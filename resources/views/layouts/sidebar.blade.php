        <aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
            <div class="container-fluid">

                {{-- Logo / Brand --}}
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark">
                    <a href="{{ route('home') }}" class="text-decoration-none text-white d-flex align-items-center gap-2">
                        {{-- Logo Instansi: letakkan file logo di public/images/logo-instansi.png --}}
                        @if(file_exists(public_path('images/logo-instansi.png')))
                            <img src="{{ asset('images/logo-instansi.png') }}" alt="Logo Badan Karantina Indonesia"
                                 style="height:36px;width:auto;object-fit:contain;background:#fff;border-radius:50%;padding:2px;">
                        @else
                            <span style="font-size:1.5rem;">🐟</span>
                        @endif
                        <div class="d-flex flex-column lh-1">
                            <span class="navbar-brand-text fw-bold" style="font-size:.95rem;letter-spacing:.02em;">SIP-HPIK</span>
                            <span style="font-size:.6rem;opacity:.65;letter-spacing:.04em;text-transform:uppercase;">Badan Karantina Indonesia</span>
                        </div>
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

                        {{-- 4. Evaluasi (upload dokumen seperti Pelaporan) --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('seminar/evaluasi*') || request()->is('evaluasi*') ? 'active' : '' }}"
                               href="{{ route('seminar.index', 'evaluasi') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-file-analytics" style="font-size:1.2rem;"></i>
                                </span>
                                <span class="nav-link-title">Evaluasi</span>
                            </a>
                        </li>

                        {{-- 5. Export Data / Laporan & Ekspor --}}
                        @if(Auth::user()->isPusat())
                        <li class="nav-item {{ request()->is('peta*') || request()->is('laporan*') ? 'active' : '' }}">
                            <a class="nav-link dropdown-toggle {{ request()->is('peta*') || request()->is('laporan*') ? 'active' : '' }}"
                               href="#exportDataSubmenu" data-bs-toggle="collapse"
                               aria-expanded="{{ request()->is('peta*') || request()->is('laporan*') ? 'true' : 'false' }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-file-export" style="font-size:1.2rem;"></i>
                                </span>
                                <span class="nav-link-title">Data & Laporan</span>
                            </a>
                            <div class="collapse {{ request()->is('peta*') || request()->is('laporan*') ? 'show' : '' }}" id="exportDataSubmenu">
                                <ul class="nav nav-sm flex-column ms-3 border-start border-secondary ps-2 mt-1">
                                    <li class="nav-item">
                                        <a class="nav-link py-1 {{ request()->is('peta*') ? 'active' : '' }}" href="{{ route('peta.index') }}">
                                            <i class="ti ti-map me-1" style="font-size:0.9rem;"></i>
                                            Peta Pemantauan
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
                        @else
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('laporan*') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-file-export" style="font-size:1.2rem;"></i>
                                </span>
                                <span class="nav-link-title">Export Data</span>
                            </a>
                        </li>
                        @endif

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
                            @if(($pendingApprovalCount ?? 0) > 0)
                                <a href="{{ route('perencanaan.index') }}?status=waiting" class="d-flex align-items-center gap-2 px-3 py-2 mb-2 text-decoration-none" style="background:rgba(251,191,36,0.12);border-radius:8px;">
                                    <i class="ti ti-bell-ringing text-warning" style="font-size:1.2rem;"></i>
                                    <span class="text-warning small fw-semibold">{{ $pendingApprovalCount }} menunggu approval</span>
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
