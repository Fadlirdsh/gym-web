@extends('layout.app')

@section('title', 'Manajemen Jadwal Kelas')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4 text-2xl font-bold">Manajemen Jadwal Kelas</h1>

        {{-- Tombol tambah jadwal (buka modal Tailwind) --}}
        <button id="btnOpenCreate" type="button" class="bg-blue-600 text-white px-3 py-1 rounded">
            + Tambah Jadwal
        </button>

        {{-- Pesan sukses --}}
        @if (session('success'))
            <div class="mt-3 text-green-600">
                {{ session('success') }}
            </div>
        @endif

        {{-- Tabel daftar jadwal --}}
        <table class="table-auto w-full mt-4 border">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border px-2 py-1">#</th>
                    <th class="border px-2 py-1">Hari</th>
                    <th class="border px-2 py-1">Jam</th>
                    <th class="border px-2 py-1">Kelas</th>
                    <th class="border px-2 py-1">Trainer</th>
                    <th class="border px-2 py-1">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($schedules as $key => $schedule)
                    <tr>
                        <td class="border px-2 py-1">{{ $key + 1 }}</td>
                        <td class="border px-2 py-1">{{ $schedule->day }}</td>
                        <td class="border px-2 py-1">{{ $schedule->time }}</td>
                        <td class="border px-2 py-1">{{ $schedule->kelas->nama_kelas ?? '-' }}</td>
                        <td class="border px-2 py-1">{{ $schedule->trainer->name ?? '-' }}</td>
                        <td class="border px-2 py-1">
                            {{-- Tombol Update --}}
                            <button type="button" class="text-blue-600 btnOpenUpdate" data-id="{{ $schedule->id }}"
                                data-kelas="{{ $schedule->kelas_id }}" data-trainer="{{ $schedule->trainer_id }}"
                                data-day="{{ $schedule->day }}" data-time="{{ $schedule->time }}">
                                Update
                            </button>

                            |

                            {{-- Tombol Hapus --}}
                            <form action="{{ route('schedules.destroy', $schedule->id) }}" method="POST" class="inline"
                                onsubmit="return confirm('Yakin hapus jadwal ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600">Hapus</button>
                            </form>

                            |

                            {{-- Toggle Active pakai switch --}}
                            <form action="{{ route('schedules.toggle', $schedule->id) }}" method="POST"
                                class="inline toggle-form">
                                @csrf
                                @method('PATCH')
                                <label class="switch">
                                    <input type="checkbox" onchange="this.form.submit()"
                                        {{ $schedule->is_active ? 'checked' : '' }}>
                                    <span class="slider round"></span>
                                </label>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center p-3">Belum ada jadwal</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Modal Tambah Jadwal --}}
    <div id="modalCreate" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 animate-fadeIn">
            <h2 class="text-xl font-bold mb-4">Tambah Jadwal</h2>
            <form action="{{ route('schedules.store') }}" method="POST" class="space-y-3">
                @csrf

                <div>
                    <label class="block font-medium">Kelas</label>
                    <select name="kelas_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-medium">Trainer</label>
                    <select name="trainer_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">-- Pilih Trainer --</option>
                        @foreach ($trainers as $trainer)
                            <option value="{{ $trainer->id }}">{{ $trainer->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-medium">Hari</label>
                    <input type="text" name="day" class="w-full border rounded px-3 py-2" placeholder="Contoh: Senin"
                        required>
                </div>

                <div>
                    <label class="block font-medium">Jam</label>
                    <input type="time" name="time" class="w-full border rounded px-3 py-2" required>
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" id="btnCloseCreate"
                        class="px-4 py-2 bg-gray-500 text-black rounded hover:bg-gray-600">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-black rounded hover:bg-green-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Update Jadwal --}}
    <div id="modalUpdate" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 animate-fadeIn">
            <h2 class="text-xl font-bold mb-4">Edit Jadwal</h2>
            <form id="formUpdate" method="POST" class="space-y-3">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-medium">Kelas</label>
                    <select name="kelas_id" id="updateKelas" class="w-full border rounded px-3 py-2" required>
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-medium">Trainer</label>
                    <select name="trainer_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">-- Pilih Trainer --</option>
                        @foreach ($trainers as $trainer)
                            <option value="{{ $trainer->id }}">{{ $trainer->name }}</option>
                        @endforeach
                    </select>

                </div>

                <div>
                    <label class="block font-medium">Hari</label>
                    <input type="text" name="day" id="updateDay" class="w-full border rounded px-3 py-2" required>
                </div>

                <div>
                    <label class="block font-medium">Jam</label>
                    <input type="time" name="time" id="updateTime" class="w-full border rounded px-3 py-2" required>
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" id="btnCloseUpdate"
                        class="px-4 py-2 bg-gray-500 text-black rounded hover:bg-gray-600">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-black rounded hover:bg-green-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Script buka/tutup modal & isi data update --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- Modal Tambah Jadwal ---
            const modalCreate = document.getElementById('modalCreate');
            const btnOpenCreate = document.getElementById('btnOpenCreate');
            const btnCloseCreate = document.getElementById('btnCloseCreate');

            btnOpenCreate.addEventListener('click', () => {
                modalCreate.classList.remove('hidden');
                modalCreate.classList.add('flex'); // supaya muncul ditengah
            });

            btnCloseCreate.addEventListener('click', () => {
                modalCreate.classList.add('hidden');
                modalCreate.classList.remove('flex');
            });

            // --- Modal Update Jadwal ---
            const modalUpdate = document.getElementById('modalUpdate');
            const btnCloseUpdate = document.getElementById('btnCloseUpdate');
            const formUpdate = document.getElementById('formUpdate');

            document.querySelectorAll('.btnOpenUpdate').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    const kelas = btn.dataset.kelas;
                    const trainer = btn.dataset.trainer;
                    const day = btn.dataset.day;
                    const time = btn.dataset.time;

                    document.getElementById('updateKelas').value = kelas;
                    document.getElementById('updateTrainer').value = trainer;
                    document.getElementById('updateDay').value = day;
                    document.getElementById('updateTime').value = time;

                    formUpdate.action = `/schedules/${id}`;
                    modalUpdate.classList.remove('hidden');
                    modalUpdate.classList.add('flex');
                });
            });

            btnCloseUpdate.addEventListener('click', () => {
                modalUpdate.classList.add('hidden');
                modalUpdate.classList.remove('flex');
            });
        });
    </script>

@endsection
