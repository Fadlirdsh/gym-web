@extends('layout.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container mx-auto p-6 space-y-8">
        <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>

        {{-- Statistik --}}
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-6">
            @php
                $stats = [
                    ['label' => 'Total Pelanggan', 'value' => $totalUsers, 'color' => 'bg-blue-500', 'icon' => '👤'],
                    ['label' => 'Total Trainer', 'value' => $totalTrainers, 'color' => 'bg-green-500', 'icon' => '💪'],
                    ['label' => 'Total Kelas', 'value' => $totalClasses, 'color' => 'bg-yellow-500', 'icon' => '📚'],
                    [
                        'label' => 'Jadwal Minggu Ini',
                        'value' => $totalSchedules,
                        'color' => 'bg-indigo-500',
                        'icon' => '📅',
                    ],
                    ['label' => 'Diskon Aktif', 'value' => $activeDiscounts, 'color' => 'bg-red-500', 'icon' => '🏷️'],
                    [
                        'label' => 'Total Reservasi',
                        'value' => $totalReservations,
                        'color' => 'bg-purple-500',
                        'icon' => '📝',
                    ],
                ];
            @endphp

            @foreach ($stats as $stat)
                <div class="{{ $stat['color'] }} text-white rounded-lg shadow p-4 flex flex-col items-center">
                    <div class="text-3xl mb-2">{{ $stat['icon'] }}</div>
                    <div class="text-sm">{{ $stat['label'] }}</div>
                    <div class="text-2xl font-bold">{{ $stat['value'] }}</div>
                </div>
            @endforeach
        </div>

        {{-- Grafik Tren --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Bar Chart Reservasi --}}
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-bold mb-4">Grafik Reservasi Per Bulan</h2>
                <div id="reservationsBar"></div>
            </div>

            {{-- Donut Chart Pengguna Baru --}}
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-bold mb-4">Pengguna Baru (Trainer vs Pelanggan)</h2>
                <div id="usersDonut"></div>
            </div>
        </div>

        {{-- Data terbaru --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-bold mb-4">Pengguna Terbaru</h2>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($latestUsers as $user)
                        @if ($user->role === 'pelanggan')
                            <li>{{ $user->name }} ({{ ucfirst($user->role) }})</li>
                        @endif
                    @endforeach
                </ul>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-xl font-bold mb-4">Diskon Terbaru</h2>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($latestDiscounts as $diskon)
                        <li>{{ $diskon->nama_diskon }} - {{ $diskon->diskon_persen }}%</li>
                    @endforeach
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
