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

    {{-- =========================== --}}
    {{-- FORM FILTER --}}
    {{-- =========================== --}}
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

    {{-- =========================== --}}
    {{-- TABEL JADWAL --}}
    {{-- =========================== --}}
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
                        $reservasi = $schedule->reservasi->first();
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
    <div class="container py-4">
        <h1 class="mb-4 text-2xl font-bold">Jadwal Trainer</h1>

<div class="container py-4">
    <h1 class="mb-4 text-2xl font-bold">Jadwal Latihan Client</h1>


    {{-- =========================== --}}
    {{-- FORM FILTER --}}
    {{-- =========================== --}}
    <form method="GET" action="{{ route('schedules.index') }}" class="flex flex-wrap gap-3 items-end mb-5">
        <div>
            <label class="block text-sm font-medium">Nama Client</label>
            <input type="text" name="client" value="{{ request('client') }}" placeholder="Cari nama client"
                class="border rounded p-2 w-48">
        </div>

        <div>
            <label class="block text-sm font-medium">Tanggal</label>
            <input type="date" name="date" value="{{ request('date') }}" class="border rounded p-2 w-40">
        </div>

        <div>
            <label class="block text-sm font-medium">Jam</label>
            <input type="time" name="time" value="{{ request('time') }}" class="border rounded p-2 w-36">
        </div>

        <div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                🔍 Cari
            </button>
            <a href="{{ route('schedules.index') }}" class="bg-gray-400 text-white px-4 py-2 rounded">
                Reset
            </a>
        </div>
    </form>

    {{-- =========================== --}}
    {{-- TABEL JADWAL --}}
    {{-- =========================== --}}
    <table class="table-auto w-full mt-2 border text-sm">
        <thead class="bg-gray-200 text-center">
            <tr>
                <th class="border px-2 py-1">#</th>
                <th class="border px-2 py-1">Hari</th>
                <th class="border px-2 py-1">Tanggal</th>
                <th class="border px-2 py-1">Bulan</th>
                <th class="border px-2 py-1">Jam</th>
                <th class="border px-2 py-1">Kelas</th>
                <th class="border px-2 py-1">Paket</th>
                <th class="border px-2 py-1">Trainer</th>
                <th class="border px-2 py-1">Client</th>
                <th class="border px-2 py-1">Status Kehadiran</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($schedules as $key => $schedule)
                @php
                    $reservasi = $schedule->reservasi->first();
                    $pelanggan = $reservasi?->pelanggan?->name ?? '-';
                    $status = $reservasi?->status ?? '-';
                    $tanggal = \Carbon\Carbon::parse($schedule->time)->format('d');
                    $bulan = \Carbon\Carbon::parse($schedule->time)->translatedFormat('F');
                    $jam = \Carbon\Carbon::parse($schedule->time)->format('H:i');
                @endphp

                <tr class="text-center">
                    <td class="border px-2 py-1">{{ $key + 1 }}</td>
                    <td class="border px-2 py-1">{{ $schedule->day }}</td>
                    <td class="border px-2 py-1">{{ $tanggal }}</td>
                    <td class="border px-2 py-1">{{ $bulan }}</td>
                    <td class="border px-2 py-1">{{ $jam }}</td>
                    <td class="border px-2 py-1">{{ $schedule->kelas->nama_kelas ?? '-' }}</td>
                    <td class="border px-2 py-1">{{ $schedule->kelas->tipe_paket ?? '-' }}</td>
                    <td class="border px-2 py-1">{{ $schedule->trainer->name ?? '-' }}</td>
                    <td class="border px-2 py-1">{{ $pelanggan }}</td>
                    <td class="border px-2 py-1">
                        @if ($status === 'hadir')
                            <span class="text-green-600 font-semibold">Hadir</span>
                        @elseif ($status === 'tidak_hadir')
                            <span class="text-red-600 font-semibold">Tidak Hadir</span>
                        @else
                            <span class="text-gray-500">-</span>
                        @endif
                    </td>
                </tr>
            </thead>
            <tbody>
                @forelse ($schedules as $key => $schedule)
                    <tr class="text-center">
                        <td class="border px-2 py-1">{{ $key + 1 }}</td>
                        <td class="border px-2 py-1">{{ $schedule->day }}</td>
                        <td class="border px-2 py-1">{{ \Carbon\Carbon::parse($schedule->time)->format('H:i') }}</td>
                        <td class="border px-2 py-1">{{ $schedule->kelas->nama_kelas ?? '-' }}</td>
                        <td class="border px-2 py-1">{{ $schedule->trainer->name ?? '-' }}</td>
                        <td class="border px-2 py-1">
                            @if ($schedule->is_active)
                                <span class="text-green-600 font-semibold">Aktif</span>
                            @else
                                <span class="text-red-600 font-semibold">Nonaktif</span>
                            @endif
                        </td>
                        <td class="border px-2 py-1 flex gap-1 justify-center">
                            <!-- BUTTON EDIT -->
                            <button class="bg-yellow-500 text-white px-2 py-1 rounded text-xs btnEdit"
                                data-id="{{ $schedule->id }}"
                                data-day="{{ $schedule->day }}"
                                data-time="{{ $schedule->time }}"
                                data-kelas="{{ $schedule->kelas_id }}"
                                data-trainer="{{ $schedule->trainer_id }}"
                                data-status="{{ $schedule->is_active }}">
                                Edit
                            </button>

                            <!-- BUTTON DELETE TANPA MODAL -->
                            <form action="{{ route('schedules.destroy', $schedule->id) }}"
                                  method="POST"
                                  onsubmit="return confirm('Yakin ingin menghapus jadwal ini?')"
                                  class="inline">
                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                        class="bg-red-600 text-white px-2 py-1 rounded text-xs">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center p-3">Belum ada data jadwal</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>


    {{-- ========================= --}}
    {{--      MODAL ADD            --}}
    {{-- ========================= --}}
    <div id="addScheduleModal" class="fixed inset-0 bg-black/50 hidden justify-center items-center z-50">

        <div class="bg-white p-6 rounded shadow-lg w-96 text-black">

            <div class="flex justify-between mb-4">
                <h2 class="text-xl font-bold">Tambah Jadwal</h2>
                <button id="closeAddModal" class="text-red-600 font-bold">X</button>
            </div>

            <form action="{{ route('schedules.store') }}" method="POST">
                @csrf

                <label>Hari</label>
                <input type="text" name="day" class="border border-black rounded w-full p-2 mb-2">

                <label>Jam</label>
                <input type="time" name="time" class="border border-black rounded w-full p-2 mb-2">

                <label>Kelas</label>
                <select name="kelas_id" class="border border-black rounded w-full p-2 mb-2 text-black">
                    @foreach ($kelas as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>

                <label>Trainer</label>
                <select name="trainer_id" class="border border-black rounded w-full p-2 mb-2 text-black">
                    @foreach ($trainers as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>

                <label>Status</label>
                <select name="is_active" class="border border-black rounded w-full p-2 mb-2 text-black">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>

                <button class="bg-blue-600 text-white px-3 py-2 rounded w-full mt-2">
                    Tambah
                </button>
            </form>
        </div>
    </div>

    {{-- ========================= --}}
    {{--      MODAL EDIT           --}}
    {{-- ========================= --}}
    <div id="editScheduleModal" class="fixed inset-0 bg-black/50 hidden justify-center items-center z-50">

        <div class="bg-white p-6 rounded shadow-lg w-96">
            <div class="flex justify-between mb-4">
                <h2 class="text-xl font-bold">Edit Jadwal</h2>
                <button id="closeEditModal" class="text-red-600 font-bold">X</button>
            </div>

            <form id="editForm" method="POST">
                @csrf
                @method('PUT')

                <label>Hari</label>
                <input id="editDay" type="text" name="day" class="border rounded w-full p-2 mb-2">

                <label>Jam</label>
                <input id="editTime" type="time" name="time" class="border rounded w-full p-2 mb-2">

                <label>Kelas</label>
                <select id="editKelas" name="kelas_id" class="border rounded w-full p-2 mb-2">
                    @foreach ($kelas as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>

                <label>Trainer</label>
                <select id="editTrainer" name="trainer_id" class="border rounded w-full p-2 mb-2">
                    @foreach ($trainers as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>

                <label>Status</label>
                <select id="editStatus" name="is_active" class="border rounded w-full p-2 mb-2">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>

                <button class="bg-blue-600 text-white px-3 py-2 rounded w-full mt-2">
                    Simpan
                </button>
            </form>
        </div>
    </div>

    @vite('resources/js/schedule.js')

            @empty
                <tr>
                    <td colspan="10" class="text-center p-3">Belum ada data jadwal</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
