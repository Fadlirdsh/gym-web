@extends('layout.app')

@section('title', 'Manajemen Jadwal Trainer')

@section('content')

    <div class="min-h-screen px-6 py-8 bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">

        {{-- ================= HEADER ================= --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <h1 class="text-3xl font-bold">Manajemen Jadwal Trainer</h1>
            <div class="flex gap-3 flex-wrap">
                <a id="exportShiftPdf" href="#" class="btn btn-red hidden">
                    Export PDF Shift Ini
                </a>

                <button type="button" onclick="openShiftModal()" class="btn btn-primary">
                    + Tambah Shift
                </button>
            </div>
        </div>

        {{-- ================= SHIFT SECTION ================= --}}
        <div class="card mb-10">
            <h2 class="text-2xl font-bold mb-2">Shift Kerja Trainer</h2>
            <p class="text-sm text-gray-500 mb-4">
                Klik baris untuk memilih shift. Gunakan tombol aksi untuk edit / hapus.
            </p>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Trainer</th>
                            <th>Hari</th>
                            <th>Jam Kerja</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($shifts as $shift)
                            <tr class="cursor-pointer"
                                onclick="selectShift(
    {{ $shift->id }},
    '{{ $shift->trainer->name }}',
    '{{ $shift->day }}',
    '{{ $shift->shift_start }}',
    '{{ $shift->shift_end }}'
)">

                                <td>{{ $shift->trainer->name }}</td>
                                <td>{{ $shift->day }}</td>
                                <td>{{ $shift->shift_start }} - {{ $shift->shift_end }}</td>
                                <td>
                                    @if ($shift->is_active)
                                        <span class="badge-green">Aktif</span>
                                    @else
                                        <span class="badge-red">Nonaktif</span>
                                    @endif
                                </td>

                                {{-- ✅ AKSI SHIFT (STOP PROPAGATION) --}}
                                <td class="flex justify-center gap-2">
                                    <button type="button" onclick="event.stopPropagation(); editShift({{ $shift->id }})"
                                        class="btn btn-primary !py-1 !px-3">
                                        Edit
                                    </button>

                                    <button type="button"
                                        onclick="event.stopPropagation(); deleteShift({{ $shift->id }})"
                                        class="btn btn-red !py-1 !px-3">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $shifts->links() }}
            </div>
        </div>

        {{-- ================= SCHEDULE SECTION ================= --}}
        <div id="scheduleSection" class="hidden">

            <div class="flex justify-between items-center mb-4">
                <div>
                    <h2 class="text-2xl font-bold">Jadwal Kelas Dalam Shift</h2>
                    <p id="selectedShiftInfo" class="text-sm text-gray-500"></p>
                </div>

                <button type="button" onclick="openScheduleModal()" class="btn btn-primary">
                    + Tambah Jadwal
                </button>
            </div>

            <div class="table-container p-4">
                <table>
                    <thead>
                        <tr>
                            <th>Kelas</th>
                            <th>Trainer</th>
                            <th>Hari</th>
                            <th>Jam</th>
                            <th>Fokus</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($schedules as $s)
                            <tr data-shift="{{ $s->trainer_shift_id }}">
                                <td>{{ $s->kelas->nama_kelas }}</td>
                                <td>{{ optional($s->trainerShift?->trainer)->name ?? '-' }}</td>
                                <td>{{ $s->trainerShift->day }}</td>
                                <td>{{ $s->start_time }} - {{ $s->end_time }}</td>
                                <td>{{ $s->class_focus ?? '-' }}</td>
                                <td>
                                    @if ($s->is_active)
                                        <span class="badge-green">Aktif</span>
                                    @else
                                        <span class="badge-red">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="flex gap-2">
                                    <button type="button" onclick="editSchedule({{ $s->id }})"
                                        class="btn btn-primary !py-1 !px-3">
                                        Edit
                                    </button>
                                    <button type="button" onclick="deleteSchedule({{ $s->id }})"
                                        class="btn btn-red !py-1 !px-3">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- ================= MODAL SHIFT ================= --}}
        <div id="modalShift" class="modal-overlay hidden">
            <div class="modal-box">
                <button type="button" onclick="closeModal('modalShift')" class="close-btn">✕</button>
                <h2 class="text-xl font-bold mb-4">Tambah Shift Trainer</h2>

                <form id="shiftForm">
                    @csrf
                    <input type="hidden" id="shift_id">


                    <label>Trainer</label>
                    <select id="shift_trainer_id" class="input-field">
                        @foreach ($trainers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>

                    <label>Hari</label>
                    <select id="shift_day" class="input-field">
                        @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $d)
                            <option value="{{ $d }}">{{ $d }}</option>
                        @endforeach
                    </select>

                    <label>Jam Mulai</label>
                    <input type="time" id="shift_start" class="input-field">

                    <label>Jam Selesai</label>
                    <input type="time" id="shift_end" class="input-field">

                    <label>Status</label>
                    <select id="shift_is_active" class="input-field">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>

                    <button type="submit" class="btn btn-primary w-full mt-3">
                        Simpan Shift
                    </button>
                </form>
            </div>
        </div>

        {{-- ================= MODAL SCHEDULE ================= --}}
        <div id="modalSchedule" class="modal-overlay hidden">
            <div class="modal-box">
                <button type="button" onclick="closeModal('modalSchedule')" class="close-btn">✕</button>
                <h2 class="text-xl font-bold mb-4">Kelola Jadwal Kelas</h2>

                <form id="scheduleForm">
                    @csrf
                    <input type="hidden" id="schedule_id">
                    <input type="hidden" id="trainer_shift_id" name="trainer_shift_id">

                    <label>Kelas</label>
                    <select id="kelas_id" class="input-field">
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>

                    <label>Trainer</label>
                    <select id="trainer_id" class="input-field">
                        @foreach ($trainers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                        @endforeach
                    </select>

                    <label>Hari</label>
                    <input type="text" id="day" class="input-field" readonly>

                    <label>Jam Mulai</label>
                    <input type="time" id="start_time" class="input-field">

                    <label>Jam Selesai</label>
                    <input type="time" id="end_time" class="input-field">

                    <label>Kapasitas</label>
                    <input type="number" id="capacity" class="input-field" min="1" placeholder="Contoh: 20">

                    <label>Fokus Kelas</label>
                    <input type="text" id="class_focus" class="input-field">

                    <label>Status</label>
                    <select id="is_active" class="input-field">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>

                    <button type="submit" class="btn btn-primary w-full mt-4">
                        Simpan Jadwal
                    </button>
                </form>
            </div>
        </div>


    </div>

    @vite('resources/js/schedule.js')
    @vite('resources/css/admin/schedule.css')

@endsection
