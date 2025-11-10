<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>

    <link href="{{ asset('css/output.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    @vite('resources/js/app.js')
    @stack('styles')
</head>

<body class="bg-gray-900 min-h-screen flex overflow-x-hidden font-inter text-gray-100">

    {{-- Overlay mobile --}}
    <div id="sidebarOverlay" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden"></div>

    {{-- SIDEBAR --}}
    <aside id="sidebar"
        class="sidebar fixed inset-y-0 left-0 z-50 w-64 transform -translate-x-full md:translate-x-0 transition-all duration-300 flex flex-col bg-gradient-to-b from-blue-800 via-blue-900 to-indigo-900 shadow-lg rounded-tr-3xl">

        {{-- Logo --}}
        <div class="h-16 flex items-center justify-between px-6 border-b border-blue-700">
            <div class="flex items-center gap-3 w-full">
                <img src="{{ asset('uploads/logo-paradise.png') }}" alt="Logo Paradise"
                    class="h-10 w-auto max-w-[150px] object-contain">
                <div id="logoSidebarToggle"
                    class="font-semibold text-white text-lg tracking-wide cursor-pointer sidebar-logo-text">
                    Paradise Gym
                </div>
            </div>
        </div>

        {{-- Menu --}}
        <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 text-sm">
            @php
                $menu = [
                    ['url' => url('/admin/home'), 'label' => 'Dashboard', 'pattern' => 'admin/home'],
                    ['url' => url('/admin/manage'), 'label' => 'Manage Users', 'pattern' => 'admin/manage'],
                    ['url' => route('kelas.index'), 'label' => 'Manage Kelas', 'pattern' => 'kelas*'],
                    ['url' => route('schedules.index'), 'label' => 'Manage Schedule', 'pattern' => 'schedules*'],
                    ['url' => route('dashboard.index'), 'label' => 'Data', 'pattern' => 'dashboard*'],
                    ['url' => route('visitlog.index'), 'label' => 'Visit Log', 'pattern' => 'visitlog*'],
                ];
            @endphp

            @foreach ($menu as $item)
                <a href="{{ $item['url'] }}"
                    class="sidebar-link flex items-center gap-3 px-4 py-2 rounded-lg transition-colors duration-200
                    {{ request()->is($item['pattern']) ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-blue-700 hover:text-white' }}">
                    <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <span class="sidebar-label">{{ $item['label'] }}</span>
                </a>
            @endforeach

            {{-- Dropdown Manage Diskon & Voucher --}}
            @php
                $isDiskonActive = request()->is('diskon*') || request()->is('voucher*');
            @endphp
            <div x-data="{ open: {{ $isDiskonActive ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open"
                    class="flex items-center w-full px-4 py-2 rounded-lg transition-colors duration-200
                    {{ $isDiskonActive ? 'bg-blue-600 text-white' : 'text-gray-300 hover:bg-blue-700 hover:text-white' }}">
                    <svg class="h-5 w-5 flex-shrink-0 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <span>Manage Diskon</span>
                    <svg :class="{ 'rotate-180': open }" class="ml-auto h-4 w-4 transition-transform duration-200"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                <div x-show="open" x-collapse class="pl-8 space-y-1">
                    <a href="{{ route('diskon.index') }}"
                        class="block px-3 py-2 rounded-md text-sm
                        {{ request()->is('diskon*') ? 'bg-blue-700 text-white' : 'text-gray-400 hover:text-white hover:bg-blue-800' }}">
                        Diskon Kelas
                    </a>
                    <a href="{{ route('voucher.index') }}"
                        class="block px-3 py-2 rounded-md text-sm
                        {{ request()->is('voucher*') ? 'bg-blue-700 text-white' : 'text-gray-400 hover:text-white hover:bg-blue-800' }}">
                        Voucher (User)
                    </a>
                </div>
            </div>
        </nav>

        {{-- Logout --}}
        <div class="p-4 border-t border-blue-700">
            <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="hidden">@csrf</form>
            <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                class="w-full px-4 py-2 text-red-400 border border-red-500/40 rounded-lg hover:bg-red-500/20 transition">
                Logout
            </button>
        </div>
    </aside>

    {{-- MAIN CONTENT --}}
    <div id="appLayout" class="flex-1 flex flex-col w-full md:pl-64 transition-all duration-300 bg-gray-900">

        {{-- NAVBAR --}}
        <header
            class="fixed top-0 left-0 right-0 md:left-64 z-50 backdrop-blur-md bg-gray-800/90 shadow-sm border-b border-gray-700 transition-all duration-300">
            <div class="top-navbar-custom flex items-center justify-between px-6 h-16">
                <div class="flex items-center gap-3">
                    <button id="collapseSidebar"
                        class="hidden md:inline-flex text-gray-200 px-2 py-1 rounded hover:bg-gray-700 transition">⇤</button>
                    <input type="text" placeholder="Search..."
                        class="hidden md:block px-3 py-1 rounded border border-gray-600 bg-gray-700 text-gray-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div class="flex items-center gap-3">
                    <img src="https://i.pravatar.cc/100" class="h-8 w-8 rounded-full border border-gray-600">
                    <span class="text-gray-200 text-sm font-medium">Admin</span>
                </div>
            </div>
        </header>

        {{-- PAGE CONTENT --}}
        <main class="pt-16 p-6 flex-1 w-full min-h-screen overflow-auto">
            @yield('content')
        </main>
    </div>

    {{-- SCRIPT --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const collapseBtn = document.getElementById('collapseSidebar');
            const appLayout = document.getElementById('appLayout');
            const header = document.querySelector('header');
            const logoToggle = document.getElementById('logoSidebarToggle');

            // Mobile open/close
            function mobileOpen() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            }

            function mobileClose() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }

            // Desktop collapse toggle
            function toggleCollapse() {
                const isCollapsed = sidebar.classList.toggle('collapsed');
                appLayout.classList.toggle('sidebar-collapsed', isCollapsed);
                header.classList.toggle('sidebar-collapsed', isCollapsed);
                localStorage.setItem('sidebarCollapsed', isCollapsed ? '1' : '0');
            }

            // Click Paradise Gym
            logoToggle.addEventListener('click', () => {
                if (window.innerWidth < 768) {
                    if (overlay.classList.contains('hidden')) {
                        mobileOpen();
                    } else {
                        mobileClose();
                    }
                } else {
                    toggleCollapse();
                }
            });

            // Click collapse button
            collapseBtn.addEventListener('click', toggleCollapse);

            // Click overlay (mobile)
            overlay.addEventListener('click', mobileClose);

            // Restore collapse state desktop
            if (window.innerWidth >= 768) {
                if (localStorage.getItem('sidebarCollapsed') === '1') {
                    sidebar.classList.add('collapsed');
                    appLayout.classList.add('sidebar-collapsed');
                    header.classList.add('sidebar-collapsed');
                }
            }

            // Resize handler
            window.addEventListener('resize', () => {
                if (window.innerWidth < 768) {
                    sidebar.classList.remove('collapsed');
                    appLayout.classList.remove('sidebar-collapsed');
                    header.classList.remove('sidebar-collapsed');
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                } else {
                    if (localStorage.getItem('sidebarCollapsed') === '1') {
                        sidebar.classList.add('collapsed');
                        appLayout.classList.add('sidebar-collapsed');
                        header.classList.add('sidebar-collapsed');
                    }
                    sidebar.classList.remove('-translate-x-full');
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
