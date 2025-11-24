@extends('layout.app')

@section('title', 'Manajemen Diskon')

@section('content')
<div class="min-h-screen bg-gray-900 text-gray-100 px-6 py-8">

    <div class="container mx-auto px-6 py-8 space-y-10" x-data="{ showDiskonModal: false }">


    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-white tracking-wide">
            💰 Manajemen Diskon
        </h1>

        <button id="btnOpenCreate"
            class="mt-3 sm:mt-0 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-semibold shadow-lg transition flex items-center gap-2">
            ➕ Tambah Diskon
        </button>
    </div>

    {{-- TABEL DISKON --}}
    <div class="bg-gray-800/60 border border-gray-700 rounded-2xl shadow-xl backdrop-blur-md overflow-hidden">
        <table class="w-full text-sm text-gray-200">
            <thead class="bg-gray-700 text-gray-100 uppercase text-xs tracking-wider">
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
            <tbody>
                @forelse ($diskons as $diskon)
                    <tr class="hover:bg-gray-50">
                        <td class="border px-4 py-2">{{ $diskon->nama_diskon }}</td>
                        <td class="border px-4 py-2">{{ $diskon->persentase }}%</td>
                        <td class="border px-4 py-2">{{ $diskon->kelas->nama_kelas ?? '-' }}</td>
                        <td class="border px-4 py-2">{{ $diskon->tanggal_mulai }}</td>
                        <td class="border px-4 py-2">{{ $diskon->tanggal_berakhir }}</td>
                        <td class="border px-4 py-2 text-center">
                            <a href="#" class="text-blue-500 hover:underline">Edit</a> |
                            <a href="#" class="text-red-500 hover:underline">Hapus</a>

<div class="min-h-screen bg-gray-900 text-gray-100 px-6 py-8">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-white tracking-wide">
            💰 Manajemen Diskon
        </h1>

        <button id="btnOpenCreate" 
            class="mt-3 sm:mt-0 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-semibold shadow-lg transition flex items-center gap-2">
            ➕ Tambah Diskon
        </button>
    </div>

    {{-- TABEL DISKON --}}
    <div class="bg-gray-800/60 border border-gray-700 rounded-2xl shadow-xl backdrop-blur-md overflow-hidden">
        <table class="w-full text-sm text-gray-200">
            <thead class="bg-gray-700 text-gray-100 uppercase text-xs tracking-wider">
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

            <tbody class="divide-y divide-gray-700">
                @forelse($diskons as $diskon)
                    <tr class="text-center hover:bg-gray-700/40 transition">
                        <td class="px-3 py-2">{{ $diskon->id }}</td>
                        <td class="px-3 py-2 font-medium">{{ $diskon->kelas->nama_kelas ?? '-' }}</td>
                        <td class="px-3 py-2">{{ $diskon->nama_diskon }}</td>
                        <td class="px-3 py-2">
                            <span class="inline-block bg-blue-700/30 text-blue-400 border border-blue-600 px-3 py-1 rounded-full text-xs font-semibold">
                                {{ $diskon->persentase }}%
                            </span>
                        </td>
                        <td class="px-3 py-2">{{ \Carbon\Carbon::parse($diskon->tanggal_mulai)->format('d M Y') }}</td>
                        <td class="px-3 py-2">{{ \Carbon\Carbon::parse($diskon->tanggal_berakhir)->format('d M Y') }}</td>
                        <td class="px-3 py-2 space-x-2">
                            <button 
                                class="btnOpenEdit bg-yellow-500 hover:bg-yellow-600 text-gray-900 font-semibold px-3 py-1 rounded-lg shadow transition"
                                data-id="{{ $diskon->id }}"
                                data-kelas_id="{{ $diskon->kelas_id }}"
                                data-nama_diskon="{{ $diskon->nama_diskon }}"
                                data-persentase="{{ $diskon->persentase }}"
                                data-tanggal_mulai="{{ $diskon->tanggal_mulai }}"
                                data-tanggal_berakhir="{{ $diskon->tanggal_berakhir }}">
                                ✏️ Edit
                            </button>

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
                        <td colspan="6" class="text-center border py-3 text-gray-500">Belum ada data diskon</td>
                        <td colspan="7" class="text-center py-6 text-gray-400">
                            🚫 Belum ada data diskon yang tersedia
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- MODAL TAMBAH DISKON --}}
    <div id="modalCreate" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-gray-800 text-gray-100 rounded-2xl shadow-2xl w-96 p-6 border border-gray-700">
            <h2 class="text-2xl font-bold mb-4">Tambah Diskon</h2>

            <form action="{{ route('diskon.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm mb-1">Pilih Kelas</label>
                    <select name="kelas_id" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm mb-1">Nama Diskon</label>
                    <input type="text" name="nama_diskon"
                        class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>

                <div>
                    <label class="block text-sm mb-1">Persentase (%)</label>
                    <input type="number" name="persentase" min="1" max="100"
                        class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm mb-1">Mulai</label>
                        <input type="date" name="tanggal_mulai"
                            class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Berakhir</label>
                        <input type="date" name="tanggal_berakhir"
                            class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none" required>
                    </div>
                </div>

                <div class="flex justify-between mt-5">
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                        💾 Simpan
                    </button>
                    <button type="button" id="btnCloseCreate"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                        ❌ Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL EDIT DISKON --}}
    </div>

    {{-- ===================== --}}
    {{-- MODAL TAMBAH DISKON --}}
    {{-- ===================== --}}
    <div id="modalCreate" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-gray-800 text-gray-100 rounded-2xl shadow-2xl w-96 p-6 border border-gray-700">
            <h2 class="text-2xl font-bold mb-4">Tambah Diskon</h2>

            <form action="{{ route('diskon.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm mb-1">Pilih Kelas</label>
                    <select name="kelas_id" class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none">
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm mb-1">Nama Diskon</label>
                    <input type="text" name="nama_diskon"
                        class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>

                <div>
                    <label class="block text-sm mb-1">Persentase (%)</label>
                    <input type="number" name="persentase" min="1" max="100"
                        class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm mb-1">Mulai</label>
                        <input type="date" name="tanggal_mulai"
                            class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Berakhir</label>
                        <input type="date" name="tanggal_berakhir"
                            class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 outline-none" required>
                    </div>
                </div>

                <div class="flex justify-between mt-5">
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                        💾 Simpan
                    </button>
                    <button type="button" id="btnCloseCreate"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                        ❌ Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ===================== --}}
    {{-- MODAL EDIT DISKON --}}
    {{-- ===================== --}}

    <div id="modalEdit" class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm flex items-center justify-center z-50">
        <div class="bg-gray-800 text-gray-100 rounded-2xl shadow-2xl w-96 p-6 border border-gray-700">
            <h2 class="text-2xl font-bold mb-4">Edit Diskon</h2>

            <form id="formEditDiskon" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="editId">

                <div>
                    <label class="block text-sm mb-1">Pilih Kelas</label>
                    <select name="kelas_id" id="editKelasId"
                        class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 focus:ring-2 focus:ring-yellow-500 outline-none">
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm mb-1">Nama Diskon</label>
                    <input type="text" name="nama_diskon" id="editNamaDiskon"
                        class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 focus:ring-2 focus:ring-yellow-500 outline-none" required>
                </div>

                <div>
                    <label class="block text-sm mb-1">Persentase (%)</label>
                    <input type="number" name="persentase" id="editPersentase" min="1" max="100"
                        class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 focus:ring-2 focus:ring-yellow-500 outline-none" required>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm mb-1">Mulai</label>
                        <input type="date" name="tanggal_mulai" id="editTanggalMulai"
                            class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 focus:ring-2 focus:ring-yellow-500 outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm mb-1">Berakhir</label>
                        <input type="date" name="tanggal_berakhir" id="editTanggalBerakhir"
                            class="w-full bg-gray-900 border border-gray-700 rounded-lg px-3 py-2 focus:ring-2 focus:ring-yellow-500 outline-none" required>
                    </div>
                </div>

                <div class="flex justify-between mt-5">
                    <button type="submit"
                        class="bg-yellow-500 hover:bg-yellow-600 text-gray-900 px-4 py-2 rounded-lg font-semibold transition">
                        🔄 Update
                    </button>
                    <button type="button" id="btnCloseEdit"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-semibold transition">
                        ❌ Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@vite('resources/js/diskon.js')

@endsection

@endsection
