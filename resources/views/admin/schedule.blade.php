@extends('layout.app')

@section('content')
    <div class="min-h-screen bg-gray-100 text-gray-900 px-6 py-8">

        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-3xl font-bold">Manajemen Jadwal Trainer</h1>


            <a href="{{ route('schedules.exportPDF', request()->all()) }}" class="px-4 py-2 bg-red-600 text-white rounded">
                Export PDF
            </a>


            <button onclick="openModalTambah()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                + Tambah Jadwal
            </button>
        </div>

        {{-- FILTER --}}
        <form method="GET" class="mb-4 flex gap-3 items-end">
            <div>
                <label>Hari</label>
                <select name="day" class="border p-2 rounded">
                    <option value="">Semua</option>
                    @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $d)
                        <option value="{{ $d }}" {{ request('day') == $d ? 'selected' : '' }}>
                            {{ ucfirst($d) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Trainer</label>
                <select name="trainer_id" class="border p-2 rounded">
                    <option value="">Semua</option>
                    @foreach ($trainers as $t)
                        <option value="{{ $t->id }}" {{ request('trainer_id') == $t->id ? 'selected' : '' }}>
                            {{ $t->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Jam Mulai</label>
                <select name="start_time" class="border p-2 rounded">
                    <option value="">Pilih jam mulai</option>
                    <option value="07:00:00" {{ request('start_time') == '07:00:00' ? 'selected' : '' }}>7:00 AM
                    </option>
                    <option value="07:30:00" {{ request('start_time') == '07:30:00' ? 'selected' : '' }}>7:30 AM
                    </option>
                    <option value="08:00:00" {{ request('start_time') == '08:00:00' ? 'selected' : '' }}>8:00 AM
                    </option>
                    <option value="08:30:00" {{ request('start_time') == '08:30:00' ? 'selected' : '' }}>8:30 AM
                    </option>
                    <option value="09:00:00" {{ request('start_time') == '09:00:00' ? 'selected' : '' }}>9:00 AM
                    </option>
                    <option value="09:30:00" {{ request('start_time') == '09:30:00' ? 'selected' : '' }}>9:30 AM
                    </option>
                    <option value="10:00:00" {{ request('start_time') == '10:00:00' ? 'selected' : '' }}>10:00 AM
                    </option>
                    <option value="10:30:00" {{ request('start_time') == '10:30:00' ? 'selected' : '' }}>10:30 AM
                    </option>
                    <option value="11:00:00" {{ request('start_time') == '11:00:00' ? 'selected' : '' }}>11:00 AM
                    </option>
                    <option value="11:30:00" {{ request('start_time') == '11:30:00' ? 'selected' : '' }}>11:30 AM
                    </option>
                    <option value="12:00:00" {{ request('start_time') == '12:00:00' ? 'selected' : '' }}>12:00 PM
                    </option>
                    <option value="12:30:00" {{ request('start_time') == '12:30:00' ? 'selected' : '' }}>12:30 PM
                    </option>
                    <option value="14:00:00" {{ request('start_time') == '14:00:00' ? 'selected' : '' }}>2:00 PM
                    </option>
                    <option value="14:30:00" {{ request('start_time') == '14:30:00' ? 'selected' : '' }}>2:30 PM
                    </option>
                    <option value="15:00:00" {{ request('start_time') == '15:00:00' ? 'selected' : '' }}>3:00 PM
                    </option>
                    <option value="15:30:00" {{ request('start_time') == '15:30:00' ? 'selected' : '' }}>3:30 PM
                    </option>
                    <option value="16:00:00" {{ request('start_time') == '16:00:00' ? 'selected' : '' }}>4:00 PM
                    </option>
                    <option value="16:30:00" {{ request('start_time') == '16:30:00' ? 'selected' : '' }}>4:30 PM
                    </option>
                    <option value="17:00:00" {{ request('start_time') == '17:00:00' ? 'selected' : '' }}>5:00 PM
                    </option>
                    <option value="18:00:00" {{ request('start_time') == '18:00:00' ? 'selected' : '' }}>6:00 PM
                    </option>
                    <option value="19:00:00" {{ request('start_time') == '19:00:00' ? 'selected' : '' }}>7:00 PM
                    </option>
                </select>
            </div>

            <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Filter</button>
            <a href="{{ route('schedules.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded">Reset</a>
        </form>

        {{-- TABLE --}}
        <div class="bg-white p-5 rounded-lg shadow overflow-x-auto">
            <table class="w-full text-left border-collapse" id="scheduleTable">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="p-3">Kelas</th>
                        <th class="p-3">Trainer</th>
                        <th class="p-3">Hari</th>
                        <th class="p-3">Jam</th>
                        <th class="p-3">Fokus</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($schedules as $s)
                        <tr>
                            <td class="p-3">{{ $s->kelas->nama_kelas }}</td>
                            <td class="p-3">{{ $s->trainer->name }}</td>
                            <td class="p-3">{{ $s->day }}</td>
                            <td class="p-3">{{ $s->start_time }} - {{ $s->end_time }}</td>
                            <td class="p-3">{{ $s->class_focus ?? '-' }}</td>
                            <td class="p-3">
                                @if ($s->is_active)
                                    <span class="text-green-600 font-semibold">Aktif</span>
                                @else
                                    <span class="text-red-600 font-semibold">Nonaktif</span>
                                @endif
                            </td>
                            <td class="p-3 flex gap-2">
                                <button onclick="editSchedule({{ $s->id }})"
                                    class="px-3 py-1 bg-yellow-500 text-white rounded">Edit</button>
                                <button onclick="deleteSchedule({{ $s->id }})" data-id="{{ $s->id }}"
                                    class="px-3 py-1 bg-red-500 text-white rounded">Hapus</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- =========================
      MODAL FORM JADWAL
========================= --}}
    <div id="modalSchedule" class="hidden fixed inset-0 bg-black/50 items-center justify-center p-4">

        <div class="bg-white w-96 rounded p-6 relative">

            <button onclick="closeModal()" class="absolute top-2 right-2 text-black">
                ✕
            </button>

            <h2 id="modalTitle" class="text-xl font-bold mb-4">Tambah Jadwal</h2>

            <form id="scheduleForm">

                @csrf

                <input type="hidden" id="schedule_id">

                <label class="block">Kelas</label>
                <select id="kelas_id" class="border w-full p-2 mb-4">
                    @foreach ($kelas as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>

                <label class="block">Trainer</label>
                <select id="trainer_id" class="border w-full p-2 mb-4">
                    @foreach ($trainers as $t)
                        <option value="{{ $t->id }}">{{ $t->name }}</option>
                    @endforeach
                </select>

                <label class="block">Hari</label>
                <select name="day" id="day" class="w-full mt-1 px-3 py-2 rounded-lg bg-gray-200 dark:bg-gray-700">
                    <option value="">Semua</option>
                    <option value="Monday" {{ request('day') == 'Monday' ? 'selected' : '' }}>Senin</option>
                    <option value="Tuesday" {{ request('day') == 'Tuesday' ? 'selected' : '' }}>Selasa</option>
                    <option value="Wednesday" {{ request('day') == 'Wednesday' ? 'selected' : '' }}>Rabu</option>
                    <option value="Thursday" {{ request('day') == 'Thursday' ? 'selected' : '' }}>Kamis</option>
                    <option value="Friday" {{ request('day') == 'Friday' ? 'selected' : '' }}>Jumat</option>
                    <option value="Saturday" {{ request('day') == 'Saturday' ? 'selected' : '' }}>Sabtu</option>
                    <option value="Sunday" {{ request('day') == 'Sunday' ? 'selected' : '' }}>Minggu</option>
                </select>

                <label class="block">Jam Mulai</label>
                <select id="start_time" class="border w-full p-2 mb-4">
                    <option value="">Pilih jam mulai</option>
                    <option value="07:00:00">7:00 AM</option>
                    <option value="07:30:00">7:30 AM</option>
                    <option value="08:00:00">8:00 AM</option>
                    <option value="08:30:00">8:30 AM</option>
                    <option value="09:00:00">9:00 AM</option>
                    <option value="09:30:00">9:30 AM</option>
                    <option value="10:00:00">10:00 AM</option>
                    <option value="10:30:00">10:30 AM</option>
                    <option value="11:00:00">11:00 AM</option>
                    <option value="11:30:00">11:30 AM</option>
                    <option value="12:00:00">12:00 PM</option>
                    <option value="12:30:00">12:30 PM</option>
                    <option value="14:00:00">2:00 PM</option>
                    <option value="14:30:00">2:30 PM</option>
                    <option value="15:00:00">3:00 PM</option>
                    <option value="15:30:00">3:30 PM</option>
                    <option value="16:00:00">4:00 PM</option>
                    <option value="16:30:00">4:30 PM</option>
                    <option value="17:00:00">5:00 PM</option>
                    <option value="18:00:00">6:00 PM</option>
                    <option value="19:00:00">7:00 PM</option>
                </select>

                <label class="block">Jam Selesai</label>
                <select id="end_time" class="border w-full p-2 mb-4">
                    <option value="">Pilih jam selesai</option>
                    <option value="07:00:00">7:00 AM</option>
                    <option value="07:30:00">7:30 AM</option>
                    <option value="08:00:00">8:00 AM</option>
                    <option value="08:30:00">8:30 AM</option>
                    <option value="09:00:00">9:00 AM</option>
                    <option value="09:30:00">9:30 AM</option>
                    <option value="10:00:00">10:00 AM</option>
                    <option value="10:30:00">10:30 AM</option>
                    <option value="11:00:00">11:00 AM</option>
                    <option value="11:30:00">11:30 AM</option>
                    <option value="12:00:00">12:00 PM</option>
                    <option value="12:30:00">12:30 PM</option>
                    <option value="14:00:00">2:00 PM</option>
                    <option value="14:30:00">2:30 PM</option>
                    <option value="15:00:00">3:00 PM</option>
                    <option value="15:30:00">3:30 PM</option>
                    <option value="16:00:00">4:00 PM</option>
                    <option value="16:30:00">4:30 PM</option>
                    <option value="17:00:00">5:00 PM</option>
                    <option value="18:00:00">6:00 PM</option>
                    <option value="19:00:00">7:00 PM</option>
                </select>

                <label class="block">Fokus Kelas</label>
                <input type="text" id="class_focus" class="border w-full p-2 mb-4">

                <label class="block">Status</label>
                <select id="is_active" class="border w-full p-2 mb-4">
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>

                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded w-full">
                    Simpan
                </button>

            </form>

        </div>
    </div>

    @vite('resources/js/schedule.js')
@endsection
