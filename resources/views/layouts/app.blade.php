<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>@yield('title', 'SIP-HPIK') — Dashboard Pemantauan HPIK</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-instansi.png') }}">

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
            border-radius: 8px; margin: 0px 8px; padding: 6px 10px !important;
            font-size: 0.84rem; font-weight: 500; color: rgba(255,255,255,0.6) !important;
            transition: all 0.18s ease !important;
        }
        .navbar-vertical .nav-link:hover { color: #fff !important; background: var(--sidebar-hover) !important; }
        .navbar-vertical .nav-link.active { color: #fff !important; background: var(--sidebar-active) !important; font-weight: 600; }
        .navbar-vertical .nav-item-header {
            font-size: 0.62rem; font-weight: 700; letter-spacing: 0.1em;
            color: rgba(255,255,255,0.25) !important; padding: 10px 14px 2px !important;
        }
        .navbar-vertical hr { border-color: rgba(255,255,255,0.07) !important; margin: 4px 12px !important; }

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

        /* ── Mobile Responsive Tables to Cards ── */
        @media (max-width: 767.98px) {
            .table-mobile-cards, .table-mobile-cards tbody, .table-mobile-cards tr, .table-mobile-cards td {
                display: block; width: 100%;
            }
            .table-mobile-cards thead { display: none; }
            .table-mobile-cards tr {
                background: #fff; border: 1px solid rgba(0,0,0,0.08); border-radius: 12px;
                margin-bottom: 1rem; padding: 1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            }
            .table-mobile-cards td {
                padding: 0.5rem 0 !important; border: none !important;
                display: flex; justify-content: space-between; align-items: center;
                text-align: right;
            }
            .table-mobile-cards td::before {
                content: attr(data-label);
                font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;
                margin-right: 1rem; text-align: left;
            }
            .table-mobile-cards td:last-child {
                margin-top: 0.5rem; padding-top: 1rem !important; border-top: 1px dashed #e2e8f0 !important;
                justify-content: center;
            }
            .aksi-sticky-td { border: none !important; position: static !important; background: transparent !important; }
        }

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
        @include('layouts.sidebar')

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
                            @hasSection('breadcrumb')
                                @yield('breadcrumb')
                            @else
                                <h2 class="page-title">
                                    @yield('page_title', 'Dashboard')
                                </h2>
                                <div class="text-secondary mt-1 small">
                                    @yield('page_subtitle', 'Sistem Informasi Pemantauan HPIK')
                                </div>
                            @endif
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
    <script src="{{ asset('js/app-global.js') }}"></script>
    <script>
    // Inisialisasi Tooltip & Polling menggunakan fungsi dari app-global.js
    document.addEventListener('DOMContentLoaded', function() {
        @auth
            initNotifPolling('{{ route("notifikasi.jumlah") }}');
        @endauth
    });
    </script>

    {{-- Global Confirmation Modal (No Blur) --}}
    <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true">
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
    {{-- Hidden form for confirmAction --}}
    <form id="confirmForm" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="_method" id="confirmMethod" value="DELETE">
    </form>

    @yield('scripts')
    @stack('scripts')

</body>
</html>
