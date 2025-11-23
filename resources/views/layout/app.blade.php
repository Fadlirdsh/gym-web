<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Dashboard')</title>

  <link href="{{ asset('css/output.css') }}" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

  <!-- 🔥 Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

  @vite('resources/js/app.js')
  @stack('styles')

  <style>
    /* Glassmorphism */
    .glass {
      background: rgba(255,255,255,0.06);
      -webkit-backdrop-filter: blur(10px) saturate(130%);
      backdrop-filter: blur(10px) saturate(130%);
      border: 1px solid rgba(255,255,255,0.06);
    }
    @media (prefers-color-scheme: dark) {
      .glass {
        background: rgba(15,23,42,0.6);
        border-color: rgba(255,255,255,0.06);
      }
    }

    .sidebar { transition: width 0.3s cubic-bezier(.2,.9,.2,1), transform .3s ease; }
    .sidebar.collapsed { width: 4.5rem; }

    .sidebar.collapsed .sidebar-label,
    .sidebar.collapsed .sidebar-logo-text {
      opacity: 0;
      pointer-events: none;
      transition: opacity .25s ease;
    }

    #appLayout { transition: all 0.3s ease; margin-left: 16rem; }
    #appLayout.sidebar-collapsed { margin-left: 4.5rem !important; width: calc(100% - 4.5rem); }

    header { transition: all 0.3s ease; left: 16rem; }
    header.sidebar-collapsed { left: 4.5rem !important; width: calc(100% - 4.5rem); }

    #loader { display: none; align-items: center; justify-content: center; height: 200px; }
    .spinner { width: 32px; height: 32px; border: 3px solid rgba(255,255,255,.2); border-top: 3px solid #6366f1; border-radius: 50%; animation: spin .8s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }
  </style>
</head>

<body class="min-h-screen flex overflow-x-hidden font-inter bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100">

  <div id="sidebarOverlay" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden"></div>

  <!-- SIDEBAR -->
  <aside id="sidebar"
    class="sidebar fixed inset-y-0 left-0 z-50 w-64 transform -translate-x-full md:translate-x-0 flex flex-col glass rounded-tr-3xl shadow-xl">

    <div class="h-16 flex items-center justify-between px-6 border-b border-white/6">
      <div class="flex items-center gap-3 w-full">
        <img src="{{ asset('uploads/logo-paradise.png') }}" alt="Logo Paradise" class="h-10 w-auto">
        <div id="logoSidebarToggle" class="font-semibold text-white text-lg tracking-wide cursor-pointer sidebar-logo-text">
          Paradise Gym
        </div>
      </div>
    </div>

    <!-- MENU -->
    <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 text-sm">
      @php
        $menu = [
          ['url' => url('/admin/home'), 'label' => 'Home', 'pattern' => 'admin/home'],
          ['url' => url('/admin/manage'), 'label' => 'Manage Users', 'pattern' => 'admin/manage'],
          ['url' => route('kelas.index'), 'label' => 'Manage Kelas', 'pattern' => 'kelas*'],
          ['url' => route('schedules.index'), 'label' => 'Manage Schedule', 'pattern' => 'schedules*'],
          ['url' => route('dashboard.index'), 'label' => 'Dashboard Data', 'pattern' => 'dashboard*'],
          ['url' => route('visitlog.index'), 'label' => 'Visit Log', 'pattern' => 'visitlog*'],
        ];
      @endphp

      @foreach ($menu as $item)
        <a href="{{ $item['url'] }}"
          class="sidebar-link nav-item-transition flex items-center gap-3 px-4 py-2 rounded-lg
            {{ request()->is($item['pattern']) ? 'bg-indigo-600/20 text-indigo-200 ring-1 ring-inset ring-indigo-600/30' : 'text-gray-300 hover:bg-indigo-500/8 hover:text-indigo-100' }}">
          
          {{-- 🔥 GANTI ICON HOME --}}
          @if ($item['label'] === 'Home')
            <i class="fa-solid fa-house w-5 h-5 text-current"></i>
          @else
            <!-- SVG Default -->
            <svg class="h-5 w-5 flex-shrink-0 text-current" viewBox="0 0 24 24" fill="none" stroke="currentColor">
              <path d="M4 6h16M4 12h16M4 18h16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
          @endif

          <span class="sidebar-label">{{ $item['label'] }}</span>
        </a>
      @endforeach

      <!-- DROPDOWN DISKON -->
      @php $isDiskonActive = request()->is('diskon*') || request()->is('voucher*'); @endphp

      <div x-data="{ open: {{ $isDiskonActive ? 'true' : 'false' }} }" class="space-y-1">
        <button @click="open = !open"
          class="flex items-center w-full px-4 py-2 rounded-lg nav-item-transition
          {{ $isDiskonActive ? 'bg-indigo-600/20 text-indigo-200 ring-1 ring-inset ring-indigo-600/30' : 'text-gray-300 hover:bg-indigo-500/8 hover:text-indigo-100' }}">
          
          <i class="fa-solid fa-tags h-5 w-5 mr-2"></i>
          <span class="sidebar-label">Manage Diskon</span>

          <svg :class="{ 'rotate-180': open }" class="ml-auto h-4 w-4 transition-transform duration-200 text-gray-300" fill="none" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>

        <div x-show="open" x-collapse class="pl-8 space-y-1">
          <a href="{{ route('diskon.index') }}"
            class="block px-3 py-2 rounded-md text-sm {{ request()->is('diskon*') ? 'bg-indigo-600/20 text-indigo-100' : 'text-gray-400 hover:text-indigo-100 hover:bg-indigo-500/8' }}">
            Diskon Kelas
          </a>
          <a href="{{ route('voucher.index') }}"
            class="block px-3 py-2 rounded-md text-sm {{ request()->is('voucher*') ? 'bg-indigo-600/20 text-indigo-100' : 'text-gray-400 hover:text-indigo-100 hover:bg-indigo-500/8' }}">
            Voucher (User)
          </a>
        </div>
      </div>
    </nav>
  </aside>

  <!-- MAIN CONTENT -->
  <div id="appLayout" class="flex flex-col flex-1">

    <header class="fixed top-0 left-64 right-0 z-50 glass backdrop-blur-lg border-b border-white/6 shadow-sm">
      <div class="flex items-center justify-between px-6 h-16">
        <div class="flex items-center gap-3">
          <button id="collapseSidebar" class="hidden md:inline-flex text-gray-200 px-2 py-1 rounded hover:bg-gray-700/30">⇤</button>
        </div>

        <!-- Account -->
        <div class="relative" x-data="{ open: false }">
          <button @click="open = !open" class="flex items-center gap-2 px-3 py-2 rounded-md hover:bg-gray-700/30">
            <img src="https://i.pravatar.cc/100" class="h-8 w-8 rounded-full border border-gray-600/40">
            <span class="hidden sm:block text-gray-200 text-sm font-medium">Admin</span>
          </button>

          <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-gray-800 border border-white/6 rounded-lg shadow-lg z-50">
            <div class="px-4 py-2 border-b border-white/6 text-sm text-gray-200">John Doe</div>
            <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-red-400 hover:bg-red-500/10">
              Logout
            </button>
          </div>
        </div>
      </div>
    </header>

    <main id="mainContent" class="pt-20 p-6 flex-1 w-full min-h-screen">
      <div id="loader"><div class="spinner"></div></div>
      <div id="pageContent">@yield('content')</div>
    </main>

    <footer class="border-t border-white/6 bg-transparent py-4 text-center text-gray-500 text-sm">
      &copy; {{ date('Y') }} Paradise Gym.
    </footer>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const collapseBtn = document.getElementById('collapseSidebar');
    const appLayout = document.getElementById('appLayout');
    const header = document.querySelector('header');
    const logoToggle = document.getElementById('logoSidebarToggle');

    let isMobile = window.innerWidth < 768;

    function updateLayout() {
      isMobile = window.innerWidth < 768;
      if (isMobile) {
        sidebar.classList.remove('collapsed');
        appLayout.classList.remove('sidebar-collapsed');
        header.classList.remove('sidebar-collapsed');
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
      } else {
        sidebar.classList.remove('-translate-x-full');
        overlay.classList.add('hidden');
        if (localStorage.getItem('sidebarCollapsed') === '1') {
          sidebar.classList.add('collapsed');
          appLayout.classList.add('sidebar-collapsed');
          header.classList.add('sidebar-collapsed');
        }
      }
    }

    function mobileOpen() { sidebar.classList.remove('-translate-x-full'); overlay.classList.remove('hidden'); }
    function mobileClose() { sidebar.classList.add('-translate-x-full'); overlay.classList.add('hidden'); }

    function toggleCollapse() {
      if (isMobile) return overlay.classList.contains('hidden') ? mobileOpen() : mobileClose();
      const isCollapsed = sidebar.classList.toggle('collapsed');
      appLayout.classList.toggle('sidebar-collapsed', isCollapsed);
      header.classList.toggle('sidebar-collapsed', isCollapsed);
      localStorage.setItem('sidebarCollapsed', isCollapsed ? '1' : '0');
    }

    logoToggle.addEventListener('click', toggleCollapse);
    collapseBtn.addEventListener('click', toggleCollapse);
    overlay.addEventListener('click', mobileClose);

    updateLayout();
    window.addEventListener('resize', updateLayout);

    // AJAX loader navigation
    const loader = document.getElementById('loader');
    const pageContent = document.getElementById('pageContent');

    document.querySelectorAll('.sidebar-link').forEach(link => {
      link.addEventListener('click', async (e) => {
        e.preventDefault();
        const url = e.currentTarget.getAttribute('href');
        if (isMobile) mobileClose();

        loader.style.display = 'flex';
        pageContent.style.opacity = '0.4';

        try {
          const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
          const html = await res.text();
          const doc = new DOMParser().parseFromString(html, 'text/html');
          const newContent = doc.querySelector('#pageContent')?.innerHTML || html;

          setTimeout(() => {
            pageContent.innerHTML = newContent;
            pageContent.style.opacity = '1';
            loader.style.display = 'none';
            history.pushState({}, '', url);
          }, 350);
        } catch {
          loader.style.display = 'none';
        }
      });
    });
  });
  </script>

  @stack('scripts')
</body>
</html>
