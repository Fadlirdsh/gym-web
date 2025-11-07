<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Dashboard')</title>

  <link href="{{ asset('css/output.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
  @vite('resources/js/app.js')
  @stack('styles')

  <style>
    body {
      font-family: "Inter", sans-serif;
    }

    .top-navbar-custom {
      height: 56px;
      background: #1f2937;
      border-bottom: 1px solid #2f3a4a;
      padding: 0 24px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      z-index: 50;
    }

    .top-navbar-custom input {
      background: #111827;
      border: 1px solid #374151;
      padding: 7px 14px;
      border-radius: 6px;
      color: #e5e7eb;
      width: 220px;
      font-size: 14px;
    }

    .sidebar-link {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 14px;
      border-radius: 6px;
      font-size: 14px;
      transition: 0.15s;
    }

    .sidebar-link:hover {
      background: #334155;
      color: white;
    }

    /* === COLLAPSE STYLING === */
    :root {
      --sidebar-w-full: 16rem;
      --sidebar-w-collapsed: 5rem;
    }

    @media (min-width: 768px) {
      .sidebar.collapsed {
        width: var(--sidebar-w-collapsed) !important;
        min-width: var(--sidebar-w-collapsed) !important;
      }

      #appLayout.sidebar-collapsed {
        padding-left: var(--sidebar-w-collapsed) !important;
      }

      header.sidebar-collapsed {
        left: var(--sidebar-w-collapsed) !important;
      }
    }

    .sidebar.collapsed .sidebar-label {
      display: none;
    }

    .sidebar.collapsed .sidebar-logo-text {
      display: none;
    }

    .sidebar.collapsed .sidebar-logo img {
      max-width: 36px;
      height: auto;
    }

    .sidebar.collapsed .sidebar-link {
      justify-content: center;
    }

    #sidebar,
    header,
    #appLayout {
      transition: all 0.25s ease;
    }
  </style>
</head>

<body class="bg-gray-900 min-h-screen flex text-gray-100 overflow-x-hidden">

  {{-- Overlay mobile --}}
  <div id="sidebarOverlay" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden"></div>

  {{-- SIDEBAR --}}
  <aside id="sidebar"
    class="sidebar fixed inset-y-0 left-0 z-50 w-64 bg-gray-800 border-r border-gray-700 transform -translate-x-full md:translate-x-0 transition-all duration-300 flex flex-col">

    {{-- Logo --}}
    <div class="h-16 flex items-center justify-between px-4 border-b border-gray-700">
      <div class="flex items-center justify-start gap-3 w-full sidebar-logo">
        <img src="{{ asset('uploads/logo-paradise.png') }}" alt="Logo Paradise"
          class="h-10 w-auto max-w-[150px] object-contain">
        <span class="font-semibold text-gray-100 text-lg tracking-wide whitespace-nowrap sidebar-logo-text">
          Paradise Gym
        </span>
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
              ['url' => route('diskon.index'), 'label' => 'Manage Diskon', 'pattern' => 'diskon*'],
              ['url' => route('dashboard.index'), 'label' => 'Data', 'pattern' => 'dashboard*'],
              ['url' => route('voucher.index'), 'label' => 'Manage Voucher', 'pattern' => 'voucher*'],
              ['url' => route('visitlog.index'), 'label' => 'Visit Log', 'pattern' => 'visitlog*'],
          ];
      @endphp

      @foreach ($menu as $item)
        <a href="{{ $item['url'] }}"
          class="sidebar-link {{ request()->is($item['pattern']) ? 'bg-blue-600 text-white' : 'text-gray-300' }}"
          title="{{ $item['label'] }}">
          {{-- Icon contoh --}}
          <svg class="h-5 w-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor"
            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M4 6h16M4 12h16M4 18h16" />
          </svg>
          <span class="sidebar-label">{{ $item['label'] }}</span>
        </a>
      @endforeach
    </nav>

    {{-- Logout --}}
    <div class="p-4 border-t border-gray-700">
      <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="hidden">@csrf</form>
      <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
        class="w-full px-4 py-2 text-red-400 border border-red-500/40 rounded-md hover:bg-red-500/20 transition">
        Logout
      </button>
    </div>
  </aside>

  {{-- MAIN CONTENT AREA --}}
  <div id="appLayout" class="flex-1 flex flex-col w-full md:pl-64 transition-all duration-300">
    {{-- NAVBAR --}}
    <header class="fixed top-0 left-0 right-0 md:left-64 z-[999] bg-gray-800 transition-all duration-300">
      <div class="top-navbar-custom">
        {{-- Toggle sidebar mobile --}}
        <button id="openSidebar" class="md:hidden text-gray-300 text-2xl">☰</button>

        {{-- Collapse button desktop --}}
        <button id="collapseSidebar"
          class="hidden md:inline-flex text-gray-300 ml-3 px-2 py-1 rounded hover:bg-gray-700">⇤</button>

        {{-- Search --}}
        <input type="text" placeholder="Search..." class="hidden md:block">

        {{-- Profile --}}
        <div class="flex items-center gap-3">
          <img src="https://i.pravatar.cc/100" class="h-8 w-8 rounded-full border border-gray-600">
          <span class="text-gray-200 text-sm font-medium">Admin</span>
        </div>
      </div>
    </header>

    {{-- PAGE CONTENT --}}
    <main class="pt-[64px] p-6 flex-1 w-full min-h-screen overflow-auto">
      @yield('content')
    </main>
  </div>

  {{-- SCRIPT --}}
  <script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const openBtn = document.getElementById('openSidebar');
    const closeBtn = document.getElementById('closeSidebar');
    const collapseBtn = document.getElementById('collapseSidebar');
    const appLayout = document.getElementById('appLayout');
    const header = document.querySelector('header');

    // buka sidebar mobile
    openBtn?.addEventListener('click', () => {
      if (window.innerWidth < 768) {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.remove('hidden');
        return;
      }
      toggleCollapse();
    });

    const closeSidebarMobile = () => {
      sidebar.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
    };

    closeBtn?.addEventListener('click', closeSidebarMobile);
    overlay?.addEventListener('click', closeSidebarMobile);

    // collapse toggle desktop
    collapseBtn?.addEventListener('click', toggleCollapse);

    function toggleCollapse() {
      const isCollapsed = sidebar.classList.toggle('collapsed');
      if (isCollapsed) {
        appLayout.classList.add('sidebar-collapsed');
        header.classList.add('sidebar-collapsed');
      } else {
        appLayout.classList.remove('sidebar-collapsed');
        header.classList.remove('sidebar-collapsed');
      }
      localStorage.setItem('sidebarCollapsed', isCollapsed ? '1' : '0');
    }

    // restore dari localStorage
    (function restore() {
      const val = localStorage.getItem('sidebarCollapsed');
      if (val === '1') {
        sidebar.classList.add('collapsed');
        appLayout.classList.add('sidebar-collapsed');
        header.classList.add('sidebar-collapsed');
      }
    })();

    // responsive behavior
    window.addEventListener('resize', () => {
      if (window.innerWidth < 768) {
        sidebar.classList.remove('collapsed');
        appLayout.classList.remove('sidebar-collapsed');
        header.classList.remove('sidebar-collapsed');
      } else {
        const val = localStorage.getItem('sidebarCollapsed');
        if (val === '1') {
          sidebar.classList.add('collapsed');
          appLayout.classList.add('sidebar-collapsed');
          header.classList.add('sidebar-collapsed');
        }
      }
    });
  </script>

  @stack('scripts')
</body>

</html>
