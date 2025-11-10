@extends('layout.app')

@section('title', 'Manajemen Diskon')

@section('content')
<div class="container mx-auto px-6 py-8 space-y-10" x-data="{ showDiskonModal: false }">

    {{-- ========================= --}}
    {{-- 🔹 Header + Tombol Tambah --}}
    {{-- ========================= --}}
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-semibold">Daftar Diskon</h2>
        <button 
            @click="showDiskonModal = true" 
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
            + Tambah Diskon
        </button>
    </div>

    {{-- ========================= --}}
    {{-- 🔹 Tabel Diskon --}}
    {{-- ========================= --}}
    <table class="min-w-full border border-gray-300 text-black">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-4 py-2">Nama Diskon</th>
                <th class="border px-4 py-2">Persentase</th>
                <th class="border px-4 py-2">Kelas</th>
                <th class="border px-4 py-2">Tanggal Mulai</th>
                <th class="border px-4 py-2">Tanggal Berakhir</th>
                <th class="border px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($diskons as $diskon)
                <tr class="hover:bg-gray-50">
                    <td class="border px-4 py-2">{{ $diskon->nama_diskon }}</td>
                    <td class="border px-4 py-2">{{ $diskon->persentase }}%</td>
                    <td class="border px-4 py-2">{{ $diskon->kelas->nama ?? '-' }}</td>
                    <td class="border px-4 py-2">{{ $diskon->tanggal_mulai }}</td>
                    <td class="border px-4 py-2">{{ $diskon->tanggal_berakhir }}</td>
                    <td class="border px-4 py-2 text-center">
                        <a href="#" class="text-blue-500 hover:underline">Edit</a> |
                        <a href="#" class="text-red-500 hover:underline">Hapus</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center border py-3 text-gray-500">Belum ada data diskon</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ==================================================== --}}
    {{-- 🔹 Modal Tambah Diskon --}}
    {{-- ==================================================== --}}
    <div 
        x-show="showDiskonModal" 
        x-transition 
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
    >
        <div class="bg-white text-black rounded-lg w-full max-w-md p-6 relative">
            <h3 class="text-lg font-semibold mb-4">Tambah Diskon</h3>
            <form action="{{ route('diskon.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="block text-sm font-medium">Nama Diskon</label>
                    <input type="text" name="nama_diskon" required 
                        class="w-full border rounded px-3 py-2 text-black focus:ring focus:ring-blue-200">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium">Persentase (%)</label>
                    <input type="number" name="persentase" min="1" max="100" required 
                        class="w-full border rounded px-3 py-2 text-black focus:ring focus:ring-blue-200">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium">Kelas</label>
                    <select name="kelas_id" required class="w-full border rounded px-3 py-2 text-black">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach ($kelas as $k)
                            <option value="{{ $k->id }}">{{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" required 
                        class="w-full border rounded px-3 py-2 text-black focus:ring focus:ring-blue-200">
                </div>

                <div class="mb-3">
                    <label class="block text-sm font-medium">Tanggal Berakhir</label>
                    <input type="date" name="tanggal_berakhir" required 
                        class="w-full border rounded px-3 py-2 text-black focus:ring focus:ring-blue-200">
                </div>

                <div class="flex justify-end gap-3 mt-4">
                    <button type="button" 
                        @click="showDiskonModal = false" 
                        class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit" 
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
