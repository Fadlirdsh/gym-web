{{-- resources/views/layouts/tailwind.blade.php --}}
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Home')</title>

    {{-- CSS Tailwind --}}
    <link href="{{ asset('css/output.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    @vite('resources/js/app.js')
    @stack('styles')
</head>

<body class="bg-gray-900 min-h-screen flex flex-col">
    {{-- Navbar --}}
    <div class="min-h-full">
        <nav class="bg-gray-800/50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">

                    {{-- Kiri: Logo + Menu --}}
                    <div class="flex items-center">
                        {{-- Logo --}}
                        <div class="shrink-0">
                            <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500"
                                alt="MyApp" class="size-8" />
                        </div>

                        {{-- Menu Navigasi --}}
                        <div class="hidden md:block">
                            <div class="ml-10 flex items-baseline space-x-4">
                                <a href="{{ url('/admin/home') }}"
                                    class="rounded-md bg-gray-950/50 px-3 py-2 text-sm font-medium text-white">
                                    Home
                                </a>
                                <a href="{{ url('/admin/manage') }}"
                                    class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white">
                                    Manage Users
                                </a>
                                <a href="{{ route('kelas.index') }}"
                                    class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white">
                                    Manage Kelas
                                </a>
                                <a href="{{ route('schedules.index') }}"
                                    class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white">
                                    Manage Schedule
                                </a>
                                <a href="{{ route('diskon.index') }}"
                                    class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white">
                                    Manage Diskon
                                </a>
                                <a href="{{ route('dashboard.index') }}"
                                    class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white">
                                    Data
                                </a>
                                <a href="{{ route('visitlog.index') }}"
                                    class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white">
                                    Visit Log
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Kanan: Avatar + Tombol Logout --}}
                    <div class="flex items-center space-x-4">
                        {{-- Avatar --}}
                        <img class="h-8 w-8 rounded-full outline outline-1 outline-white/10"
                            src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                            alt="User">

                        {{-- Form Logout --}}
                        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>

                        {{-- Tombol Logout --}}
                        <button onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                            class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 text-sm font-medium">
                            Logout
                        </button>
                    </div>

                </div>
            </div>
        </nav>
    </div>

    {{-- Konten Utama --}}
    <main class="flex-1 container mx-auto px-4 py-12">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-800 border-t border-gray-700">
        <div class="container mx-auto px-4 py-4 text-center text-sm text-gray-400">
            &copy; {{ date('Y') }} MyApp. Semua hak dilindungi.
        </div>
    </footer>

    @stack('scripts')
</body>

</html>
