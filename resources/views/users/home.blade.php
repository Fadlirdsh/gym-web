<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="{{ asset('css/output.css') }}" rel="stylesheet">
    @vite('resources/js/app.js')
</head>

<body class="bg-gray-900 min-h-screen flex flex-col">

    {{-- Navbar (Tailwind Plus) --}}
    <div class="min-h-full">
        <nav class="bg-gray-800/50">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <div class="flex items-center">
                        <div class="shrink-0">
                            <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500" alt="MyApp" class="size-8" />
                        </div>
                        <div class="hidden md:block">
                            <div class="ml-10 flex items-baseline space-x-4">
                                <a href="{{ url('/') }}" class="rounded-md bg-gray-950/50 px-3 py-2 text-sm font-medium text-white">
                                    Home
                                </a>
                                <a href="{{ url('/about') }}" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white">
                                    About
                                </a>
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-red-400">
                                    Logout
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-4 flex items-center md:ml-6">
                            <img class="h-8 w-8 rounded-full outline outline-1 outline-white/10"
                                 src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                                 alt="User">
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    {{-- Form logout hidden --}}
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
        @csrf
    </form>

    {{-- Konten Utama --}}
    <main class="flex-1 container mx-auto px-4 py-12">
        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-white mb-4">
                Selamat Datang, {{ Auth::user()->name ?? 'Admin' }}!
            </h2>
            <p class="text-gray-400 mb-6">
                Ini adalah halaman Home sederhana. Kamu bisa mengubah kontennya sesuai kebutuhan.
            </p>
            <a href="{{ url('/dashboard') }}"
               class="inline-block bg-indigo-600 text-white font-semibold px-6 py-2 rounded-lg shadow hover:bg-indigo-500 transition">
                Pergi ke Dashboard
            </a>
        </div>

        {{-- Section tambahan --}}
        <section class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-white mb-2">Fitur 1</h3>
                <p class="text-gray-400 text-sm">Deskripsi singkat tentang fitur pertama.</p>
            </div>
            <div class="bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-white mb-2">Fitur 2</h3>
                <p class="text-gray-400 text-sm">Deskripsi singkat tentang fitur kedua.</p>
            </div>
            <div class="bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-white mb-2">Fitur 3</h3>
                <p class="text-gray-400 text-sm">Deskripsi singkat tentang fitur ketiga.</p>
            </div>
        </section>
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-800 border-t border-gray-700">
        <div class="container mx-auto px-4 py-4 text-center text-sm text-gray-400">
            &copy; {{ date('Y') }} MyApp. Semua hak dilindungi.
        </div>
    </footer>
</body>
</html>
