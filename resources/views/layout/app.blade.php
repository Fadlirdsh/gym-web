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

    <style>
        body {
            background: #f5f7fd;
        }

        @media (prefers-color-scheme: dark) {
            body {
                background: #0f172a;
            }
        }

        .sidebar {
            transition: width .3s ease, transform .35s ease;
        }

        .sidebar.collapsed .sidebar-label,
        .sidebar.collapsed .sidebar-logo-text {
            opacity: 0;
            pointer-events: none;
        }

        .sidebar.collapsed .promo-dropdown-arrow {
            opacity: 0;
        }

        #appLayout {
            margin-left: 16rem;
            transition: all .3s ease;
        }

        header {
            transition: all .3s ease;
            left: 16rem;
            width: calc(100% - 16rem);
        }

        header.sidebar-collapsed {
            left: 4.5rem !important;
            width: calc(100% - 4.5rem) !important;
        }

        #loader {
            position: absolute;
            z-index: 10;
        }
    </style>
</head>

<body class="min-h-screen flex font-inter overflow-x-hidden">

    <!-- OVERLAY MOBILE -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/40 hidden z-40 md:hidden"></div>

    <!-- SIDEBAR -->
    <aside id="sidebar"
        class="sidebar fixed inset-y-0 left-0 z-50 w-64 transform -translate-x-full md:translate-x-0 flex flex-col
    bg-gray-900/80 backdrop-blur-lg rounded-tr-3xl shadow-xl">

        <div class="h-16 flex items-center px-6 border-b border-white/10">
            <div class="flex items-center gap-3 w-full">
                <img src="{{ asset('uploads/logo-paradise.png') }}" class="h-10">
                <div id="logoSidebarToggle"
                    class="font-semibold text-white text-lg cursor-pointer sidebar-logo-text hover:text-indigo-400 transition">
                    Paradise Gym
                </div>
            </div>
        </div>

        <nav class="flex-1 overflow-y-auto py-4 px-3 text-sm space-y-1">

            @php
                $menu = [
                    ['url' => url('/admin/home'), 'label' => 'Home', 'icon' => 'fa-house', 'pattern' => 'admin/home'],
                    [
                        'url' => route('dashboard.index'),
                        'label' => 'Dashboard Data',
                        'icon' => 'fa-chart-line',
                        'pattern' => 'dashboard*',
                    ],
                    [
                        'url' => url('/admin/manage'),
                        'label' => 'Manage Users',
                        'icon' => 'fa-user',
                        'pattern' => 'admin/manage',
                    ],
                    [
                        'url' => route('member.index'),
                        'label' => 'Manage Member',
                        'icon' => 'fa-users',
                        'pattern' => 'member*',
                    ],
                    [
                        'url' => route('kelas.index'),
                        'label' => 'Manage Kelas',
                        'icon' => 'fa-bars-progress',
                        'pattern' => 'kelas*',
                    ],
                    [
                        'url' => route('schedules.index'),
                        'label' => 'Manage Schedule',
                        'icon' => 'fa-calendar',
                        'pattern' => 'schedules*',
                    ],
                    [
                        'url' => route('visitlog.index'),
                        'label' => 'Visit Log',
                        'icon' => 'fa-eye',
                        'pattern' => 'visitlog*',
                    ],
                ];
            @endphp

            @foreach ($menu as $item)
                <a href="{{ $item['url'] }}"
                    class="flex items-center gap-3 px-4 py-2 rounded-lg sidebar-link
                {{ request()->is($item['pattern'])
                    ? 'bg-indigo-600/20 text-indigo-200 ring-1 ring-indigo-600/30 font-semibold'
                    : 'text-gray-300 hover:bg-indigo-500/10 hover:text-indigo-100' }}">

                    <i class="fa-solid {{ $item['icon'] }} w-5 h-5"></i>
                    <span class="sidebar-label">{{ $item['label'] }}</span>
                </a>
            @endforeach

            <!-- PROMO CENTER -->
            @php $isPromoActive = request()->is('diskon*') || request()->is('voucher*'); @endphp

            <div x-data="{ open: {{ $isPromoActive ? 'true' : 'false' }} }" class="space-y-1">

                <button @click="open = !open"
                    class="flex items-center w-full gap-3 px-4 py-2 rounded-lg transition-all
                {{ $isPromoActive
                    ? 'bg-indigo-600/20 text-indigo-200 ring-1 ring-indigo-600/30 font-semibold'
                    : 'text-gray-300 hover:text-indigo-200 hover:bg-indigo-500/10' }}">

                    <i class="fa-solid fa-tags w-5 h-5"></i>
                    <span class="sidebar-label">Promo Center</span>

                    <svg :class="{ 'rotate-180': open }"
                        class="promo-dropdown-arrow ml-auto h-4 w-4 transform transition-transform duration-300 text-gray-300"
                        fill="none" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="open" x-collapse class="pl-10 mt-1 space-y-1 overflow-hidden">

                    <a href="{{ route('diskon.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-md text-sm transition
                    {{ request()->is('diskon*')
                        ? 'bg-indigo-600/20 text-indigo-100 ring-1 ring-indigo-600/20'
                        : 'text-gray-400 hover:text-indigo-100 hover:bg-indigo-500/10' }}">
                        <i class="fa-solid fa-percent w-5"></i> Diskon
                    </a>

                    <a href="{{ route('voucher.index') }}"
                        class="flex items-center gap-3 px-3 py-2 rounded-md text-sm transition
                    {{ request()->is('voucher*')
                        ? 'bg-indigo-600/20 text-indigo-100 ring-1 ring-indigo-600/20'
                        : 'text-gray-400 hover:text-indigo-100 hover:bg-indigo-500/10' }}">
                        <i class="fa-solid fa-ticket w-5"></i> Voucher
                    </a>

                </div>
            </div>

        </nav>
    </aside>

    <!-- ================= MAIN WRAPPER ================= -->
    <div id="appLayout" class="flex flex-col flex-1">

        <!-- HEADER -->
        <header class="fixed top-0 left-64 right-0 z-50 glass backdrop-blur-lg border-b border-white/10 shadow-sm">
            <div class="flex items-center justify-between px-6 h-16">

                <!-- HAMBURGER BUTTON MOBILE -->
                <button id="mobileToggle"
                    class="md:hidden text-gray-200 p-2 rounded-md hover:bg-gray-700/30 transition">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>

                <!-- COLLAPSE BUTTON DESKTOP -->
                <button id="collapseSidebar"
                    class="hidden md:inline-flex text-gray-200 p-2 rounded-full hover:bg-gray-700/30 transition">
                    <i id="collapseIcon" class="fa-solid fa-angles-left"></i>
                </button>

                <!-- PROFILE DROPDOWN -->
                <div class="relative" x-data="{ open: false }">

                    <button @click="open = !open"
                        class="flex items-center gap-2 px-3 py-2 rounded-md hover:bg-gray-700/30 transition">

                        <img src="{{ asset('images/avatar_admin.png') }}"
                            class="h-8 w-8 rounded-full border border-gray-600/40" alt="Avatar Admin">

                        <span class="hidden sm:block text-gray-200 text-sm font-medium">
                            {{ $adminName ?? 'Admin' }}
                        </span>

                        <i :class="{ 'rotate-180': open }"
                            class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform"></i>
                    </button>

                    <!-- DROPDOWN MENU -->
                    <div x-show="open" @click.away="open = false" x-transition
                        class="absolute right-0 mt-2 w-48 bg-gray-800 border border-white/10 rounded-lg shadow-lg z-50">

                        <div class="px-4 py-2 text-gray-300 border-b border-white/10 text-sm">
                            Logged in as <br>
                            <strong>Admin</strong>
                        </div>

                        {{-- <a href="#"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-gray-300 hover:bg-gray-700/50 transition">
                        <i class="fa-solid fa-user-gear"></i> Profile Settings
                    </a> --}}

                        <!-- LOGOUT FORM -->
                        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>

                        <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-400 hover:bg-red-600/20 border-t border-white/10 transition">
                            <i class="fa-solid fa-right-from-bracket"></i> Logout
                        </button>

                    </div>
                </div>

            </div>
        </header>

        <!-- MAIN CONTENT -->
        <main id="mainContent" class="pt-20 p-6 flex-1 w-full min-h-screen relative">
            <div id="loader" class="absolute inset-0 hidden justify-center items-center">
                <div class="spinner"></div>
            </div>

            <div id="pageContent">@yield('content')</div>
        </main>

        <!-- FOOTER -->
        <footer class="border-t border-white/6 bg-transparent py-4 text-center text-gray-500 text-sm">
            &copy; {{ date('Y') }} Paradise Gym.
        </footer>

    </div>

    <!-- ================= JS ================= -->

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", () => {

            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const collapseBtn = document.getElementById('collapseSidebar');
            const collapseIcon = document.getElementById('collapseIcon');
            const mobileToggle = document.getElementById('mobileToggle');
            const appLayout = document.getElementById('appLayout');
            const header = document.querySelector('header');

            let isMobile = window.innerWidth < 768;
            let savedState = localStorage.getItem('sidebarCollapsed') === '1';

            /** ICON UPDATE **/
            function updateCollapseIcon(isCollapsed) {
                collapseIcon.classList.remove('fa-angles-left', 'fa-angles-right');
                collapseIcon.classList.add(isCollapsed ? 'fa-angles-right' : 'fa-angles-left');
            }

            /** COLLAPSE STATE DESKTOP **/
            function applyState(isCollapsed) {
                sidebar.classList.toggle('collapsed', isCollapsed);
                appLayout.classList.toggle('sidebar-collapsed', isCollapsed);
                header.classList.toggle('sidebar-collapsed', isCollapsed);
                updateCollapseIcon(isCollapsed);
            }

            /** RESPONSIVE HANDLER **/
            function updateLayout() {
                const wasMobile = isMobile;
                isMobile = window.innerWidth < 768;

                if (isMobile) {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                } else {
                    sidebar.classList.remove('-translate-x-full');
                    overlay.classList.add('hidden');
                    applyState(savedState);
                }

                if (wasMobile && !isMobile) {
                    applyState(savedState);
                }
            }

            /** DESKTOP COLLAPSE **/
            collapseBtn?.addEventListener('click', () => {
                if (isMobile) return;

                const isCollapsed = !sidebar.classList.contains('collapsed');
                applyState(isCollapsed);

                localStorage.setItem('sidebarCollapsed', isCollapsed ? '1' : '0');
            });

            /** MOBILE OPEN **/
            mobileToggle.addEventListener('click', () => {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            });

            /** MOBILE CLOSE **/
            overlay.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            });

            /** INIT **/
            updateLayout();
            window.addEventListener('resize', updateLayout);
        });
    </script>

</body>

</html>
