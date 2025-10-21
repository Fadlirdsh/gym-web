@extends('layout.app')

@section('title', 'Jadwal Latihan Client')

@section('content')
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
            @empty
                <tr>
                    <td colspan="10" class="text-center p-3">Belum ada data jadwal</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
