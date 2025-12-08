@extends('layout.app')

@section('title', 'Visit Log')

@section('content')

{{-- FIX TEKS DATE PICKER TIDAK TERLIHAT DI DARK MODE --}}
<style>
/* ================================
   FIX FINAL FLATPICKR INPUT (LIGHT & DARK)
   ================================ */

/* Light mode */
.flatpickr-input[readonly] {
    background-color: #ffffff !important;
    color: #0f172a !important; /* gray-900 */
    border: 1px solid #d1d5db !important; /* gray-300 */
    padding: 0.5rem 0.75rem !important;
    border-radius: 0.5rem !important;
    font-size: 0.875rem !important;
}

/* Dark mode */
.dark .flatpickr-input[readonly] {
    background-color: #1f2937 !important; /* gray-800 */
    color: #f3f4f6 !important; /* white */
    border: 1px solid #4b5563 !important; /* gray-600 */
}

/* Placeholder fix */
.flatpickr-input::placeholder {
    color: #6b7280 !important; 
}
.dark .flatpickr-input::placeholder {
    color: #d1d5db !important; 
}

/* Remove Flatpickr border shadow */
input.flatpickr-input {
    box-shadow: none !important;
}
</style>


{{-- WRAPPER PAGE --}}
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 p-8 transition">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10">

        <div>
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white flex items-center gap-3">
                <span class="text-cyan-600 dark:text-cyan-400 text-5xl">📋</span>
                Visit Log
            </h1>

            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                Rekap kunjungan lengkap dengan filter & catatan.
            </p>
        </div>

        {{-- QUICK FILTER --}}
        <div class="flex flex-wrap gap-2 mt-4 md:mt-0">
            @php $q = request('filter'); @endphp

            <a href="?filter=today"
                class="px-4 py-2 text-sm font-medium rounded-lg transition
                       {{ $q==='today'
                          ? 'bg-cyan-600 text-white shadow'
                          : 'bg-white text-gray-800 border border-gray-300 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600' }}">
                Hari Ini
            </a>

            <a href="?filter=yesterday"
                class="px-4 py-2 text-sm font-medium rounded-lg transition
                       {{ $q==='yesterday'
                          ? 'bg-indigo-600 text-white shadow'
                          : 'bg-white text-gray-800 border border-gray-300 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600' }}">
                Kemarin
            </a>

            <a href="?filter=week"
                class="px-4 py-2 text-sm font-medium rounded-lg transition
                       {{ $q==='week'
                          ? 'bg-emerald-600 text-white shadow'
                          : 'bg-white text-gray-800 border border-gray-300 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600' }}">
                Minggu Ini
            </a>
        </div>
    </div>


    {{-- FILTER TANGGAL --}}
    <form method="GET"
        class="flex flex-wrap items-end gap-5 
               bg-white dark:bg-gray-800 
               border border-gray-300 dark:border-gray-700 
               rounded-2xl p-6 mb-12 shadow">

        {{-- DARI --}}
        <div class="flex flex-col">
            <label class="text-gray-800 dark:text-gray-200 text-sm mb-1 font-medium">
                Dari Tanggal
            </label>
            <input type="text"
                name="from"
                value="{{ request('from') }}"
                class="datepicker bg-white dark:bg-gray-900 
                       border border-gray-300 dark:border-gray-700
                       text-gray-900 dark:text-gray-100
                       px-3 py-2 rounded-lg shadow-sm focus:ring-2 focus:ring-cyan-500">
        </div>

        {{-- SAMPAI --}}
        <div class="flex flex-col">
            <label class="text-gray-800 dark:text-gray-200 text-sm mb-1 font-medium">
                Sampai Tanggal
            </label>
            <input type="text"
                name="to"
                value="{{ request('to') }}"
                class="datepicker bg-white dark:bg-gray-900 
                       border border-gray-300 dark:border-gray-700
                       text-gray-900 dark:text-gray-100
                       px-3 py-2 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500">
        </div>

        <button
            class="h-[42px] px-6 bg-gradient-to-r from-cyan-600 to-blue-600 text-white 
                   rounded-lg font-semibold shadow hover:brightness-110 transition">
            🔍 Filter
        </button>
    </form>


    {{-- FLATPICKR --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/light.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        flatpickr(".datepicker", {
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d F Y",
        });
    </script>



    {{-- TABLE --}}
    <div class="overflow-hidden rounded-2xl shadow-lg bg-white dark:bg-gray-800 
                border border-gray-300 dark:border-gray-700">

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-gray-900 dark:text-gray-300">

                <thead class="bg-gray-100 dark:bg-gray-700 border-b border-gray-300 dark:border-gray-600">
                    <tr class="uppercase text-xs tracking-wider text-gray-700 dark:text-gray-200">
                        <th class="py-4 px-4 text-left font-semibold">Tanggal</th>
                        <th class="py-4 px-4 text-left font-semibold">Pelanggan</th>
                        <th class="py-4 px-4 text-center font-semibold">Jam</th>
                        <th class="py-4 px-4 text-center font-semibold">Kelas</th>
                        <th class="py-4 px-4 text-center font-semibold">Pengajar</th>
                        <th class="py-4 px-4 text-center font-semibold">Status</th>
                        <th class="py-4 px-4 text-left font-semibold">Catatan</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">

                    @forelse($visitLogs as $log)
                    <tr class="hover:bg-gray-200 dark:hover:bg-gray-700 transition">

                        <td class="py-3 px-4 font-semibold text-gray-900 dark:text-gray-100">
                            {{ $log->created_at?->format('d F Y') }}
                        </td>

                        <td class="py-3 px-4 font-bold">
                            {{ $log->user?->name ?? 'N/A' }}
                        </td>

                        <td class="py-3 px-4 text-center text-cyan-700 dark:text-cyan-300">
                            {{ $log->reservasi->jam_kelas ?? '-' }}
                        </td>

                        <td class="py-3 px-4 text-center">
                            <span class="px-3 py-1 rounded-full 
                                         bg-cyan-200 text-cyan-900 
                                         dark:bg-cyan-600/20 dark:text-cyan-300 
                                         text-xs font-semibold">
                                {{ $log->reservasi->kelas->nama_kelas ?? '-' }}
                            </span>
                        </td>

                        <td class="py-3 px-4 text-center">
                            {{ $log->reservasi->pengajar ?? '-' }}
                        </td>

                        {{-- STATUS --}}
                        <td class="py-3 px-4 text-center">
                            @php
                                $s = strtolower($log->status);
                                $badge = match($s) {
                                    'approved','attended' => 'bg-green-300 text-green-900 dark:bg-green-600/20 dark:text-green-300',
                                    'pending' => 'bg-yellow-300 text-yellow-900 dark:bg-yellow-600/20 dark:text-yellow-300',
                                    default => 'bg-red-300 text-red-900 dark:bg-red-600/20 dark:text-red-300',
                                };
                            @endphp

                            <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $badge }}">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>

                        <td class="py-3 px-4 italic text-gray-700 dark:text-gray-400">
                            {{ $log->catatan ?? '-' }}
                        </td>

                    </tr>

                    @empty
                    <tr>
                        <td colspan="7" class="py-10 text-center text-gray-600 dark:text-gray-400 italic">
                            🚫 Tidak ada data untuk periode ini.
                        </td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

    </div>

</div>

@endsection
