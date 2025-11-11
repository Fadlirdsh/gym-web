@extends('layout.app')

@section('content')

{{-- ✅ Wrapper mengikuti style layout yang benar --}}
<div class=" p-8 pt-24 w-full">

    <div class="text-center mb-10">
        <h2 class="text-3xl font-extrabold text-white mb-2">
            Selamat Datang, {{ $user->name ?? 'Admin' }} 👋
        </h2>
        <p class="text-gray-400">
            Berikut ringkasan aktivitas hari ini di sistem Gym kamu.
        </p>
    </div>

    {{-- 📊 Kartu Statistik --}}
    <section class="grid grid-cols-1 md:grid-cols-3 gap-6">

        <div class="bg-gray-800 rounded-lg shadow p-6 text-center">
            <h3 class="text-lg font-semibold text-white mb-2">Total Pelanggan</h3>
            <p class="text-gray-400 text-sm mt-1">Jumlah seluruh pelanggan terdaftar</p>
        </div>

        <div class="bg-gray-800 rounded-lg shadow p-6 text-center">
            <h3 class="text-lg font-semibold text-white mb-2">Reservasi Hari Ini</h3>
            <p class="text-gray-400 text-sm mt-1">Total reservasi yang masuk hari ini</p>
        </div>

        <div class="bg-gray-800 rounded-lg shadow p-6 text-center">
            <h3 class="text-lg font-semibold text-white mb-2">Kunjungan Hari Ini</h3>
            <p class="text-gray-400 text-sm mt-1">Jumlah pelanggan yang hadir hari ini</p>
        </div>

    </section>

    {{-- Tombol --}}
    <div class="mt-12 flex flex-wrap justify-center gap-4">
        <a href="{{ url('admin/dashboard') }}"
            class="bg-indigo-600 text-white px-6 py-3 rounded-lg shadow hover:bg-indigo-500 transition">
            📈 Lihat Dashboard Data
        </a>

        <a href="{{ url('admin/visitlog') }}"
            class="bg-green-600 text-white px-6 py-3 rounded-lg shadow hover:bg-green-500 transition">
            📋 Lihat Visit Log
        </a>
    </div>

</div>

@endsection
