@extends('layout.app')

@section('title', 'Jadwal Trainer')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4 text-2xl font-bold">Jadwal Trainer</h1>

        <!-- Button Tambah Jadwal -->
        <button id="btnAddSchedule" class="bg-blue-600 text-white px-4 py-2 rounded mb-4">
            + Tambah Jadwal
        </button>

        <!-- TABLE JADWAL -->
        <table class="table-auto w-full mt-2 border text-sm">
            <thead class="bg-gray-200 text-center">
                <tr>
                    <th class="border px-2 py-1">#</th>
                    <th class="border px-2 py-1">Hari</th>
                    <th class="border px-2 py-1">Jam</th>
                    <th class="border px-2 py-1">Kelas</th>
                    <th class="border px-2 py-1">Trainer</th>
                    <th class="border px-2 py-1">Status</th>
                    <th class="border px-2 py-1">Aksi</th>
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

@endsection
