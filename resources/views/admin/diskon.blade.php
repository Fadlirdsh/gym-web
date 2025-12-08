@extends('layout.app')

@section('title', 'Manajemen Diskon')

@section('content')

{{-- FIX COLOR MODE --}}
<style>
/* ====================== */
/* MAIN PAGE BACKGROUND   */
/* ====================== */
.page-bg {
    background: #f8fafc; /* Light */
    color: #1e293b;
}
.dark .page-bg {
    background: #0f172a; /* Dark */
    color: #e5e7eb;
}

/* === FIX JUDUL TIDAK TERLIHAT === */
.page-title {
    color: #0f172a !important; /* Light mode */
}
.dark .page-title {
    color: #f8fafc !important; /* Dark mode */
}

/* ====================== */
/* CARD / WRAPPER TABLE   */
/* ====================== */
.card-box {
    background: #ffffff; /* Light */
    border: 1px solid #d1d5db;
}
.dark .card-box {
    background: rgba(31,41,55,0.8); /* Dark */
    border-color: #4b5563;
}

/* ====================== */
/* TABLE HEADER           */
/* ====================== */
.table-header {
    background: #e2e8f0;       /* Light */
    color: #1e293b;
}
.dark .table-header {
    background: rgba(55,65,81,0.7);
    color: #f1f5f9;
}

/* ====================== */
/* TABLE ROW              */
/* ====================== */
.table-row:hover {
    background: #f1f5f9; /* Light hover */
}
.dark .table-row:hover {
    background: rgba(55,65,81,0.35); /* Dark hover */
}

/* ====================== */
/* BADGE BLUE             */
/* ====================== */
.badge-blue {
    background: rgba(59,130,246,0.25);
    color: #1e3a8a;
    border: 1px solid rgba(59,130,246,0.6);
}
.dark .badge-blue {
    background: rgba(59,130,246,0.3);
    color: #bfdbfe;
    border-color: #60a5fa;
}

/* ====================== */
/* INPUT FIELD            */
/* ====================== */
.input-box {
    background: #ffffff;
    color: #1e293b;
    border: 1px solid #d1d5db;
}
.input-box:focus {
    border-color: #3b82f6;
    outline: none;
}

.dark .input-box {
    background: #111827;
    color: #f3f4f6;
    border-color: #4b5563;
}
.dark .input-box:focus {
    border-color: #93c5fd;
}

/* ====================== */
/* MODAL BACKGROUND       */
/* ====================== */
.modal-bg {
    background: #ffffff;
    border: 1px solid #d1d5db;
}
.dark .modal-bg {
    background: rgba(31,41,55,0.95);
    border-color: #4b5563;
}

/* ====================== */
/* GENERAL BUTTON FIX     */
/* ====================== */
button {
    font-weight: 600;
}
</style>


<div class="min-h-screen page-bg px-6 py-8">

    <div class="container mx-auto px-6 py-8">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row justify-between items-center mb-8">
            <h1 class="page-title text-3xl font-bold tracking-wide">
                💰 Manajemen Diskon
            </h1>

            <button id="btnOpenCreate"
                class="mt-3 sm:mt-0 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-semibold shadow-lg transition flex items-center gap-2">
                ➕ Tambah Diskon
            </button>
        </div>

        {{-- TABEL DISKON --}}
        <div class="card-box rounded-2xl shadow-xl backdrop-blur-md overflow-hidden">
            <table class="w-full text-sm text-gray-800 dark:text-gray-200">
                <thead class="table-header uppercase text-xs tracking-wider">
                    <tr class="text-center">
                        <th class="px-3 py-3">#</th>
                        <th class="px-3 py-3">Kelas</th>
                        <th class="px-3 py-3">Nama Diskon</th>
                        <th class="px-3 py-3">Persentase</th>
                        <th class="px-3 py-3">Mulai</th>
                        <th class="px-3 py-3">Berakhir</th>
                        <th class="px-3 py-3">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-300 dark:divide-gray-700">
                    @forelse($diskons as $diskon)
                        <tr class="text-center table-row transition">
                            <td class="px-3 py-2">{{ $diskon->id }}</td>
                            <td class="px-3 py-2 font-medium">{{ $diskon->kelas->nama_kelas ?? '-' }}</td>
                            <td class="px-3 py-2">{{ $diskon->nama_diskon }}</td>

                            <td class="px-3 py-2">
                                <span class="badge-blue px-3 py-1 rounded-full text-xs font-semibold">
                                    {{ $diskon->persentase }}%
                                </span>
                            </td>

                            <td class="px-3 py-2">
                                {{ \Carbon\Carbon::parse($diskon->tanggal_mulai)->format('d M Y') }}
                            </td>

                            <td class="px-3 py-2">
                                {{ \Carbon\Carbon::parse($diskon->tanggal_berakhir)->format('d M Y') }}
                            </td>

                            <td class="px-3 py-2 space-x-2">
                                {{-- EDIT --}}
                                <button class="btnOpenEdit bg-yellow-500 hover:bg-yellow-600 text-gray-900 font-semibold px-3 py-1 rounded-lg shadow transition"
                                    data-id="{{ $diskon->id }}"
                                    data-kelas_id="{{ $diskon->kelas_id }}"
                                    data-nama_diskon="{{ $diskon->nama_diskon }}"
                                    data-persentase="{{ $diskon->persentase }}"
                                    data-tanggal_mulai="{{ $diskon->tanggal_mulai }}"
                                    data-tanggal_berakhir="{{ $diskon->tanggal_berakhir }}">
                                    ✏️ Edit
                                </button>

                                {{-- DELETE --}}
                                <form action="{{ route('diskon.destroy', $diskon->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg shadow transition"
                                        onclick="return confirm('Yakin ingin menghapus diskon ini?')">
                                        🗑️ Hapus
                                    </button>
                                </form>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-6 text-gray-500 dark:text-gray-400">
                                🚫 Belum ada data diskon
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- MODAL CREATE --}}
        <div id="modalCreate" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden items-center justify-center">
            <div class="modal-bg rounded-2xl shadow-2xl w-96 p-6">
                <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Tambah Diskon</h2>

                <form action="{{ route('diskon.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm mb-1 text-gray-800 dark:text-gray-300">Pilih Kelas</label>
                        <select name="kelas_id" class="w-full input-box rounded-lg px-3 py-2">
                            @foreach ($kelas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm mb-1 text-gray-800 dark:text-gray-300">Nama Diskon</label>
                        <input type="text" name="nama_diskon" class="w-full input-box rounded-lg px-3 py-2" required>
                    </div>

                    <div>
                        <label class="block text-sm mb-1 text-gray-800 dark:text-gray-300">Persentase (%)</label>
                        <input type="number" name="persentase" class="w-full input-box rounded-lg px-3 py-2" min="1" max="100" required>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm mb-1 text-gray-800 dark:text-gray-300">Mulai</label>
                            <input type="date" name="tanggal_mulai" class="w-full input-box rounded-lg px-3 py-2" required>
                        </div>

                        <div>
                            <label class="block text-sm mb-1 text-gray-800 dark:text-gray-300">Berakhir</label>
                            <input type="date" name="tanggal_berakhir" class="w-full input-box rounded-lg px-3 py-2" required>
                        </div>
                    </div>

                    <div class="flex justify-between mt-5">
                        <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold">
                            💾 Simpan
                        </button>
                        <button type="button" id="btnCloseCreate"
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-semibold">
                            ❌ Batal
                        </button>
                    </div>

                </form>
            </div>
        </div>

        {{-- MODAL EDIT --}}
        <div id="modalEdit" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden items-center justify-center">
            <div class="modal-bg rounded-2xl shadow-2xl w-96 p-6">
                <h2 class="text-2xl font-bold mb-4 text-gray-900 dark:text-gray-100">Edit Diskon</h2>

                <form id="formEditDiskon" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" id="editId">

                    <div>
                        <label class="block text-sm mb-1 text-gray-800 dark:text-gray-300">Pilih Kelas</label>
                        <select name="kelas_id" id="editKelasId" class="w-full input-box rounded-lg px-3 py-2">
                            @foreach ($kelas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm mb-1 text-gray-800 dark:text-gray-300">Nama Diskon</label>
                        <input type="text" id="editNamaDiskon" name="nama_diskon" class="w-full input-box rounded-lg px-3 py-2" required>
                    </div>

                    <div>
                        <label class="block text-sm mb-1 text-gray-800 dark:text-gray-300">Persentase (%)</label>
                        <input type="number" id="editPersentase" name="persentase" class="w-full input-box rounded-lg px-3 py-2" min="1" max="100" required>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm mb-1 text-gray-800 dark:text-gray-300">Mulai</label>
                            <input type="date" id="editTanggalMulai" name="tanggal_mulai" class="w-full input-box rounded-lg px-3 py-2" required>
                        </div>

                        <div>
                            <label class="block text-sm mb-1 text-gray-800 dark:text-gray-300">Berakhir</label>
                            <input type="date" id="editTanggalBerakhir" name="tanggal_berakhir" class="w-full input-box rounded-lg px-3 py-2" required>
                        </div>
                    </div>

                    <div class="flex justify-between mt-5">
                        <button type="submit"
                            class="bg-yellow-500 hover:bg-yellow-600 text-gray-900 px-4 py-2 rounded-lg font-semibold">
                            🔄 Update
                        </button>
                        <button type="button" id="btnCloseEdit"
                            class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-semibold">
                            ❌ Batal
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>

</div>

@vite('resources/js/diskon.js')

@endsection
