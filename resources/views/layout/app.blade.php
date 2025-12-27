<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>

    <link href="{{ asset('css/output.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @vite('resources/js/app.js')
    @stack('styles')


    <!-- ================= PREVENT SIDEBAR GLITCH ================= -->
    <script>
        (function() {
            if (localStorage.getItem('sidebarCollapsed') === '1') {
                document.documentElement.classList.add('sidebar-pre-collapsed');
            }
        })();
    </script>

    <style>
        :root {
            --bg-main: #f8fafc;
            --bg-surface: #ffffff;
            --border-soft: rgba(15, 23, 42, .08);
            --text-primary: #0f172a;
            --text-secondary: #334155;
            --text-muted: #64748b;
            --primary: #4f46e5;
            --primary-soft: rgba(79, 70, 229, .12);
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg-main: #0b1220;
                --bg-surface: #0f172a;
                --border-soft: rgba(255, 255, 255, .06);
                --text-primary: #e5e7eb;
                --text-secondary: #9ca3af;
                --text-muted: #6b7280;
                --primary: #6366f1;
                --primary-soft: rgba(99, 102, 241, .18);
            }
        }

        body {
            background: var(--bg-main);
            color: var(--text-primary);
            font-family: Inter, system-ui, sans-serif;
        }

        /* ================= SIDEBAR ================= */
        .sidebar {
            background: linear-gradient(180deg,
                    color-mix(in srgb, var(--bg-surface) 96%, transparent),
                    color-mix(in srgb, var(--bg-surface) 88%, transparent));
            border-right: 1px solid var(--border-soft);
            transition: width .28s ease;
            will-change: width;
        }

        /* ===== PRE-COLLAPSED STATE (ANTI FLASH) ===== */
        html.sidebar-pre-collapsed .sidebar {
            width: 4.5rem !important;
        }

        html.sidebar-pre-collapsed #appLayout {
            margin-left: 4.5rem;
        }

        html.sidebar-pre-collapsed header {
            left: 4.5rem;
            width: calc(100% - 4.5rem);
        }

        html.sidebar-pre-collapsed .sidebar-label,
        html.sidebar-pre-collapsed .sidebar-logo-text {
            display: none;
        }

        html.sidebar-pre-collapsed .sidebar-item {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        /* LOGO */
        .logo-wrap {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .sidebar.collapsed .sidebar-logo-text {
            display: none;
        }

        .sidebar.collapsed .logo-wrap {
            justify-content: center;
        }

        .sidebar.collapsed .logo-wrap img {
            height: 30px;
        }

        /* MENU ITEM */
        .sidebar-item {
            display: flex;
            align-items: center;
            gap: .75rem;
            padding: .6rem 1rem;
            min-height: 44px;
            border-radius: .75rem;
            color: var(--text-secondary);
            position: relative;
            transition: background .2s ease, color .2s ease;
        }

        .sidebar-item:hover {
            background: var(--primary-soft);
            color: var(--text-primary);
        }

        .sidebar-item.active {
            background: linear-gradient(90deg,
                    color-mix(in srgb, var(--primary) 28%, transparent),
                    transparent);
            color: var(--text-primary);
            font-weight: 600;
        }

        .sidebar-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 12%;
            height: 76%;
            width: 3px;
            background: var(--primary);
            border-radius: 999px;
        }

        /* COLLAPSED CLEAN */
        .sidebar.collapsed .sidebar-label {
            display: none;
        }

        .sidebar.collapsed .sidebar-item {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        /* CHEVRON */
        .chevron {
            margin-left: auto;
            transition: transform .2s ease, opacity .2s ease;
        }

        .rotate-180 {
            transform: rotate(180deg);
        }

        /* PROMO SUBMENU */
        .promo-submenu {
            overflow: hidden;
        }

        .sidebar.collapsed .promo-submenu span {
            display: none;
        }

        .sidebar.collapsed .promo-submenu a {
            justify-content: center;
            padding-left: 0;
            padding-right: 0;
        }

        /* Promo toggle when collapsed */
        .sidebar.collapsed .promo-toggle {
            justify-content: center;
        }

        .sidebar.collapsed .promo-toggle .chevron {
            opacity: .6;
            margin-left: 0;
        }

        /* LAYOUT */
        #appLayout {
            margin-left: 16rem;
            transition: margin-left .28s ease;
        }

        #appLayout.sidebar-collapsed {
            margin-left: 4.5rem;
        }

        header {
            left: 16rem;
            width: calc(100% - 16rem);
            transition: left .28s ease, width .28s ease;
            backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border-soft);
        }

        header.sidebar-collapsed {
            left: 4.5rem;
            width: calc(100% - 4.5rem);
        }
    </style>
</head>

<body class="min-h-screen flex overflow-x-hidden">


    {{-- ================= SIDEBAR ================= --}}
    <aside id="sidebar" class="sidebar fixed inset-y-0 left-0 z-50 w-64 flex flex-col rounded-tr-3xl shadow-xl">

        <div class="h-16 px-6 border-b logo-wrap" style="border-color:var(--border-soft)">
            <img src="{{ asset('uploads/logo-paradise.png') }}" class="h-10">
            <span class="sidebar-logo-text font-semibold">Paradise Gym</span>
        </div>

        <nav class="flex-1 overflow-y-auto py-4 px-3 text-sm space-y-1">

            @php
                $menu = [
                    ['url' => url('/admin/home'), 'label' => 'Home', 'icon' => 'fa-house', 'pattern' => 'admin/home'],
                    [
                        'url' => url('/admin/dashboard'),
                        'label' => 'Dashboard',
                        'icon' => 'fa-chart-line',
                        'pattern' => 'admin/dashboard*',
                    ],
                    [
                        'url' => url('/admin/manage'),
                        'label' => 'Manage Users',
                        'icon' => 'fa-user',
                        'pattern' => 'admin/manage*',
                    ],
                    [
                        'url' => url('/admin/member'),
                        'label' => 'Manage Member',
                        'icon' => 'fa-users',
                        'pattern' => 'admin/member*',
                    ],
                    [
                        'url' => url('/admin/kelas'),
                        'label' => 'Manage Kelas',
                        'icon' => 'fa-bars-progress',
                        'pattern' => 'admin/kelas*',
                    ],
                    [
                        'url' => url('/admin/schedules'),
                        'label' => 'Manage Schedule',
                        'icon' => 'fa-calendar',
                        'pattern' => 'admin/schedules*',
                    ],
                    [
                        'url' => url('/admin/visitlog'),
                        'label' => 'Visit Log',
                        'icon' => 'fa-eye',
                        'pattern' => 'admin/visitlog*',
                    ],
                ];
            @endphp

            @foreach ($menu as $item)
                <a href="{{ $item['url'] }}"
                    class="sidebar-item {{ request()->is($item['pattern']) ? 'active' : '' }}">
                    <i class="fa-solid {{ $item['icon'] }} w-5"></i>
                    <span class="sidebar-label">{{ $item['label'] }}</span>
                </a>
            @endforeach

            @php
                $promoActive = request()->is('admin/diskon*') || request()->is('admin/voucher*');
            @endphp

            <div x-data="{ open: {{ $promoActive ? 'true' : 'false' }} }" class="space-y-1">

                <button @click="open=!open" class="sidebar-item promo-toggle {{ $promoActive ? 'active' : '' }}">
                    <i class="fa-solid fa-tags w-5"></i>
                    <span class="sidebar-label">Promo Center</span>
                    <i class="fa-solid fa-chevron-down chevron" :class="{ 'rotate-180': open }"></i>
                </button>

                <div x-show="open" x-collapse class="promo-submenu space-y-1">
                    <a href="{{ url('/admin/diskon') }}"
                        class="sidebar-item {{ request()->is('admin/diskon*') ? 'active' : '' }}">
                        <i class="fa-solid fa-percent w-5"></i>
                        <span>Diskon</span>
                    </a>
                    <a href="{{ url('/admin/voucher') }}"
                        class="sidebar-item {{ request()->is('admin/voucher*') ? 'active' : '' }}">
                        <i class="fa-solid fa-ticket w-5"></i>
                        <span>Voucher</span>
                    </a>
                </div>

            </div>
        </nav>
    </aside>

    {{-- ================= MAIN ================= --}}
    <div id="appLayout" class="flex flex-col flex-1">

        <header class="fixed top-0 right-0 z-50">
            <div class="flex items-center justify-between px-6 h-16">
                <button id="collapseSidebar" class="hidden md:inline-flex">
                    <i id="collapseIcon" class="fa-solid fa-angles-left"></i>
                </button>

                <button onclick="openScanner()"
                    class="bg-indigo-600 text-white px-3 py-1 rounded text-sm hover:bg-indigo-700">
                    Scan QR
                </button>

                <div id="scanModal" class="fixed inset-0 bg-black/60 hidden z-50">
                    <div class="bg-white max-w-md mx-auto mt-20 p-4 rounded">
                        <h2 class="font-bold mb-2">Scan QR Pelanggan</h2>

                        <div id="reader" style="width:100%"></div>

                        <button onclick="closeScanner()" class="mt-3 text-sm text-red-600">
                            Tutup
                        </button>

                        <div id="scanResult" class="mt-2 text-sm"></div>
                    </div>
                </div>



                <form action="{{ route('admin.logout') }}" method="POST">@csrf
                    <button class="text-red-500 text-sm">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </button>
                </form>
            </div>
        </header>

        <main class="pt-24 p-6 flex-1">@yield('content')</main>

        <footer class="py-4 text-center text-sm text-muted">
            &copy; {{ date('Y') }} Paradise Gym
        </footer>

        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>

        <script>
            document.addEventListener("DOMContentLoaded", () => {
                const sidebar = document.getElementById('sidebar');
                const btn = document.getElementById('collapseSidebar');
                const icon = document.getElementById('collapseIcon');
                const layout = document.getElementById('appLayout');
                const header = document.querySelector('header');

                let collapsed = document.documentElement.classList.contains('sidebar-pre-collapsed');

                function apply() {
                    sidebar.classList.toggle('collapsed', collapsed);
                    layout.classList.toggle('sidebar-collapsed', collapsed);
                    header.classList.toggle('sidebar-collapsed', collapsed);
                    icon.className = collapsed ?
                        'fa-solid fa-angles-right' :
                        'fa-solid fa-angles-left';
                }

                btn.onclick = () => {
                    collapsed = !collapsed;
                    localStorage.setItem('sidebarCollapsed', collapsed ? '1' : '0');
                    document.documentElement.classList.toggle('sidebar-pre-collapsed', collapsed);
                    apply();
                };

                apply();
            });
        </script>
</body>


<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    let html5Qr;

    function openScanner() {
        document.getElementById('scanModal').classList.remove('hidden');

        html5Qr = new Html5Qrcode("reader");

        html5Qr.start({
                facingMode: "environment"
            }, {
                fps: 10,
                qrbox: 250
            },
            onScanSuccess
        );
    }

    function closeScanner() {
        document.getElementById('scanModal').classList.add('hidden');

        if (html5Qr) {
            html5Qr.stop().then(() => {
                html5Qr.clear();
            });
        }
    }

    function onScanSuccess(text) {
        document.getElementById('scanResult').innerHTML = 'Memproses...';

        fetch("{{ route('admin.absensi.scan') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    token: text
                })
            })
            .then(res => res.json())
            .then(res => {
                document.getElementById('scanResult').innerHTML = res.message;

                if (res.success) {
                    setTimeout(closeScanner, 1000);
                }
            });
    }

    html5Qr.start({
            facingMode: "environment"
        }, {
            fps: 10,
            qrbox: 250
        },
        onScanSuccess
    );
</script>


</html>
