@extends('layout.app')

@section('title', 'Dashboard')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Dashboard</h1>

    {{-- Statistik --}}
    <div class="grid grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded shadow">
            <h2>Total Pelanggan</h2>
            <p class="text-xl font-bold">{{ $totalUsers }}</p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h2>Total Trainer</h2>
            <p class="text-xl font-bold">{{ $totalTrainers }}</p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h2>Total Kelas</h2>
            <p class="text-xl font-bold">{{ $totalClasses }}</p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h2>Total Jadwal Minggu Ini</h2>
            <p class="text-xl font-bold">{{ $totalSchedules }}</p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h2>Diskon Aktif</h2>
            <p class="text-xl font-bold">{{ $activeDiscounts }}</p>
        </div>
        <div class="bg-white p-4 rounded shadow">
            <h2>Total Reservasi</h2>
            <p class="text-xl font-bold">{{ $totalReservations }}</p>
        </div>
    </div>

    {{-- Data terbaru --}}
    <div class="mt-8">
        <h2 class="text-xl font-bold mb-2">Pengguna Terbaru</h2>
        <ul class="list-disc pl-5">
            @foreach($latestUsers as $user)
                <li>{{ $user->name }} ({{ $user->role }})</li>
            @endforeach
        </ul>
    </div>

    <div class="mt-8">
        <h2 class="text-xl font-bold mb-2">Jadwal Mendatang</h2>
        <table class="w-full border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 border">Tanggal</th>
                    <th class="p-2 border">Kelas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($upcomingSchedules as $schedule)
                    <tr>
                        <td class="p-2 border">{{ $schedule->tanggal }}</td>
                        <td class="p-2 border">{{ $schedule->kelas->nama_kelas ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-8">
        <h2 class="text-xl font-bold mb-2">Diskon Terbaru</h2>
        <ul class="list-disc pl-5">
            @foreach($latestDiscounts as $diskon)
                <li>{{ $diskon->nama_diskon }} - {{ $diskon->diskon_persen }}%</li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
