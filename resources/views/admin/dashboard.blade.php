@extends('layout.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto p-8 space-y-10 text-gray-100">
    <!-- Judul -->
    <div class="flex items-center justify-between">
        <h1 class="text-4xl font-bold tracking-tight">🏋️ Dashboard</h1>
        <span class="text-gray-400 text-sm">Selamat datang kembali, Admin 👋</span>
    </div>

    {{-- ===================== --}}
    {{-- STATISTIK UTAMA --}}
    {{-- ===================== --}}
    <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-6 gap-6">
        @php
            $stats = [
                ['label' => 'Total Pelanggan', 'value' => $totalUsers, 'color' => 'from-blue-500 to-cyan-400', 'icon' => '👤'],
                ['label' => 'Total Trainer', 'value' => $totalTrainers, 'color' => 'from-green-500 to-emerald-400', 'icon' => '💪'],
                ['label' => 'Total Kelas', 'value' => $totalClasses, 'color' => 'from-yellow-500 to-amber-400', 'icon' => '📚'],
                ['label' => 'Jadwal Minggu Ini', 'value' => $totalSchedules, 'color' => 'from-indigo-500 to-sky-400', 'icon' => '📅'],
                ['label' => 'Diskon Aktif', 'value' => $activeDiscounts, 'color' => 'from-rose-500 to-pink-400', 'icon' => '🏷️'],
                ['label' => 'Total Reservasi', 'value' => $totalReservations, 'color' => 'from-purple-500 to-fuchsia-400', 'icon' => '📝'],
            ];  
        @endphp

        @foreach ($stats as $stat)
            <div class="bg-gradient-to-br {{ $stat['color'] }} rounded-2xl shadow-lg p-5 flex flex-col items-center text-white transform hover:scale-105 transition-all duration-300">
                <div class="text-4xl mb-3 drop-shadow-md">{{ $stat['icon'] }}</div>
                <span class="text-sm uppercase tracking-wide text-white/80">{{ $stat['label'] }}</span>
                <span class="text-3xl font-extrabold mt-1">{{ $stat['value'] }}</span>
            </div>
        @endforeach
    </div>

    {{-- ===================== --}}
    {{-- GRAFIK TREND --}}
    {{-- ===================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-gray-900/60 backdrop-blur-lg p-6 rounded-2xl shadow-lg border border-gray-800">
            <h2 class="text-xl font-semibold mb-4 text-gray-100 flex items-center gap-2">
                📊 <span>Grafik Reservasi Per Bulan</span>
            </h2>
            <div id="reservationsBar" class="h-72"></div>
        </div>

        <div class="bg-gray-900/60 backdrop-blur-lg p-6 rounded-2xl shadow-lg border border-gray-800">
            <h2 class="text-xl font-semibold mb-4 text-gray-100 flex items-center gap-2">
                🧍 <span>Distribusi Pengguna Baru</span>
            </h2>
            <div id="usersDonut" class="h-72"></div>
        </div>
    </div>

    {{-- ===================== --}}
    {{-- DATA TERBARU --}}
    {{-- ===================== --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-gray-900/60 backdrop-blur-lg p-6 rounded-2xl shadow-lg border border-gray-800">
            <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">🆕 Pengguna Terbaru</h2>
            <ul class="divide-y divide-gray-700">
                @forelse ($latestUsers as $user)
                    <li class="py-3 flex items-center justify-between">
                        <div>
                            <p class="font-semibold">{{ $user->name }}</p>
                            <p class="text-sm text-gray-400 capitalize">{{ $user->role }}</p>
                        </div>
                        <span class="text-xs text-gray-500">
                            {{ $user->created_at->diffForHumans() }}
                        </span>
                    </li>
                @empty
                    <li class="py-3 text-gray-400 text-center">Belum ada pengguna baru</li>
                @endforelse
            </ul>
        </div>

        <div class="bg-gray-900/60 backdrop-blur-lg p-6 rounded-2xl shadow-lg border border-gray-800">
            <h2 class="text-xl font-semibold mb-4 flex items-center gap-2">💸 Diskon Terbaru</h2>
            <ul class="divide-y divide-gray-700">
                @forelse ($latestDiscounts as $diskon)
                    <li class="py-3 flex justify-between">
                        <span class="font-semibold">{{ $diskon->nama_diskon }}</span>
                        <span class="text-green-400 font-bold">{{ $diskon->diskon_persen }}%</span>
                    </li>
                @empty
                    <li class="py-3 text-gray-400 text-center">Belum ada diskon baru</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

{{-- Sisipkan data dari Controller ke window --}}
<script>
    window.dashboardData = {
        reservationsData: @json(array_values($reservationsPerMonth)),
        trainerData: @json(array_values($trainerPerMonth)),
        pelangganData: @json(array_values($pelangganPerMonth)),
    };
</script>

{{-- Tarik file JS --}}
@vite('resources/js/dashboard.js')

@endsection
