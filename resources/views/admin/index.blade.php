<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>@yield('title', 'WCMC AIMS')</title>

    <script src="https://kit.fontawesome.com/6cea1e7bdb.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="{{ asset('vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/base/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="{{ asset('sweetAlert/sweetAlert.js') }}"></script>
    @stack('styles')

    <style>
        /* ── Design tokens ── */
        :root {
            --primary: #2563eb;
            --primary-dark: #1e3a5f;
            --success: #16a34a;
            --warning: #d97706;
            --danger: #dc2626;
            --info: #0891b2;
            --text: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --bg: #f1f5f9;
            --radius: 8px;
        }

        /* ── Base ── */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important;
            color: var(--text);
            background: var(--bg) !important;
        }

        /* ── Cards ── */
        .card {
            border: none !important;
            border-radius: var(--radius) !important;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.07) !important;
        }

        .card-body {
            padding: 24px !important;
        }

        /* ── Page header (form pages) ── */
        .page-card {
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.07);
            background: #fff;
        }

        .page-card-header {
            padding: 18px 24px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .page-card-header h5 {
            margin: 0;
            font-weight: 700;
            font-size: 15px;
            color: var(--text);
        }

        .page-card-body {
            padding: 28px 24px;
        }

        /* ── Form controls ── */
        .form-group {
            margin-bottom: 18px;
        }

        .form-group label {
            display: block;
            font-size: 11.5px;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
        }

        .form-control {
            border: 1.5px solid var(--border) !important;
            border-radius: 6px !important;
            padding: 9px 13px !important;
            font-size: 13.5px !important;
            font-family: 'Inter', sans-serif !important;
            background: #f8fafc !important;
            color: var(--text) !important;
            transition: border-color 0.18s, box-shadow 0.18s !important;
            box-shadow: none !important;
        }

        .form-control:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
            background: #fff !important;
            outline: none !important;
        }

        select.form-control,
        select.form-select {
            border: 1.5px solid var(--border) !important;
            border-radius: 6px !important;
            padding: 9px 13px !important;
            font-size: 13.5px !important;
            background: #f8fafc !important;
            color: var(--text) !important;
            height: auto !important;
        }

        select.form-control:focus,
        select.form-select:focus {
            border-color: var(--primary) !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
            background: #fff !important;
            outline: none !important;
        }

        /* ── Icon alignment (MDI + FontAwesome) ── */
        .mdi,
        [class^="mdi-"],
        [class*=" mdi-"] {
            vertical-align: middle;
            line-height: 1;
            position: relative;
            top: -1px;
        }

        .btn .mdi,
        .btn [class^="mdi-"],
        .btn [class*=" mdi-"],
        .btn .fa,
        .btn [class^="fa-"],
        .btn [class*=" fa-"] {
            vertical-align: middle;
            position: relative;
            top: -1px;
            line-height: 1;
        }

        /* Sidebar menu icons */
        .sidebar .menu-icon {
            vertical-align: middle;
            top: 0 !important;
        }

        /* ── Buttons ── */
        .btn {
            font-size: 13px !important;
            font-weight: 600 !important;
            border-radius: 6px !important;
            padding: 9px 20px !important;
            letter-spacing: 0.01em;
            transition: all 0.18s ease !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            gap: 5px !important;
            vertical-align: middle !important;
            line-height: 1.5 !important;
            white-space: nowrap;
        }

        .btn .mdi,
        .btn .fa {
            top: 0 !important;
        }

        /* Normalize button group alignment */
        .btn+.btn,
        .btn+a.btn,
        a.btn+.btn {
            margin-left: 0;
        }

        .btn-primary {
            background: var(--primary) !important;
            border-color: var(--primary) !important;
            color: #fff !important;
        }

        .btn-primary:hover {
            background: #1d4ed8 !important;
            border-color: #1d4ed8 !important;
        }

        .btn-secondary {
            background: #64748b !important;
            border-color: #64748b !important;
            color: #fff !important;
        }

        .btn-secondary:hover {
            background: #475569 !important;
            border-color: #475569 !important;
        }

        .btn-success {
            background: var(--success) !important;
            border-color: var(--success) !important;
            color: #fff !important;
        }

        .btn-success:hover {
            background: #15803d !important;
            border-color: #15803d !important;
        }

        .btn-danger {
            background: var(--danger) !important;
            border-color: var(--danger) !important;
            color: #fff !important;
        }

        .btn-danger:hover {
            background: #b91c1c !important;
            border-color: #b91c1c !important;
        }

        .btn-info {
            background: var(--info) !important;
            border-color: var(--info) !important;
            color: #fff !important;
        }

        .btn-sm {
            padding: 6px 14px !important;
            font-size: 12px !important;
        }

        /* ── Status badges ── */
        .badge-pill-status {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .s-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .s-published {
            background: #d1fae5;
            color: #065f46;
        }

        .s-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        /* ── Section divider ── */
        .form-section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            margin-bottom: 16px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--border);
        }

        /* ── Sidebar ── */
        .sidebar .nav-item.active>.nav-link {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 5px;
        }

        .sidebar .nav-item>.nav-link {
            transition: background 0.18s ease;
            border-radius: 5px;
        }

        .sidebar .nav-item>.nav-link:hover {
            background: rgba(255, 255, 255, 0.09);
        }

        /* ── DataTable tweaks ── */
        .dataTables_wrapper .dataTables_length select,
        .dataTables_wrapper .dataTables_filter input {
            border: 1.5px solid var(--border) !important;
            border-radius: 6px !important;
            padding: 5px 10px !important;
            font-size: 13px !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            background: var(--primary) !important;
            border-color: var(--primary) !important;
            color: #fff !important;
            border-radius: 5px !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: #f1f5f9 !important;
            border-color: var(--border) !important;
            color: var(--text) !important;
            border-radius: 5px !important;
        }

        .dataTables_info {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* ── Table ── */
        .table th {
            font-size: 11.5px !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
            letter-spacing: 0.05em !important;
            color: var(--text-muted) !important;
        }

        .table td {
            font-size: 13.5px !important;
        }

        /* ── Navbar dropdown ── */
        .navbar .nav-profile .dropdown-menu {
            z-index: 9998 !important;
            display: block;
            opacity: 0;
            visibility: hidden;
            transform: translateY(6px);
            transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s;
            pointer-events: none;
        }

        .navbar .nav-profile .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
            pointer-events: auto;
        }

        .navbar .nav-profile .nav-link {
            cursor: pointer;
        }

        /* ── Loader ── */
        #page-loader {
            position: fixed;
            inset: 0;
            background: #fff;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: opacity 0.3s ease;
        }

        .loader-ring {
            width: 44px;
            height: 44px;
            border: 4px solid #e8eaf6;
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        .loader-text {
            margin-top: 12px;
            font-size: 12px;
            color: #9e9e9e;
            letter-spacing: 0.04em;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ── Stat cards ── */
        .stat-label {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 4px;
        }

        .stat-value {
            font-size: 1.6rem;
            font-weight: 700;
            line-height: 1;
        }
    </style>

    <style>
        /* ── Page loader ── */
        #page-loader {
            position: fixed;
            inset: 0;
            background: #fff;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: opacity 0.3s ease;
        }

        .loader-ring {
            width: 44px;
            height: 44px;
            border: 4px solid #e8eaf6;
            border-top-color: #4285f4;
            border-radius: 50%;
            animation: spin 0.7s linear infinite;
        }

        .loader-text {
            margin-top: 12px;
            font-size: 12px;
            color: #9e9e9e;
            letter-spacing: 0.04em;
            font-family: Arial, sans-serif;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* ── Sidebar active state ── */
        .sidebar .nav-item.active>.nav-link {
            background: rgba(255, 255, 255, 0.13);
            border-radius: 4px;
        }

        .sidebar .nav-item>.nav-link {
            transition: background 0.18s ease;
        }

        .sidebar .nav-item>.nav-link:hover {
            background: rgba(255, 255, 255, 0.08);
            border-radius: 4px;
        }

        /* ── Stat cards ── */
        .stat-card {
            border-left: 4px solid;
            border-radius: 6px;
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.10) !important;
        }

        .stat-card .stat-icon {
            font-size: 2.2rem;
            opacity: 0.25;
        }

        .stat-card .stat-label {
            font-size: 0.68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 4px;
        }

        .stat-card .stat-value {
            font-size: 1.6rem;
            font-weight: 700;
            line-height: 1;
        }

        /* ── Table utilities ── */
        .badge-pill-status {
            display: inline-block;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .s-pending {
            background: #fff3cd;
            color: #856404;
        }

        .s-published {
            background: #d1e7dd;
            color: #0a5c36;
        }

        .s-rejected {
            background: #f8d7da;
            color: #842029;
        }

        /* ── Table row tightness ── */
        #dataTable td {
            vertical-align: middle;
            padding: 10px 14px !important;
        }

        #dataTable th {
            padding: 10px 14px !important;
        }
    </style>
</head>

<body>

    <!-- Page Loader -->
    <div id="page-loader">
        <div class="loader-ring"></div>
        <div class="loader-text">Loading&hellip;</div>
    </div>

    <script>
        // Registered immediately, before the render-blocking CDN <script> tags
        // further down, so this failsafe fires even if those hosts are
        // unreachable (e.g. a LAN deployment with no outbound internet).
        window.__hideLoader = (function() {
            var hidden = false;
            return function() {
                if (hidden) return;
                hidden = true;
                var el = document.getElementById('page-loader');
                if (!el) return;
                el.style.opacity = '0';
                setTimeout(function() {
                    el.style.display = 'none';
                }, 320);
            };
        })();
        setTimeout(window.__hideLoader, 4000);
    </script>

    <div class="container-scroller">

        <!-- Top Navbar -->
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            <div class="navbar-brand-wrapper d-flex justify-content-center align-items-center">
                <div class="navbar-brand-inner-wrapper d-flex justify-content-between align-items-center w-100">
                    <a class="navbar-brand brand-logo">
                        <img style="width:70%;height:auto;"
                            src="https://www.worldcitimedicalcenter.com/frontend/wcmc-logo.png" alt="WCMC" />
                    </a>
                    <button class="navbar-toggler navbar-toggler align-self-center" type="button"
                        data-toggle="minimize">
                        <span class="mdi mdi-sort-variant"></span>
                    </button>
                </div>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
                <ul class="navbar-nav navbar-nav-right">
                    <li class="nav-item nav-profile dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown" id="profileDropdown">
                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQm0FWrcJLDYw6_WHnt7Wgyqcm91PlSlb3EGXdwHlg2UUj4cua583wsckrfxi6ipl7vWsg&usqp=CAU"
                                alt="profile" />
                            <span class="nav-profile-name">{{ Auth()->user()->first_name }}
                                {{ Auth()->user()->last_name }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown"
                            aria-labelledby="profileDropdown">
                            <a href="{{ route('logout') }}" class="dropdown-item">
                                <i class="mdi mdi-logout text-primary"></i> Logout
                            </a>
                        </div>
                    </li>
                </ul>
                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                    data-toggle="offcanvas">
                    <span class="mdi mdi-menu"></span>
                </button>
            </div>
        </nav>

        <div class="container-fluid page-body-wrapper">

            <!-- Sidebar -->
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">

                    <li class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                            <i class="mdi mdi-home menu-icon"></i>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </li>

                    <li class="nav-item {{ request()->routeIs('admin.addSlide') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.addSlide') }}">
                            <i class="mdi mdi-plus-circle menu-icon"></i>
                            <span class="menu-title">Add Slide</span>
                        </a>
                    </li>

                    @if (Auth()->user()->role === 'master_admin')
                        <li
                            class="nav-item {{ request()->routeIs('admin.users') || request()->routeIs('admin.addUser') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('admin.users') }}">
                                <i class="mdi mdi-account-multiple menu-icon"></i>
                                <span class="menu-title">Users</span>
                            </a>
                        </li>
                    @endif

                    @if (in_array(Auth()->user()->role, ['master_admin', 'admin']))
                        <li class="nav-item {{ request()->routeIs('pendingSlides') ? 'active' : '' }}">
                            <a class="nav-link" href="{{ route('pendingSlides') }}">
                                <i class="mdi mdi-lan-pending menu-icon"></i>
                                <span class="menu-title">Pending Slides</span>
                                @if ($slidesPending > 0)
                                    <span class="badge badge-danger ml-2">{{ $slidesPending }}</span>
                                @endif
                            </a>
                        </li>
                    @endif

                    <li class="nav-item {{ request()->routeIs('admin.activity') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ route('admin.activity') }}">
                            <i class="mdi mdi-format-list-bulleted menu-icon"></i>
                            <span class="menu-title">Activity Logs</span>
                        </a>
                    </li>

                </ul>
            </nav>

            <!-- Main Panel -->
            <div class="main-panel">
                <div class="content-wrapper">
                    @yield('content')
                </div>
                <footer class="footer">
                    <div class="d-sm-flex justify-content-center justify-content-sm-between">
                        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
                            Copyright &copy; <a>World Citi Medical Center</a> 2024
                        </span>
                    </div>
                </footer>
            </div>

        </div>
    </div>

    <!-- Scripts — loaded once for all pages -->
    <script src="{{ asset('vendors/base/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('vendors/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('vendors/datatables.net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('js/off-canvas.js') }}"></script>
    <script src="{{ asset('js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('js/template.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <script src="{{ asset('js/data-table.js') }}"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Hide page loader once the DOM is parsed — don't wait on external
        // CDN scripts, images, or slide videos like window.load would.
        // (A failsafe timeout is also registered earlier, at the top of
        // <body>, in case these render-blocking CDN scripts never load.)
        document.addEventListener('DOMContentLoaded', window.__hideLoader);

        // Manual navbar dropdown — works regardless of Bootstrap version
        $(document).ready(function() {
            $('#profileDropdown').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var menu = $(this).next('.dropdown-menu');
                menu.toggleClass('show');
                $(this).closest('.dropdown').toggleClass('show');
            });

            // Close dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.nav-profile').length) {
                    $('.nav-profile .dropdown-menu').removeClass('show');
                    $('.nav-profile').removeClass('show');
                }
            });

            // Manual dropdown toggle for everything else (e.g. table row Action
            // menus) — this vendor bundle's Bootstrap build doesn't register its
            // own data-toggle="dropdown" click handler, so without this the
            // buttons are inert.
            $(document).on('click', '[data-toggle="dropdown"]', function(e) {
                if ($(this).closest('.nav-profile').length) return;
                e.preventDefault();
                e.stopPropagation();
                var menu = $(this).next('.dropdown-menu');
                var wasOpen = menu.hasClass('show');
                $('.dropdown-menu.show').not('.nav-profile .dropdown-menu').removeClass('show');
                if (!wasOpen) menu.addClass('show');
            });

            $(document).on('click', function(e) {
                if (!$(e.target).closest('.dropdown').length) {
                    $('.dropdown-menu.show').not('.nav-profile .dropdown-menu').removeClass('show');
                }
            });
        });
    </script>

    @yield('scripts')
</body>

</html>
