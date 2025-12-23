@extends('layout.app')

@section('content')

<div class="min-h-screen px-4 sm:px-6 lg:px-8 pt-20 sm:pt-24 pb-20
            bg-gradient-to-br
            from-indigo-100/40 via-white to-sky-100/40
            dark:from-indigo-950 dark:via-slate-900 dark:to-slate-950
            transition-colors duration-500">

    <!-- ================= HERO HEADER ================= -->
    <div class="relative mb-16 overflow-hidden rounded-3xl
                bg-gradient-to-r from-indigo-600 via-violet-600 to-fuchsia-600
                text-white shadow-2xl">

        <!-- glow -->
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_white,_transparent_60%)] opacity-20"></div>

        <div class="relative p-8 sm:p-10">
            <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">
                Dashboard Admin
            </h1>

            <p class="mt-3 max-w-2xl text-indigo-100">
                Selamat datang kembali,
                <span class="font-semibold text-white">
                    {{ $user->name ?? 'Admin' }}
                </span>.
                Pantau performa dan aktivitas Gym kamu secara real-time.
            </p>
        </div>
    </div>

    <!-- ================= STAT CARDS ================= -->
    <section class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-20">

        <!-- Total Pelanggan -->
        <div class="group relative rounded-3xl p-6
                    bg-gradient-to-br from-white to-indigo-50
                    dark:from-slate-900 dark:to-indigo-950
                    border border-indigo-200/60 dark:border-indigo-500/30
                    shadow-md hover:shadow-2xl
                    hover:-translate-y-1 transition-all duration-300">

            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                    Total Pelanggan
                </p>

                <div class="w-12 h-12 rounded-2xl
                            flex items-center justify-center
                            bg-gradient-to-br from-indigo-500 to-violet-500
                            text-white shadow-lg
                            group-hover:rotate-6 group-hover:scale-110
                            transition-all duration-300">
                    <i class="fa-solid fa-users text-lg"></i>
                </div>
            </div>

            <p class="text-4xl font-extrabold text-slate-900 dark:text-white">
                {{ $totalPelanggan ?? 0 }}
            </p>

            <p class="mt-1 text-xs text-slate-600 dark:text-slate-400">
                Seluruh member terdaftar
            </p>
        </div>

        <!-- Reservasi Hari Ini -->
        <div class="group relative rounded-3xl p-6
                    bg-gradient-to-br from-white to-emerald-50
                    dark:from-slate-900 dark:to-emerald-950
                    border border-emerald-200/60 dark:border-emerald-500/30
                    shadow-md hover:shadow-2xl
                    hover:-translate-y-1 transition-all duration-300">

            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                    Reservasi Hari Ini
                </p>

                <div class="w-12 h-12 rounded-2xl
                            flex items-center justify-center
                            bg-gradient-to-br from-emerald-500 to-green-500
                            text-white shadow-lg
                            group-hover:rotate-6 group-hover:scale-110
                            transition-all duration-300">
                    <i class="fa-solid fa-calendar-check text-lg"></i>
                </div>
            </div>

            <p class="text-4xl font-extrabold text-emerald-600 dark:text-emerald-400">
                {{ $reservasiHariIni ?? 0 }}
            </p>

            <p class="mt-1 text-xs text-slate-600 dark:text-slate-400">
                Reservasi masuk hari ini
            </p>
        </div>

        <!-- Kunjungan Hari Ini -->
        <div class="group relative rounded-3xl p-6
                    bg-gradient-to-br from-white to-amber-50
                    dark:from-slate-900 dark:to-amber-950
                    border border-amber-200/60 dark:border-amber-500/30
                    shadow-md hover:shadow-2xl
                    hover:-translate-y-1 transition-all duration-300">

            <div class="flex items-center justify-between mb-4">
                <p class="text-sm font-semibold text-slate-800 dark:text-slate-200">
                    Kunjungan Hari Ini
                </p>

                <div class="w-12 h-12 rounded-2xl
                            flex items-center justify-center
                            bg-gradient-to-br from-amber-500 to-orange-500
                            text-white shadow-lg
                            group-hover:rotate-6 group-hover:scale-110
                            transition-all duration-300">
                    <i class="fa-solid fa-person-walking text-lg"></i>
                </div>
            </div>

            <p class="text-4xl font-extrabold text-amber-600 dark:text-amber-400">
                {{ $kunjunganHariIni ?? 0 }}
            </p>

            <p class="mt-1 text-xs text-slate-600 dark:text-slate-400">
                Member hadir hari ini
            </p>
        </div>

    </section>

    <!-- ================= QUICK ACTION ================= -->
    <section>
        <h2 class="mb-6 text-xl font-semibold text-slate-900 dark:text-white">
            Quick Action
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 max-w-3xl">

            <a href="{{ url('admin/dashboard') }}"
               class="group relative overflow-hidden
                      flex items-center justify-between
                      px-6 py-4 rounded-2xl
                      bg-gradient-to-r from-indigo-600 to-violet-600
                      text-white font-semibold
                      shadow-xl hover:shadow-2xl
                      transition-all duration-300">

                <span class="flex items-center gap-3">
                    <i class="fa-solid fa-chart-line"></i>
                    Dashboard Data
                </span>

                <i class="fa-solid fa-arrow-right
                          group-hover:translate-x-1 transition"></i>
            </a>

            <a href="{{ url('admin/visitlog') }}"
               class="group relative overflow-hidden
                      flex items-center justify-between
                      px-6 py-4 rounded-2xl
                      bg-gradient-to-r from-emerald-600 to-green-600
                      text-white font-semibold
                      shadow-xl hover:shadow-2xl
                      transition-all duration-300">

                <span class="flex items-center gap-3">
                    <i class="fa-solid fa-clipboard-list"></i>
                    Visit Log
                </span>

                <i class="fa-solid fa-arrow-right
                          group-hover:translate-x-1 transition"></i>
            </a>

        </div>
    </section>

</div>

@endsection
