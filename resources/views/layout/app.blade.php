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

/* Sidebar collapse */
.sidebar-collapsed {
  width: 4.5rem !important;
}
.sidebar-collapsed .sidebar-label {
  opacity: 0;
  pointer-events: none;
  transform: translateX(-10px);
}

/* Hide on mobile */
.sidebar-hidden {
  transform: translateX(-100%) !important;
}

/* ======================= */
/* MAIN CONTENT SHIFT      */
/* ======================= */
#appLayout {
  margin-left: 16rem;
  transition: all .3s ease;
}

header {
  left: 16rem;
  transition: all .3s ease;
}

#appLayout.sidebar-collapsed {
  margin-left: 4.5rem !important;
}
header.sidebar-collapsed {
  left: 4.5rem !important;
}

/* ======================= */
/* ACTIVE MENU             */
/* ======================= */
.menu-active {
  background: #e0e7ff !important;
  color: #4338ca !important;
}
@media (prefers-color-scheme: dark) {
  .menu-active {
    background: rgba(99,102,241,0.25) !important;
    color: #c7d2fe !important;
  }
}

/* NORMAL MENU */
.menu-normal { 
  color: #475569 !important; 
}
@media (prefers-color-scheme: dark) {
  .menu-normal { color: #cbd5e1 !important; }
}

/* ======================= */
/* RESPONSIVE MEDIA QUERY  */
/* ======================= */

/* MOBILE ≤ 768px */
@media (max-width: 768px) {
  #appLayout,
  header {
    margin-left: 0 !important;
    left: 0 !important;
  }

  #sidebar {
    transform: translateX(-100%);
    width: 16rem !important;
  }
}

/* TABLET 769–1024px */
@media (min-width: 769px) and (max-width: 1024px) {
  #sidebar {
    width: 4.5rem !important;
  }
  .sidebar-label { opacity: 0; }
  #appLayout { margin-left: 4.5rem !important; }
  header { left: 4.5rem !important; }
}

</style>
</head>

<body class="min-h-screen flex font-inter overflow-x-hidden">

<!-- OVERLAY MOBILE -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black/40 hidden z-40 md:hidden"></div>

<!-- SIDEBAR -->
<aside id="sidebar"
  class="sidebar sidebar-light fixed inset-y-0 left-0 z-50 w-64 
         transform md:translate-x-0 -translate-x-full flex flex-col rounded-tr-3xl">

  <div class="h-16 flex items-center px-6 border-b">
    <img src="{{ asset('uploads/logo-paradise.png') }}" class="h-10">
    <div id="logoSidebarToggle" class="ml-3 font-semibold text-gray-800 dark:text-white cursor-pointer sidebar-label">
      Paradise Gym
    </div>
  </div>

  <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1 text-sm">

    @php
      $menu = [
        ['url'=>route('dashboard.index'),'label'=>'Dashboard Data','pattern'=>'dashboard*','icon'=>'fa-chart-line'],
        ['url'=>url('/admin/home'),'label'=>'Home','pattern'=>'admin/home','icon'=>'fa-house'],
        ['url'=>url('/admin/manage'),'label'=>'Manage Users','pattern'=>'admin/manage','icon'=>'fa-user'],
        ['url'=>route('kelas.index'),'label'=>'Manage Kelas','pattern'=>'kelas*','icon'=>'fa-bars-progress'],
        ['url'=>route('schedules.index'),'label'=>'Manage Schedule','pattern'=>'schedules*','icon'=>'fa-calendar'],
        ['url'=>route('visitlog.index'),'label'=>'Visit Log','pattern'=>'visitlog*','icon'=>'fa-eye'],
      ];
    @endphp

    @foreach ($menu as $item)
      <a href="{{ $item['url'] }}"
        class="flex items-center gap-3 px-4 py-2 rounded-lg
        {{ request()->is($item['pattern']) ? 'menu-active' : 'menu-normal' }}">
        <i class="fa-solid {{ $item['icon'] }}"></i>
        <span class="sidebar-label">{{ $item['label'] }}</span>
      </a>
    @endforeach

  </nav>
</aside>

<!-- MAIN WRAPPER -->
<div id="appLayout" class="flex flex-col flex-1">

  <!-- HEADER -->
  <header class="fixed top-0 right-0 z-50 h-16 bg-white dark:bg-gray-900 shadow-md flex items-center px-6">
    <button id="toggleSidebar" class="hidden md:inline-flex text-xl dark:text-white">
      <i class="fa-solid fa-bars"></i>
    </button>

    <div class="ml-auto relative" x-data="{open:false}">
      <button @click="open=!open" class="flex items-center gap-3 px-3 py-2">
        <img src="https://i.pravatar.cc/100" class="h-8 w-8 rounded-full shadow">
        <span class="hidden sm:inline text-sm dark:text-gray-200">Admin</span>
      </button>

      <div x-show="open" @click.away="open=false"
        class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 shadow-xl rounded-lg">
        <div class="px-4 py-2 border-b dark:border-gray-700">
          John Doe
        </div>
        <button onclick="document.getElementById('logout-form').submit();"
          class="flex items-center gap-2 w-full px-4 py-2 text-red-600 hover:bg-red-100 dark:text-red-400">
          <i class="fa-solid fa-right-from-bracket"></i> Logout
        </button>
      </div>
    </div>
  </header>

  <!-- PAGE CONTENT -->
  <main id="mainContent" class="pt-20 p-6 flex-1">
    @yield('content')
  </main>

  <footer class="py-4 text-center text-gray-500">
    © {{ date('Y') }} Paradise Gym.
  </footer>

</div>

<script>
document.addEventListener("DOMContentLoaded", () => {

  const sidebar = document.getElementById("sidebar");
  const overlay = document.getElementById("sidebarOverlay");
  const toggleSidebar = document.getElementById("toggleSidebar");
  const logoToggle = document.getElementById("logoSidebarToggle");
  const appLayout = document.getElementById("appLayout");
  const header = document.querySelector("header");

  /* DESKTOP TOGGLE */
  toggleSidebar.addEventListener("click", () => {
    if (window.innerWidth <= 768) return;

    const isCollapsed = sidebar.classList.toggle("sidebar-collapsed");
    appLayout.classList.toggle("sidebar-collapsed", isCollapsed);
    header.classList.toggle("sidebar-collapsed", isCollapsed);
  });

  /* MOBILE SHOW */
  logoToggle.addEventListener("click", () => {
    if (window.innerWidth > 768) return;
    sidebar.classList.remove("sidebar-hidden");
    sidebar.style.transform = "translateX(0)";
    overlay.classList.remove("hidden");
  });

  /* MOBILE HIDE */
  overlay.addEventListener("click", () => {
    sidebar.classList.add("sidebar-hidden");
    sidebar.style.transform = "translateX(-100%)";
    overlay.classList.add("hidden");
  });

});
</script>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@stack('scripts')

</body>
</html>
