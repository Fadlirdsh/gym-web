@extends('layout.app')

@section('title', 'Manajemen Kelas')

@section('content')
    <div class="container mx-auto px-4 py-10 space-y-10">

        {{-- ✅ Pesan Sukses --}}
        @if (session('success'))
            <div class="mb-6 rounded-lg bg-green-600/20 border border-green-500/40 text-green-300 px-5 py-3 text-sm shadow">
                {{ session('success') }}
            </div>
        @endif

        {{-- ✅ Header --}}
        <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-white">Manajemen Kelas</h1>
                <p class="text-gray-400 text-sm mt-1">Kelola daftar kelas aktif di Paradise Gym dengan mudah dan cepat.</p>
            </div>
            <button id="btnOpenCreate"
                class="flex items-center justify-center gap-2 px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white font-semibold rounded-lg shadow-md hover:shadow-indigo-500/25 transition-all w-full sm:w-auto">
                <i class="fa-solid fa-plus"></i> Tambah Kelas
            </button>
        </div>

        {{-- ✅ Daftar Kelas --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($kelas as $k)
                <div
                    class="group bg-gray-800/80 border border-gray-700 rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-1 transition-all duration-200 overflow-hidden backdrop-blur-sm flex flex-col justify-between">
                    {{-- 🖼️ Gambar --}}
                    @if ($k->gambar)
                        <img src="{{ asset($k->gambar) }}" alt="{{ $k->nama_kelas }}"
                            class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="h-48 bg-gray-700 flex items-center justify-center text-gray-400 text-sm">
                            Tidak ada gambar
                        </div>
                    @endif

                    {{-- 📘 Info --}}
                    <div class="p-5 flex flex-col flex-1">
                        <h2 class="text-lg font-semibold text-white mb-2">{{ $k->nama_kelas }}</h2>

                        <div class="text-gray-400 text-sm space-y-1">
                            <p><i class="fa-solid fa-users mr-1 text-indigo-400"></i> {{ $k->tipe_kelas }}</p>
                            <p><i class="fa-solid fa-money-bill mr-1 text-green-400"></i> Rp
                                {{ number_format($k->harga, 0, ',', '.') }}</p>
                            <p><i class="fa-solid fa-percent mr-1 text-yellow-400"></i> Diskon: {{ $k->diskon_persen }}%</p>
                            <p><i class="fa-solid fa-tag mr-1 text-blue-400"></i> Harga Setelah Diskon:
                                <span class="font-medium text-gray-100">Rp
                                    {{ number_format($k->harga_diskon, 0, ',', '.') }}</span>
                            </p>
                            <p><i class="fa-solid fa-box mr-1 text-purple-400"></i> Paket: {{ $k->tipe_paket ?? 'General' }}
                            </p>
                            <p><i class="fa-solid fa-ticket mr-1 text-pink-400"></i> Token: {{ $k->jumlah_token ?? '-' }}
                            </p>
                            <p><i class="fa-solid fa-hourglass-end mr-1 text-red-400"></i> Expired:
                                {{ $k->expired_at ? \Carbon\Carbon::parse($k->expired_at)->format('d-m-Y H:i') : '-' }}
                            </p>
                        </div>

                        <p class="text-gray-400 text-sm mt-3 line-clamp-3">{{ $k->deskripsi }}</p>

                        {{-- 🎛️ Tombol Aksi --}}
                        <div class="flex flex-col sm:flex-row gap-2 mt-5">
                            <button
                                class="flex-1 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium btnOpenEdit transition"
                                data-id="{{ $k->id }}" data-nama="{{ $k->nama_kelas }}"
                                data-tipe="{{ $k->tipe_kelas }}" data-harga="{{ $k->harga }}" data-paket="General"
                                data-deskripsi="{{ $k->deskripsi }}" data-token="{{ $k->jumlah_token }}"
                                data-expired="{{ $k->expired_at ? \Carbon\Carbon::parse($k->expired_at)->format('Y-m-d\TH:i') : '' }}"
                                data-kapasitas="{{ $k->kapasitas }}">
                                <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
                            </button>

                            <form action="{{ route('kelas.destroy', ['kelas' => $k->id]) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus kelas ini?')" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm font-medium transition">
                                    <i class="fa-solid fa-trash-can mr-1"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16 border border-dashed border-gray-700 rounded-xl text-gray-400">
                    <i class="fa-solid fa-folder-open text-4xl mb-3 text-gray-500"></i>
                    <p>Belum ada data kelas yang tersedia.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ✅ Modal Create & Edit --}}
    @foreach (['Create' => 'Tambah Kelas', 'Edit' => 'Edit Kelas'] as $modalId => $modalTitle)
        <div id="modal{{ $modalId }}"
            class="hidden fixed inset-0 z-50 items-center justify-center bg-black/70 backdrop-blur-sm p-4">
            <div
                class="bg-gray-800 text-gray-200 rounded-2xl w-full max-w-lg border border-gray-700 shadow-2xl animate-fadeIn flex flex-col max-h-[90vh]">
                <div class="overflow-y-auto px-6 py-6 flex-1 space-y-4">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold text-white">{{ $modalTitle }}</h2>
                        <button type="button" id="btnClose{{ $modalId }}"
                            class="text-gray-400 hover:text-white transition">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>

                    <form action="{{ $modalId === 'Create' ? route('kelas.store') : '' }}" method="POST"
                        enctype="multipart/form-data" id="form{{ $modalId }}" class="space-y-4">
                        @csrf
                        @if ($modalId === 'Edit')
                            @method('PUT')
                        @endif

                        {{-- Nama Kelas --}}
                        <div>
                            <label class="block mb-1 font-medium">Nama Kelas</label>
                            <input type="text" name="nama_kelas" id="{{ strtolower($modalId) }}Nama"
                                class="w-full bg-gray-900 border border-gray-700 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                                required>
                        </div>

                        {{-- Tipe Kelas --}}
                        <div>
                            <label class="block mb-1 font-medium">Tipe Kelas</label>
                            <select name="tipe_kelas" id="{{ strtolower($modalId) }}Tipe"
                                class="w-full bg-gray-900 border border-gray-700 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                                @foreach (['Pilates Group', 'Pilates Private', 'Yoga Group', 'Yoga Private'] as $tipe)
                                    <option value="{{ $tipe }}">{{ $tipe }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Harga --}}
                        <div>
                            <label class="block mb-1 font-medium">Harga</label>
                            <input type="number" name="harga" id="{{ strtolower($modalId) }}Harga"
                                class="w-full bg-gray-900 border border-gray-700 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>

                        {{-- Kapasitas --}}
                        <div>
                            <label class="block mb-1 font-medium">Kapasitas</label>
                            <input type="number" name="kapasitas" id="{{ strtolower($modalId) }}Kapasitas"
                                class="w-full bg-gray-900 border border-gray-700 rounded-md px-3 py-2 text-sm"
                                value="20" min="1" required>
                        </div>

                        {{-- Deskripsi --}}
                        <div>
                            <label class="block mb-1 font-medium">Deskripsi</label>
                            <textarea name="deskripsi" id="{{ strtolower($modalId) }}Deskripsi"
                                class="w-full bg-gray-900 border border-gray-700 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none"
                                rows="3"></textarea>
                        </div>

                        {{-- Expired At --}}
                        <div>
                            <label class="block mb-1 font-medium">Expired At</label>
                            <input type="datetime-local" name="expired_at" id="{{ strtolower($modalId) }}Expired"
                                class="w-full bg-gray-900 border border-gray-700 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>

                        {{-- Gambar --}}
                        <div>
                            <label class="block mb-1 font-medium">Gambar</label>
                            <input type="file" name="gambar"
                                class="w-full bg-gray-900 border border-gray-700 rounded-md px-3 py-2 text-sm"
                                accept="image/*">
                            <div id="preview{{ $modalId }}Gambar" class="mt-3"></div>
                        </div>

                        <input type="hidden" name="tipe_paket" value="General">
                    </form>
                </div>

                <div class="px-6 py-4 border-t border-gray-700 flex justify-end gap-2 bg-gray-800">
                    <button type="button" id="btnClose{{ $modalId }}Bottom"
                        class="px-4 py-2 bg-gray-700 text-white text-sm rounded-md hover:bg-gray-600 transition">
                        Batal
                    </button>
                    <button type="submit" form="form{{ $modalId }}"
                        class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 transition">
                        {{ $modalId === 'Create' ? 'Simpan' : 'Update' }}
                    </button>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Animasi Fade --}}
    <style>
        .animate-fadeIn {
            animation: fadeIn 0.25s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(.96);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>

    <script>
        window.updateRouteTemplate = "{{ url('admin/users/kelas') }}/:id";
    </script>

    @vite('resources/js/kelas.js')
@endsection
