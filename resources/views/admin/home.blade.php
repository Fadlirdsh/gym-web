@extends('layout.app')

@section('content')

<div class="p-8 pt-24 w-full
            bg-white text-gray-900
            dark:bg-gray-900 dark:text-gray-100">

    {{-- Judul Sambutan --}}
    <div class="text-center mb-10">
        <h2 class="text-3xl font-extrabold 
                   text-gray-900 dark:text-white mb-2">
            Selamat Datang, {{ $user->name ?? 'Admin' }} 👋
        </h2>

        <p class="text-gray-500 dark:text-gray-400">
            Berikut ringkasan aktivitas hari ini di sistem Gym kamu.
        </p>
    </div>

    {{-- 📊 Kartu Statistik --}}
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Total Pelanggan --}}
        <div class="p-6 text-center rounded-xl shadow
                    bg-gray-100 dark:bg-gray-800
                    transition-all duration-200">
            <h3 class="text-lg font-semibold 
                       text-gray-900 dark:text-white mb-2">
                Total Pelanggan
            </h3>
            <p class="text-3xl font-bold text-indigo-500 dark:text-indigo-400">
                {{ $totalPelanggan ?? 0 }}
            </p>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                Jumlah seluruh pelanggan terdaftar
            </p>
        </div>

        {{-- Reservasi Hari Ini --}}
        <div class="p-6 text-center rounded-xl shadow
                    bg-gray-100 dark:bg-gray-800
                    transition-all duration-200">
            <h3 class="text-lg font-semibold 
                       text-gray-900 dark:text-white mb-2">
                Reservasi Hari Ini
            </h3>
            <p class="text-3xl font-bold text-green-500 dark:text-green-400">
                {{ $reservasiHariIni ?? 0 }}
            </p>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                Total reservasi yang masuk hari ini
            </p>
        </div>

        {{-- Kunjungan Hari Ini --}}
        <div class="p-6 text-center rounded-xl shadow
                    bg-gray-100 dark:bg-gray-800
                    transition-all duration-200">
            <h3 class="text-lg font-semibold 
                       text-gray-900 dark:text-white mb-2">
                Kunjungan Hari Ini
            </h3>
            <p class="text-3xl font-bold text-yellow-500 dark:text-yellow-400">
                {{ $kunjunganHariIni ?? 0 }}
            </p>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                Jumlah pelanggan yang hadir hari ini
            </p>
        </div>

    </section>

    {{-- Tombol Navigasi --}}
    <div class="mt-12 flex flex-wrap justify-center gap-4">

        <a href="{{ url('admin/dashboard') }}"
           class="px-6 py-3 rounded-lg shadow font-medium
                  bg-indigo-600 hover:bg-indigo-500 
                  text-white transition">
            📈 Lihat Dashboard Data
        </a>

        <a href="{{ url('admin/visitlog') }}"
           class="px-6 py-3 rounded-lg shadow font-medium
                  bg-green-600 hover:bg-green-500 
                  text-white transition">
            📋 Lihat Visit Log
        </a>

    </div>

</div>

@endsection
