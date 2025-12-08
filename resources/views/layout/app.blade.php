<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>

    <!-- Tailwind -->
    <link href="{{ asset('css/output.css') }}" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @vite('resources/js/app.js')
    @stack('styles')

<style>
/* ======================= */
/* GLOBAL BACKGROUND       */
/* ======================= */
body {
  background: #f5f7fd;
}
@media (prefers-color-scheme: dark) {
  body { background: #0f172a; }
}

/* ======================= */
/* SIDEBAR BASE            */
/* ======================= */
.sidebar {
  transition: width .3s ease, transform .35s ease;
}
.sidebar-light {
  background: #ffffff;
  border-right: 1px solid rgba(0,0,0,0.08);
}
@media (prefers-color-scheme: dark) {
  .sidebar-light {
    background: rgba(17,24,39,0.85);
    border-right: 1px solid rgba(255,255,255,0.08);
    backdrop-filter: blur(10px);
  }
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
  transition: all 0.3s ease;
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

<!-- ======================== -->
<!-- OVERLAY MOBILE           -->
<!-- ======================== -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black/40 hidden z-40 md:hidden"></div>

<!-- ======================== -->
<!-- SIDEBAR                  -->
<!-- ======================== -->
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
                ['url' => route('dashboard.index'), 'label' => 'Dashboard Data', 'icon' => 'fa-chart-line', 'pattern' => 'dashboard*'],
                ['url' => url('/admin/manage'), 'label' => 'Manage Users', 'icon' => 'fa-user', 'pattern' => 'admin/manage'],
                ['url' => route('member.index'), 'label' => 'Manage Member', 'icon' => 'fa-users', 'pattern' => 'member*'],
                ['url' => route('kelas.index'), 'label' => 'Manage Kelas', 'icon' => 'fa-bars-progress', 'pattern' => 'kelas*'],
                ['url' => route('schedules.index'), 'label' => 'Manage Schedule', 'icon' => 'fa-calendar', 'pattern' => 'schedules*'],
                ['url' => route('visitlog.index'), 'label' => 'Visit Log', 'icon' => 'fa-eye', 'pattern' => 'visitlog*'],
            ];
        @endphp

        @foreach ($menu as $item)
            <a href="{{ $item['url'] }}"
                class="flex items-center gap-3 px-4 py-2 rounded-lg sidebar-link
                {{ request()->is($item['pattern'])
                    ? 'bg-indigo-600/20 text-indigo-200 ring-1 ring-indigo-600/30 font-semibold'
                    : 'text-gray-300 hover:bg-indigo-500/10 hover:text-indigo-100' }}">

                <i class="fa-solid {{ $item['icon'] }} w-5 h-5 text-current"></i>
                <span class="sidebar-label">{{ $item['label'] }}</span>
            </a>
        @endforeach

        @php $isDiskonActive = request()->is('diskon*') || request()->is('voucher*'); @endphp

        <div x-data="{ open: {{ $isDiskonActive ? 'true' : 'false' }} }" class="space-y-1 relative">

            <button @click="open = !open"
                class="flex items-center w-full gap-3 px-4 py-2 rounded-lg
                {{ $isDiskonActive
                    ? 'bg-indigo-600/20 text-indigo-200 ring-1 ring-indigo-600/30 font-semibold'
                    : 'text-gray-300 hover:bg-indigo-500/10 hover:text-indigo-100' }}">

                <i class="fa-solid fa-tags w-5 h-5"></i>
                <span class="sidebar-label">Promo Center</span>

                <svg :class="{ 'rotate-180': open }"
                    class="promo-dropdown-arrow ml-auto h-4 w-4 transition-transform duration-200 text-gray-300"
                    fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <div x-show="open" x-collapse class="pl-10 space-y-1 dropdown-content">

                <a href="{{ route('diskon.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-md text-sm
                        {{ request()->is('diskon*')
                            ? 'bg-indigo-600/20 text-indigo-100'
                            : 'text-gray-400 hover:text-indigo-100 hover:bg-indigo-500/10' }}">
                    <i class="fa-solid fa-dollar-sign w-5"></i>
                    Diskon Kelas
                </a>

                <a href="{{ route('voucher.index') }}"
                    class="flex items-center gap-3 px-3 py-2 rounded-md text-sm
                        {{ request()->is('voucher*')
                            ? 'bg-indigo-600/20 text-indigo-100'
                            : 'text-gray-400 hover:text-indigo-100 hover:bg-indigo-500/10' }}">
                    <i class="fa-solid fa-ticket w-5"></i>
                    Voucher (User)
                </a>

            </div>
        </div>

    </nav>
</aside>

<!-- ====================== -->
<!-- MAIN WRAPPER           -->
<!-- ====================== -->
<div id="appLayout" class="flex flex-col flex-1">

    <!-- HEADER -->
    <header class="fixed top-0 left-64 right-0 z-50 glass backdrop-blur-lg border-b border-white/10 shadow-sm">
        <div class="flex items-center justify-between px-6 h-16">

            <button id="collapseSidebar"
                class="hidden md:inline-flex text-gray-200 p-2 rounded-full hover:bg-gray-700/30 transition">
                <i id="collapseIcon" class="fa-solid fa-angles-left"></i>
            </button>

            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                    class="flex items-center gap-2 px-3 py-2 rounded-md hover:bg-gray-700/30 transition">
                    <img src="https://i.pravatar.cc/100" class="h-8 w-8 rounded-full border border-gray-600/40">
                    <span class="hidden sm:block text-gray-200 text-sm font-medium">Admin</span>
                    <i :class="{'rotate-180': open}"
                        class="fa-solid fa-chevron-down text-xs text-gray-400 transition-transform"></i>
                </button>

                <div x-show="open" @click.away="open = false"
                    class="absolute right-0 mt-2 w-48 bg-gray-800 border border-white/6 rounded-lg shadow-xl z-50">
                    <div class="px-4 py-2 border-b border-white/6 text-sm text-gray-200 truncate">
                        John Doe
                    </div>

                    <a href="#" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-gray-300 hover:bg-gray-700/50">
                        <i class="fa-solid fa-gear"></i> Profile Settings
                    </a>

                    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="hidden">
                        @csrf
                    </form>

                    <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                        class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-400 hover:bg-red-500/10 border-t border-white/6">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- CONTENT -->
    <main id="mainContent" class="pt-20 p-6 flex-1 w-full min-h-screen relative">
        <div id="loader" class="absolute inset-0 hidden justify-center items-center">
            <div class="spinner"></div>
        </div>

        <div id="pageContent">@yield('content')</div>
    </main>

    <footer class="border-t border-white/6 bg-transparent py-4 text-center text-gray-500 text-sm">
        &copy; {{ date('Y') }} Paradise Gym.
    </footer>

</div>

<!-- ========================= -->
<!-- JAVASCRIPT                -->
<!-- ========================= -->
<script>
document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const collapseBtn = document.getElementById('collapseSidebar');
    const logoToggle = document.getElementById('logoSidebarToggle');
    const collapseIcon = document.getElementById('collapseIcon');
    const appLayout = document.getElementById('appLayout');
    const header = document.querySelector('header');
    const pageContent = document.getElementById('pageContent');
    const loader = document.getElementById('loader');

    let isMobile = window.innerWidth < 768;
    let savedState = localStorage.getItem('sidebarCollapsed') === '1';

    function updateCollapseIcon(isCollapsed) {
        collapseIcon.classList.remove('fa-angles-left', 'fa-angles-right');
        collapseIcon.classList.add(isCollapsed ? 'fa-angles-right' : 'fa-angles-left');
    }

    function toggleLoader(show) {
        if (show) {
            loader.classList.remove('hidden');
            loader.style.display = 'flex';
            pageContent.style.opacity = '0.4';
        } else {
            loader.classList.add('hidden');
            loader.style.display = 'none';
            pageContent.style.opacity = '1';
        }
    }

    function applyState(isCollapsed) {
        sidebar.classList.toggle('collapsed', isCollapsed);
        appLayout.classList.toggle('sidebar-collapsed', isCollapsed);
        header.classList.toggle('sidebar-collapsed', isCollapsed);
        updateCollapseIcon(isCollapsed);
    }

    function updateLayout() {
        const wasMobile = isMobile;
        isMobile = window.innerWidth < 768;

        if (isMobile) {
            applyState(false);
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

    function mobileOpen() {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
    }

    function mobileClose() {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    }

    function toggleCollapse() {
        if (isMobile) {
            return overlay.classList.contains('hidden') ? mobileOpen() : mobileClose();
        }
        const isCollapsed = !sidebar.classList.contains('collapsed');
        applyState(isCollapsed);
        localStorage.setItem('sidebarCollapsed', isCollapsed ? '1' : '0');
    }

    // EVENT LISTENERS
    collapseBtn?.addEventListener('click', toggleCollapse);
    logoToggle?.addEventListener('click', toggleCollapse);
    overlay.addEventListener('click', mobileClose);

    updateLayout();
    window.addEventListener('resize', updateLayout);

});
</script>

</body>
</html>
