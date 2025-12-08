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
=======
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
>>>>>>> Stashed changes

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
