@extends('layout.app')

@section('title', 'Jadwal Trainer')

@section('content')


    <div class="min-h-screen bg-gray-900 text-gray-100 px-6 py-8">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-white tracking-wide">
                🏋️‍♂️ Manajemen Jadwal Trainer
            </h1>

<div class="min-h-screen bg-gray-100 text-gray-900 px-6 py-8 dark:bg-gray-900 dark:text-gray-100">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-center mb-8">
        <h1 class="text-3xl font-bold tracking-wide dark:text-white text-gray-900">
            🏋️‍♂️ Manajemen Jadwal Trainer
        </h1>


            <button onclick="document.getElementById('modalCreate').classList.remove('hidden')"
                class="mt-3 sm:mt-0 bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-lg font-medium shadow transition">
                ➕ Tambah Jadwal
            </button>
        </div>


        {{-- FORM FILTER --}}
        <div class="bg-gray-800/60 border border-gray-700 rounded-2xl p-5 shadow-xl backdrop-blur-md mb-8">
            <form method="GET" action="{{ route('schedules.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-5">

                {{-- Dropdown Trainer --}}
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Trainer</label>
                    <select name="trainer" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm">
                        <option value="">Semua Trainer</option>
                        @foreach ($trainers as $t)
                            <option value="{{ $t->name }}" {{ request('trainer') == $t->name ? 'selected' : '' }}>
                                {{ $t->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Dropdown Kelas --}}
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Kelas</label>
                    <select name="kelas_id" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm">
                        <option value="">Semua Kelas</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                {{ $k->nama_kelas }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Hari --}}
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Hari</label>
                    <select name="day" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm">
                        <option value="">Semua Hari</option>
                        @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $hari)
                            <option value="{{ $hari }}" {{ request('day') == $hari ? 'selected' : '' }}>
                                {{ $hari }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Jam Mulai --}}
                <div>
                    <label class="block text-sm text-gray-400 mb-1">Jam Mulai</label>
                    <select name="start_time"
                        class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm">
                        <option value="">Semua Jam</option>
                        @foreach ($timeOptions as $value => $label)
                            <option value="{{ $value }}" {{ request('start_time') == $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

    {{-- FORM FILTER --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-2xl p-5 shadow mb-8">
        <form method="GET" action="{{ route('schedules.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-5">

            {{-- Dropdown Trainer --}}
            <div>
                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Trainer</label>
                <select name="trainer" class="
                    w-full bg-white dark:bg-gray-900 
                    border border-gray-300 dark:border-gray-700 
                    rounded-lg px-3 py-2 text-sm text-gray-700 dark:text-gray-200
                ">
                    <option value="">Semua Trainer</option>
                    @foreach ($trainers as $t)
                        <option value="{{ $t->name }}" 
                            {{ request('trainer') == $t->name ? 'selected' : '' }}>
                            {{ $t->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Dropdown Kelas --}}
            <div>
                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Kelas</label>
                <select name="kelas_id" class="
                    w-full bg-white dark:bg-gray-900 
                    border border-gray-300 dark:border-gray-700 
                    rounded-lg px-3 py-2 text-sm text-gray-700 dark:text-gray-200
                ">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelas as $k)
                        <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Hari --}}
            <div>
                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Hari</label>
                <select name="day" class="
                    w-full bg-white dark:bg-gray-900 
                    border border-gray-300 dark:border-gray-700 
                    rounded-lg px-3 py-2 text-sm text-gray-700 dark:text-gray-200
                ">
                    <option value="">Semua Hari</option>
                    @foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $hari)
                        <option value="{{ $hari }}" {{ request('day') == $hari ? 'selected' : '' }}>
                            {{ $hari }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Jam Mulai --}}
            <div>
                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Jam Mulai</label>
                <select name="start_time" class="
                    w-full bg-white dark:bg-gray-900 
                    border border-gray-300 dark:border-gray-700 
                    rounded-lg px-3 py-2 text-sm text-gray-700 dark:text-gray-200
                ">
                    <option value="">Semua Jam</option>
                    @foreach ($timeOptions as $value => $label)
                        <option value="{{ $value }}" {{ request('start_time') == $value ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>


                <div class="flex items-end">
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition">
                        🔍 Cari
                    </button>
                </div>

            </form>
        </div>



        {{-- TABEL --}}
        <a href="{{ route('schedules.exportPDF') }}" class="btn btn-primary">
            Export PDF
        </a>
        <div class="bg-gray-800/60 border border-gray-700 rounded-2xl shadow-xl backdrop-blur-md overflow-hidden">
            <table class="w-full text-sm text-gray-200">
                <thead class="bg-gray-700 text-gray-100 uppercase text-xs tracking-wider text-center">
                    <tr>
                        <th class="px-3 py-3">#</th>
                        <th class="px-3 py-3">Hari</th>
                        <th class="px-3 py-3">Jam</th>
                        <th class="px-3 py-3">Kelas</th>
                        <th class="px-3 py-3">Trainer</th>
                        <th class="px-3 py-3">Fokus Kelas</th>
                        <th class="px-3 py-3">Status</th>
                        <th class="px-3 py-3">Aksi</th>

    {{-- TABEL --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-2xl shadow overflow-hidden">
        <table class="w-full text-sm text-gray-800 dark:text-gray-200">
            <thead class="bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-100 uppercase text-xs tracking-wider text-center">
                <tr>
                    <th class="px-3 py-3">#</th>
                    <th class="px-3 py-3">Hari</th>
                    <th class="px-3 py-3">Jam</th>
                    <th class="px-3 py-3">Kelas</th>
                    <th class="px-3 py-3">Trainer</th>
                    <th class="px-3 py-3">Fokus Kelas</th>
                    <th class="px-3 py-3">Status</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-300 dark:divide-gray-700">
                @forelse ($schedules as $key => $schedule)
                    <tr class="text-center hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <td class="px-3 py-2">{{ $key + 1 }}</td>
                        <td class="px-3 py-2 font-medium">{{ $schedule->day }}</td>
                        <td class="px-3 py-2">
                            {{ $schedule->start_time }} - {{ $schedule->end_time }}
                        </td>
                        <td class="px-3 py-2">{{ $schedule->kelas->nama_kelas }}</td>
                        <td class="px-3 py-2">{{ $schedule->trainer->name }}</td>
                        <td class="px-3 py-2">{{ $schedule->class_focus ?? '-' }}</td>

                        <td class="px-3 py-2">
                            @if ($schedule->is_active)
                                <span class="text-green-600 dark:text-green-400 font-semibold">Aktif</span>
                            @else
                                <span class="text-red-600 dark:text-red-400 font-semibold">Nonaktif</span>
                            @endif
                        </td>
                    </tr>

                @empty
                    <tr>
                        <td colspan="10" class="text-center p-6 text-gray-500 dark:text-gray-400">
                            🚫 Belum ada jadwal yang tersedia
                        </td>

                    </tr>
                </thead>


                <tbody class="divide-y divide-gray-700">
                    @forelse ($schedules as $key => $schedule)
                        <tr class="text-center hover:bg-gray-700/40 transition">
                            <td class="px-3 py-2">{{ $key + 1 }}</td>
                            <td class="px-3 py-2 font-medium">{{ $schedule->day }}</td>
                            <td class="px-3 py-2">{{ $schedule->start_time }} - {{ $schedule->end_time }}</td>
                            <td class="px-3 py-2">{{ $schedule->kelas->nama_kelas }}</td>
                            <td class="px-3 py-2">{{ $schedule->trainer->name }}</td>
                            <td class="px-3 py-2">{{ $schedule->class_focus ?? '-' }}</td>
                            <td class="px-3 py-2">
                                @if ($schedule->is_active)
                                    <span class="text-green-400 font-semibold">Aktif</span>
                                @else
                                    <span class="text-red-400 font-semibold">Nonaktif</span>
                                @endif
                            </td>
                            <td class="px-3 py-2 text-center">
                                <button
                                    class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg text-xs font-medium btnEdit"
                                    data-id="{{ $schedule->id }}"
                                    data-url="{{ route('schedules.update', $schedule->id) }}"
                                    data-trainer="{{ $schedule->trainer_id }}" data-kelas="{{ $schedule->kelas_id }}"
                                    data-day="{{ $schedule->day }}" data-start="{{ $schedule->start_time }}"
                                    data-end="{{ $schedule->end_time }}" data-focus="{{ $schedule->class_focus }}"
                                    data-status="{{ $schedule->is_active }}">
                                    ✏️ Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center p-6 text-gray-400">
                                🚫 Belum ada jadwal yang tersedia
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

</div>



{{-- MODAL CREATE --}}
<div id="modalCreate" class="hidden fixed inset-0 bg-black/60 flex items-center justify-center px-4 z-50">
    <div class="bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-2xl shadow-xl w-full max-w-lg p-6">

        <h2 class="text-xl font-bold text-gray-800 dark:text-white mb-4">➕ Tambah Jadwal Trainer</h2>

        <form action="{{ route('schedules.store') }}" method="POST">
            @csrf

            {{-- Trainer --}}
            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Trainer</label>
            <select name="trainer_id" required
                class="w-full mb-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-700 dark:text-gray-200">
                <option value="">Pilih Trainer</option>
                @foreach ($trainers as $t)
                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                @endforeach
            </select>

            {{-- Kelas --}}
            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Kelas</label>
            <select name="kelas_id" required
                class="w-full mb-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-700 dark:text-gray-200">
                <option value="">Pilih Kelas</option>
                @foreach ($kelas as $k)
                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                @endforeach
            </select>

            {{-- Hari --}}
            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Hari</label>
            <select name="day" required
                class="w-full mb-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-700 dark:text-gray-200">
                <option value="">Pilih Hari</option>
                @foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $hari)
                    <option value="{{ $hari }}">{{ $hari }}</option>
                @endforeach
            </select>

            {{-- Jam Mulai --}}
            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Jam Mulai</label>
            <select name="start_time" required
                class="w-full mb-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-700 dark:text-gray-200">
                <option value="">Pilih Jam</option>
                @foreach ($timeOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>

            {{-- Jam Selesai --}}
            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Jam Selesai</label>
            <select name="end_time" required
                class="w-full mb-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-700 dark:text-gray-200">
                <option value="">Pilih Jam</option>
                @foreach ($timeOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>

            {{-- Fokus Kelas --}}
            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Fokus Kelas</label>
            <input type="text" name="class_focus"
                class="w-full mb-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-700 dark:text-gray-200">

            {{-- Status --}}
            <label class="block text-sm text-gray-600 dark:text-gray-400 mb-1">Status</label>
            <select name="is_active"
                class="w-full mb-4 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-lg px-3 py-2 text-sm text-gray-700 dark:text-gray-200">
                <option value="1">Aktif</option>
                <option value="0">Nonaktif</option>
            </select>

            <div class="flex justify-end gap-3 mt-4">
                <button type="button"
                    onclick="document.getElementById('modalCreate').classList.add('hidden')"
                    class="px-4 py-2 bg-gray-400 hover:bg-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 rounded-lg text-white">
                    Batal
                </button>

                <button type="submit"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg text-white">
                    Simpan
                </button>
            </div>

        </form>


    </div>

    {{-- MODAL CREATE SCHEDULE --}}
    <div id="modalCreate" class="hidden fixed inset-0 bg-black/60 items-center justify-center px-4 z-50">
        <div class="bg-gray-800 border border-gray-700 rounded-2xl shadow-xl w-full max-w-lg p-6">

            <h2 class="text-xl font-bold text-white mb-4">➕ Tambah Jadwal Trainer</h2>

            <form id="createForm" action="{{ route('schedules.store') }}" method="POST">
                @csrf

                <label class="block text-sm text-gray-400 mb-1">Trainer</label>
                <select name="trainer_id" required
                    class="w-full mb-3 bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm">
                    <option value="">Pilih Trainer</option>
                    @foreach ($trainers as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>

                <label class="block text-sm text-gray-400 mb-1">Kelas</label>
                <select name="kelas_id" required
                    class="w-full mb-3 bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm">
                    <option value="">Pilih Kelas</option>
                    @foreach ($kelas as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>

                <label class="block text-sm text-gray-400 mb-1">Hari</label>
                <select name="day" required
                    class="w-full mb-3 bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm">
                    <option value="">Pilih Hari</option>
                    @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $hari)
                        <option value="{{ $hari }}">{{ $hari }}</option>
                    @endforeach
                </select>

                <label class="block text-sm text-gray-400 mb-1">Jam Mulai</label>
                <select name="start_time" required
                    class="w-full mb-3 bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm">
                    <option value="">Pilih Jam</option>
                    @foreach ($timeOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                <label class="block text-sm text-gray-400 mb-1">Jam Selesai</label>
                <select name="end_time" required
                    class="w-full mb-3 bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm">
                    <option value="">Pilih Jam</option>
                    @foreach ($timeOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                <label class="block text-sm text-gray-400 mb-1">Fokus Kelas</label>
                <input type="text" name="class_focus"
                    class="w-full mb-3 bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm">

                <label class="block text-sm text-gray-400 mb-1">Status</label>
                <select name="is_active"
                    class="w-full mb-4 bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>

                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" onclick="document.getElementById('modalCreate').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-600 hover:bg-gray-500 rounded-lg text-white">
                        Batal
                    </button>

                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg text-white">
                        Simpan
                    </button>
                </div>

            </form>

        </div>
    </div>


    {{-- GLOBAL MODAL EDIT --}}
    <div id="modalEdit" class="hidden fixed inset-0 bg-black/60 items-center justify-center px-4 z-50">
        <div class="bg-gray-800 border border-gray-700 rounded-2xl shadow-xl w-full max-w-lg p-6">

            <h2 class="text-xl font-bold text-white mb-4">✏️ Edit Jadwal</h2>

            <form id="editForm" method="POST">
                @csrf
                @method('PUT')

                <label class="block text-sm text-gray-400 mb-1">Trainer</label>
                <select id="editTrainer" name="trainer_id" required
                    class="w-full mb-3 bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm">
                    @foreach ($trainers as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>

                <label class="block text-sm text-gray-400 mb-1">Kelas</label>
                <select id="editKelas" name="kelas_id" required
                    class="w-full mb-3 bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm">
                    @foreach ($kelas as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>

                <label class="block text-sm text-gray-400 mb-1">Hari</label>
                <select id="editDay" name="day" required
                    class="w-full mb-3 bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm">
                    @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $hari)
                        <option value="{{ $hari }}">{{ $hari }}</option>
                    @endforeach
                </select>

                <label class="block text-sm text-gray-400 mb-1">Jam Mulai</label>
                <select id="editStart" name="start_time" required
                    class="w-full mb-3 bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm">
                    @foreach ($timeOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                <label class="block text-sm text-gray-400 mb-1">Jam Selesai</label>
                <select id="editEnd" name="end_time" required
                    class="w-full mb-3 bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm">
                    @foreach ($timeOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                <label class="block text-sm text-gray-400 mb-1">Fokus Kelas</label>
                <input id="editFocus" type="text" name="class_focus"
                    class="w-full mb-3 bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm">

                <label class="block text-sm text-gray-400 mb-1">Status</label>
                <select id="editStatus" name="is_active"
                    class="w-full mb-4 bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 text-sm">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>

                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" id="closeEditModal"
                        class="px-4 py-2 bg-gray-600 hover:bg-gray-500 rounded-lg text-white">
                        Batal
                    </button>

                    <button class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 rounded-lg text-white">
                        Update
                    </button>
                </div>

            </form>

        </div>
    </div>

    @vite('resources/js/schedule.js')
@endsection
