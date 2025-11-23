@extends('layout.app')

@section('title', 'Jadwal Trainer')

@section('content')

<div class="min-h-screen bg-gray-900 text-gray-100 px-6 py-8">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-white tracking-wide">
            🏋️‍♂️ Jadwal Latihan Client
        </h1>
        <a href="{{ route('schedules.index') }}" 
           class="mt-3 sm:mt-0 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-medium shadow transition">
            🔄 Refresh
        </a>
    </div>

    {{-- FORM FILTER --}}
    <div class="bg-gray-800/60 border border-gray-700 rounded-2xl p-5 shadow-xl backdrop-blur-md mb-8">
        <form method="GET" action="{{ route('schedules.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-5">
            <div>
                <label class="block text-sm text-gray-400 mb-1">Nama Client</label>
                <input type="text" name="client" value="{{ request('client') }}" placeholder="Cari nama client..."
                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none placeholder-gray-500">
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-1">Tanggal</label>
                <input type="date" name="date" value="{{ request('date') }}"
                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div>
                <label class="block text-sm text-gray-400 mb-1">Jam</label>
                <input type="time" name="time" value="{{ request('time') }}"
                    class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 outline-none">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" 
                    class="flex items-center justify-center gap-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition w-full md:w-auto">
                    🔍 <span>Cari</span>
                </button>
                <a href="{{ route('schedules.index') }}" 
                    class="flex items-center justify-center gap-1 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-medium transition w-full md:w-auto">
                    ♻️ <span>Reset</span>
                </a>
            </div>
        </form>
    </div>

    {{-- TABEL JADWAL --}}
    <div class="bg-gray-800/60 border border-gray-700 rounded-2xl shadow-xl backdrop-blur-md overflow-hidden">
        <table class="w-full text-sm text-gray-200">
            <thead class="bg-gray-700 text-gray-100 text-center uppercase text-xs tracking-wider">
                <tr>
                    <th class="px-3 py-3 border-gray-700">#</th>
                    <th class="px-3 py-3 border-gray-700">Hari</th>
                    <th class="px-3 py-3 border-gray-700">Tanggal</th>
                    <th class="px-3 py-3 border-gray-700">Bulan</th>
                    <th class="px-3 py-3 border-gray-700">Jam</th>
                    <th class="px-3 py-3 border-gray-700">Kelas</th>
                    <th class="px-3 py-3 border-gray-700">Paket</th>
                    <th class="px-3 py-3 border-gray-700">Trainer</th>
                    <th class="px-3 py-3 border-gray-700">Client</th>
                    <th class="px-3 py-3 border-gray-700">Status Kehadiran</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-700">
                @forelse ($schedules as $key => $schedule)
                    @php
                        // FIX ERROR: call to first() on null
                        $reservasi = $schedule->reservasi?->first();
                        
                        $pelanggan = $reservasi?->pelanggan?->name ?? '-';
                        $status = $reservasi?->status ?? '-';

                        $tanggal = \Carbon\Carbon::parse($schedule->time)->format('d');
                        $bulan = \Carbon\Carbon::parse($schedule->time)->translatedFormat('F');
                        $jam = \Carbon\Carbon::parse($schedule->time)->format('H:i');
                    @endphp

                    <tr class="text-center hover:bg-gray-700/40 transition">
                        <td class="px-3 py-2">{{ $key + 1 }}</td>
                        <td class="px-3 py-2 font-medium">{{ $schedule->day }}</td>
                        <td class="px-3 py-2">{{ $tanggal }}</td>
                        <td class="px-3 py-2">{{ $bulan }}</td>
                        <td class="px-3 py-2">{{ $jam }}</td>
                        <td class="px-3 py-2">{{ $schedule->kelas->nama_kelas ?? '-' }}</td>
                        <td class="px-3 py-2">{{ $schedule->kelas->tipe_paket ?? '-' }}</td>
                        <td class="px-3 py-2">{{ $schedule->trainer->name ?? '-' }}</td>
                        <td class="px-3 py-2">{{ $pelanggan }}</td>

                        <td class="px-3 py-2">
                            @if ($status === 'hadir')
                                <span class="inline-flex items-center gap-1 bg-green-700/20 text-green-400 border border-green-600 px-3 py-1 rounded-full text-xs font-semibold">
                                    ✅ Hadir
                                </span>
                            @elseif ($status === 'tidak_hadir')
                                <span class="inline-flex items-center gap-1 bg-red-700/20 text-red-400 border border-red-600 px-3 py-1 rounded-full text-xs font-semibold">
                                    ❌ Tidak Hadir
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-gray-700/30 text-gray-400 border border-gray-600 px-3 py-1 rounded-full text-xs font-semibold">
                                    ⏳ Belum Dikonfirmasi
                                </span>
                            @endif
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="10" class="text-center p-6 text-gray-400">
                            🚫 Belum ada data jadwal latihan untuk saat ini
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

@endsection
