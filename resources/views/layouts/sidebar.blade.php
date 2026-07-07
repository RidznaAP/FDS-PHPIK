        <aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
            <style>
                @media (max-width: 991.98px) {
                    .navbar-brand img { height: 38px !important; margin-right: 10px !important; }
                    .navbar-brand .fw-bolder { font-size: 1.15rem !important; }
                    .navbar-brand .text-uppercase { font-size: 0.55rem !important; line-height: 1.1 !important; }
                }
            </style>
            <div class="container-fluid">

                {{-- Logo / Brand --}}
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <h1 class="navbar-brand navbar-brand-autodark">
                    <a href="{{ route('home') }}" class="text-decoration-none d-flex align-items-center pt-2 pb-1 px-1">
                        {{-- Logo Instansi: cached base64 to prevent flicker --}}
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
                                 style="height:36px;width:auto;object-fit:contain;margin-right:12px;filter:drop-shadow(0 4px 6px rgba(0,0,0,0.3));" decoding="async">
                        @else
                            <div style="height:36px;width:36px;background:rgba(255,255,255,0.1);border-radius:10px;display:flex;align-items:center;justify-content:center;margin-right:12px;">
                                <span style="font-size:1.2rem;">🐟</span>
                            </div>
                        @endif
                        <div class="d-flex flex-column justify-content-center">
                            <span class="fw-bolder text-white" style="font-size:1.1rem;letter-spacing:-0.02em;line-height:1.1;margin-bottom:1px;">SIP-HPIK</span>
                            <span class="fw-bold text-uppercase" style="color:#7dd3fc;font-size:0.55rem;letter-spacing:0.06em;line-height:1.2;">Deputi Karantina Ikan</span>
                        </div>
                    </a>
                </h1>

                {{-- User Info (mobile) --}}
                @auth
                <div class="navbar-nav flex-row d-lg-none">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link d-flex lh-1 text-reset p-0" data-bs-toggle="dropdown">
                            <span class="avatar avatar-sm" style="background: linear-gradient(135deg, #3b82f6, #8b5cf6); border: 1px solid rgba(255,255,255,0.2);">
                                {{ strtoupper(substr(Auth::user()->upt_asal ?? Auth::user()->name, 0, 1)) }}
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow shadow-lg border-0" style="border-radius:12px;">
                            <div class="dropdown-header bg-light-lt py-3">
                                <div class="fw-bold text-dark">{{ Auth::user()->upt_asal ?? Auth::user()->name }}</div>
                                <div class="text-muted small mt-1">{{ Auth::user()->email }}</div>
                            </div>
                            <a href="{{ route('profile.index') }}" class="dropdown-item py-2">
                                <i class="ti ti-user me-2"></i> Profil Saya
                            </a>
                            <div class="dropdown-divider my-1"></div>
                            <form method="POST" action="{{ route('logout') }}">@csrf
                                <button type="submit" class="dropdown-item text-danger py-2 fw-bold">
                                    <i class="ti ti-logout me-2"></i> Log Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endauth

                {{-- Sidebar Nav --}}
                <div class="collapse navbar-collapse" id="sidebar-menu">
                    @auth
                    <ul class="navbar-nav pt-lg-1">

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
                            <a class="nav-link {{ request()->is('pelaksanaan*') ? 'active' : '' }}" href="{{ route('pelaksanaan.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-map-pin" style="font-size:1.2rem;"></i>
                                </span>
                                <span class="nav-link-title">Pelaksanaan</span>
                            </a>
                        </li>

                        {{-- 3. Laboratorium --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('laboratorium*') ? 'active' : '' }}" href="{{ route('laboratorium.index') }}">
                                <span class="nav-link-icon d-md-none d-lg-inline-block">
                                    <i class="ti ti-microscope" style="font-size:1.2rem;"></i>
                                </span>
                                <span class="nav-link-title">Laboratorium</span>
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
                               href="{{ route('evaluasi.data.index') }}">
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
                                        <li class="nav-item">
                                            <a class="nav-link py-1 {{ request()->is('master/metode-uji*') ? 'active' : '' }}"
                                               href="{{ route('master.metode-uji.index') }}">
                                                <i class="ti ti-test-pipe me-1" style="font-size:0.9rem;"></i>
                                                Metode Uji
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>

                            {{-- Audit Log --}}
                            <li class="nav-item">
                                <a class="nav-link {{ request()->is('pengaturan/audit-log*') ? 'active' : '' }}" href="{{ route('audit.index') }}">
                                    <span class="nav-link-icon d-md-none d-lg-inline-block">
                                        <i class="ti ti-activity" style="font-size:1.2rem;"></i>
                                    </span>
                                    <span class="nav-link-title">Audit Log</span>
                                </a>
                            </li>
                        @endif
                    </ul>

                    {{-- Bottom: User Profile --}}
                    <div class="mt-auto border-top pt-3 pb-3 px-3" style="border-color: rgba(255,255,255,0.06) !important;">
                        
                        {{-- Notification badge for BBKHIT/Pusat --}}
                        @if(Auth::user()->isBbkhit() || Auth::user()->isPusat())
                            @php
                                $pendingApprovalCount = \App\Models\Perencanaan::where('status', 'submitted')->count();
                            @endphp
                            @if($pendingApprovalCount > 0)
                                <a href="{{ route('perencanaan.index') }}?status=waiting" class="d-flex align-items-center gap-2 px-3 py-2 mb-3 text-decoration-none shadow-sm" style="background:rgba(251,191,36,0.12); border-radius:10px; border: 1px solid rgba(251,191,36,0.25);">
                                    <i class="ti ti-bell-ringing text-warning" style="font-size:1.1rem;"></i>
                                    <span class="text-warning small fw-bold">{{ $pendingApprovalCount }} Perlu Approval</span>
                                </a>
                            @endif
                        @endif
                        
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <div class="position-relative flex-shrink-0">
                                <span class="avatar avatar-sm rounded-3" style="background: linear-gradient(135deg, #3b82f6, #8b5cf6); border: 1px solid rgba(255,255,255,0.12); box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </span>
                                <span class="badge bg-green position-absolute bottom-0 end-0 border-dark border-2" style="width:8px;height:8px;padding:0;border-radius:50%;" title="Online"></span>
                            </div>
                            <div class="flex-fill min-w-0">
                                <div class="text-white fw-semibold" style="line-height: 1.1; font-size: 0.75rem; display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;" title="{{ Auth::user()->name }}">
                                    {{ Auth::user()->name }}
                                </div>
                                <div class="text-muted mt-0" style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.02em;">
                                    {{ Auth::user()->upt_asal ?? Auth::user()->role }}
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-1">
                            <a href="{{ route('profile.index') }}" class="btn btn-dark flex-fill py-1 border-0 text-center shadow-none" style="background: rgba(255,255,255,0.06); font-size: 0.65rem;">
                                <i class="ti ti-user-cog"></i> Profil
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="flex-fill">
                                @csrf
                                <button type="submit" class="btn btn-ghost-danger w-100 py-1 text-center shadow-none" style="font-size: 0.65rem;">
                                    <i class="ti ti-logout"></i> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                    @endauth
                </div>
            </div>
        </aside>
