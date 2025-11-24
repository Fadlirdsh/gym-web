@extends('layout.app')

@section('title', 'Visit Log')

@section('content')
<div class="min-h-screen bg-gray-900/95 p-8">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white tracking-tight flex items-center gap-2">
                <span>📋</span> Visit Log
            </h1>
            <p class="text-gray-400 text-sm mt-1">
                Rekap data kunjungan pelanggan berdasarkan waktu dan kelas.
            </p>
        </div>

        {{-- Tombol Filter Cepat --}}
        <div class="flex flex-wrap gap-2 mt-4 md:mt-0">
            <a href="?filter=today" 
               class="px-4 py-2 bg-cyan-600/90 hover:bg-cyan-500 text-white rounded-lg text-sm font-medium shadow-sm transition-all duration-300">
               Hari Ini
            </a>
            <a href="?filter=yesterday" 
               class="px-4 py-2 bg-gray-700/80 hover:bg-gray-600 text-white rounded-lg text-sm font-medium shadow-sm transition-all duration-300">
               Kemarin
            </a>
            <a href="?filter=week" 
               class="px-4 py-2 bg-emerald-600/90 hover:bg-emerald-500 text-white rounded-lg text-sm font-medium shadow-sm transition-all duration-300">
               Minggu Ini
            </a>
        </div>
    </div>

    {{-- Filter Rentang Tanggal --}}
    <form method="GET" 
          class="flex flex-wrap items-end gap-4 bg-gray-800/60 border border-gray-700 rounded-2xl p-5 mb-10 shadow-xl backdrop-blur-md">
        <div class="flex flex-col">
            <label for="from" class="text-gray-300 text-sm mb-1 font-medium">Dari Tanggal</label>
            <input type="date" name="from" id="from"
                class="bg-gray-900 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500 focus:outline-none">
        </div>
        <div class="flex flex-col">
            <label for="to" class="text-gray-300 text-sm mb-1 font-medium">Sampai Tanggal</label>
            <input type="date" name="to" id="to"
                class="bg-gray-900 border border-gray-700 text-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-cyan-500 focus:outline-none">
        </div>
        <button type="submit"
            class="h-10 px-5 bg-gradient-to-r from-cyan-500 to-blue-500 text-white rounded-lg font-semibold shadow hover:shadow-lg transition-all duration-300">
            🔍 Filter
        </button>
    </form>

    {{-- Tabel Visit Log --}}
    <div class="overflow-x-auto bg-gray-800/70 border border-gray-700 rounded-2xl shadow-2xl backdrop-blur-lg">
        <table class="min-w-full text-sm text-gray-300">
            <thead class="bg-gray-700/70 text-gray-200 uppercase text-xs tracking-wider">
                <tr>
                    <th class="py-3 px-4 text-left font-semibold">Tanggal</th>
                    <th class="py-3 px-4 text-left font-semibold">Nama Pelanggan</th>
                    <th class="py-3 px-4 text-center font-semibold">Jam Reservasi</th>
                    <th class="py-3 px-4 text-center font-semibold">Kelas</th>
                    <th class="py-3 px-4 text-center font-semibold">Pengajar</th>
                    <th class="py-3 px-4 text-center font-semibold">Status</th>
                    <th class="py-3 px-4 text-left font-semibold">Catatan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700/50">
                @forelse ($visitLogs as $log)
                    <tr class="hover:bg-gray-700/40 transition-all duration-200">
                        {{-- Tanggal --}}
                        <td class="py-3 px-4">
                            <span class="text-gray-100 font-medium">
                                {{ $log->created_at ? $log->created_at->format('d/m/Y') : '-' }}
                            </span>
                        </td>

                        {{-- Nama --}}
                        <td class="py-3 px-4">
                            <span class="font-semibold text-white">{{ $log->user?->name ?? 'N/A' }}</span>
                        </td>

                        {{-- Jam --}}
                        <td class="py-3 px-4 text-center text-gray-300">
                            {{ $log->reservasi->jam_kelas ?? '-' }}
                        </td>

                        {{-- Kelas --}}
                        <td class="py-3 px-4 text-center text-cyan-400 font-semibold">
                            {{ $log->reservasi->kelas->nama_kelas ?? '-' }}
                        </td>

                        {{-- Pengajar --}}
                        <td class="py-3 px-4 text-center text-gray-200">
                            {{ $log->reservasi->pengajar ?? '-' }}
                        </td>

                        {{-- Status --}}
                        <td class="py-3 px-4 text-center">
                            @php
                                $status = strtolower($log->status);
                                $badge = match ($status) {
                                    'attended', 'approved' => 'bg-green-500/20 text-green-400 border border-green-500/30',
                                    'pending' => 'bg-yellow-500/20 text-yellow-300 border border-yellow-500/30',
                                    default => 'bg-red-500/20 text-red-400 border border-red-500/30',
                                };
                            @endphp
                            <span class="px-3 py-1.5 rounded-full text-xs font-semibold {{ $badge }}">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>

                        {{-- Catatan --}}
                        <td class="py-3 px-4 text-gray-300 italic">
                            {{ $log->catatan ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-gray-400 italic">
                            🚫 Tidak ada data kunjungan untuk periode ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    {{-- <div class="mt-6 text-gray-300">
        {{ $visitLogs->appends(request()->query())->links() }}
    </div> --}}
</div>
@endsection
