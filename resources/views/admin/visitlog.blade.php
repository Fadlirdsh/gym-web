@extends('layout.app')

@section('title', 'Visit Log')

@section('content')

<style>
/* =========================================================
   VISIT LOG - Light & Dark - Glassmorphism Style
========================================================= */

/* Card */
.card {
    background-color: #f8fafc;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    backdrop-filter: blur(8px);
    transition: background 0.3s, box-shadow 0.3s, transform 0.2s;
}
.card:hover {
    transform: translateY(-2px);
}
@media (prefers-color-scheme: dark) {
    .card {
        background-color: rgba(31,41,55,0.8);
        box-shadow: 0 8px 30px rgba(0,0,0,0.5);
        backdrop-filter: blur(12px);
    }
}

/* Input & Datepicker */
.input-fix {
    background-color: rgba(255,255,255,0.85);
    border: 1px solid rgba(0,0,0,0.15);
    border-radius: 12px;
    padding: 0.75rem 1rem;
    color: #1e293b;
    font-size: 1rem;
    width: 100%;
    transition: all 0.3s;
}
.input-fix:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79,70,229,0.25);
    outline: none;
}
@media (prefers-color-scheme: dark) {
    .input-fix {
        background-color: rgba(255,255,255,0.08);
        color: #f1f5f9;
        border: 1px solid rgba(255,255,255,0.25);
    }
    .input-fix:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.3);
    }
}

/* Buttons */
button, .quick-filter a {
    border-radius: 10px;
    font-weight: 600;
    padding: 0.5rem 1rem;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-block;
}
button:hover, .quick-filter a:hover {
    cursor: pointer;
    transform: translateY(-1px);
}

/* Table */
.table-wrapper {
    overflow-x: auto;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    backdrop-filter: blur(8px);
    background-color: #f8fafc;
}
@media (prefers-color-scheme: dark) {
    .table-wrapper {
        background-color: rgba(31,41,55,0.8);
        box-shadow: 0 8px 30px rgba(0,0,0,0.5);
        backdrop-filter: blur(12px);
    }
}
table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
}
table th, table td {
    padding: 0.75rem 1rem;
}
table tr {
    border-radius: 12px;
    transition: all 0.2s;
}
table tr:hover {
    background-color: rgba(0,0,0,0.03);
}
@media (prefers-color-scheme: dark) {
    table tr:hover {
        background-color: rgba(255,255,255,0.05);
    }
}
.thead-light {
    background: #f8fafc !important;
    color: #1e293b !important;
}
@media (prefers-color-scheme: dark) {
    .thead-light {
        background: rgba(51,65,85,0.6) !important;
        color: #e2e8f0 !important;
    }
}

/* Badges */
.badge-green { background-color: rgba(16,185,129,0.2); color:#10b981; border-radius:8px; padding:0.25rem 0.5rem; font-size:0.75rem; font-weight:600;}
.badge-yellow { background-color: rgba(245,158,11,0.2); color:#f59e0b; border-radius:8px; padding:0.25rem 0.5rem; font-size:0.75rem; font-weight:600;}
.badge-red { background-color: rgba(239,68,68,0.2); color:#ef4444; border-radius:8px; padding:0.25rem 0.5rem; font-size:0.75rem; font-weight:600;}

/* Quick filter */
.quick-filter a { margin-right: 0.5rem; margin-bottom:0.5rem; }

/* Mobile Table Responsive */
@media (max-width: 640px) {
    table thead { display: none; }
    table tbody tr { display: block; padding:14px; border-radius:12px; margin-bottom:16px; box-shadow:0 2px 10px rgba(0,0,0,0.05); }
    table tbody td { display:flex; justify-content:space-between; padding:8px 0; font-size:14px; }
    table tbody td::before { content:attr(data-label); font-weight:600; color:#64748b; flex-basis:45%; }
}

/* Body transition */
body { transition: background 0.3s, color 0.3s; }
</style>

<div class="container mx-auto px-4 py-8 space-y-10">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
        <div>
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white flex items-center gap-3">
                <span class="text-cyan-600 dark:text-cyan-400 text-5xl">📋</span>
                Visit Log
            </h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Rekap kunjungan lengkap dengan filter & catatan.</p>
        </div>

        {{-- QUICK FILTER --}}
        <div class="flex flex-wrap gap-2 mt-4 md:mt-0 quick-filter">
            @php $q = request('filter'); @endphp
            <a href="?filter=today" class="{{ $q==='today' ? 'bg-cyan-600 text-white shadow' : 'bg-white text-gray-800 border border-gray-300 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600' }}">Hari Ini</a>
            <a href="?filter=yesterday" class="{{ $q==='yesterday' ? 'bg-indigo-600 text-white shadow' : 'bg-white text-gray-800 border border-gray-300 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600' }}">Kemarin</a>
            <a href="?filter=week" class="{{ $q==='week' ? 'bg-emerald-600 text-white shadow' : 'bg-white text-gray-800 border border-gray-300 hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600' }}">Minggu Ini</a>
        </div>
    </div>

    {{-- FILTER FORM --}}
    <form method="GET" class="card p-6 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <input type="text" name="from" value="{{ request('from') }}" placeholder="Dari Tanggal" class="input-fix datepicker">
            <input type="text" name="to" value="{{ request('to') }}" placeholder="Sampai Tanggal" class="input-fix datepicker">
        </div>
        <button class="bg-indigo-600 text-white w-full sm:w-auto">🔍 Filter</button>
    </form>

    {{-- TABLE --}}
    <div class="card shadow-lg overflow-hidden">
        <div class="table-wrapper">
            <table class="min-w-full text-sm md:text-base">
                <thead class="thead-light">
                    <tr>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Jam</th>
                        <th>Kelas</th>
                        <th>Pengajar</th>
                        <th>Status</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visitLogs as $log)
                        <tr>
                            <td data-label="Tanggal">{{ $log->created_at?->format('d F Y') }}</td>
                            <td data-label="Pelanggan">{{ $log->user?->name ?? 'N/A' }}</td>
                            <td data-label="Jam">{{ $log->reservasi->jam_kelas ?? '-' }}</td>
                            <td data-label="Kelas">{{ $log->reservasi->kelas->nama_kelas ?? '-' }}</td>
                            <td data-label="Pengajar">{{ $log->reservasi->pengajar ?? '-' }}</td>
                            <td data-label="Status">
                                @php
                                    $s = strtolower($log->status);
                                    $badge = match($s) {
                                        'approved','attended' => 'badge-green',
                                        'pending' => 'badge-yellow',
                                        default => 'badge-red',
                                    };
                                @endphp
                                <span class="{{ $badge }}">{{ ucfirst($log->status) }}</span>
                            </td>
                            <td data-label="Catatan">{{ $log->catatan ?? '-' }}</td>
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

</div>

@endsection
