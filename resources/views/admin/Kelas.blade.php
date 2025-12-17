@extends('layout.app')

@section('title', 'Manajemen Kelas')

@section('content')
<div class="container mx-auto px-4 py-10 space-y-10">

    {{-- SUCCESS NOTIFICATION --}}
    @if (session('success'))
        <div class="mb-6 rounded-lg bg-green-100 text-green-700 border border-green-300
        dark:bg-green-600/20 dark:text-green-300 dark:border-green-500/40
        px-5 py-3 text-sm shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white">
                Manajemen Kelas
            </h1>
            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1 max-w-xl">
                Kelola daftar kelas aktif di Paradise Gym dengan mudah.
            </p>
        </div>

        {{-- ADD BUTTON --}}
        <button id="btnOpenCreate"
            class="flex items-center justify-center gap-2 px-5 py-2.5 
            bg-indigo-600 hover:bg-indigo-500 text-white font-semibold 
            rounded-lg shadow-md transition-all focus:outline-none focus:ring-2 focus:ring-indigo-300">
            <i class="fa-solid fa-plus"></i> Tambah Kelas
        </button>
    </div>

    {{-- GRID KELAS --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($kelas as $k)
            <div
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 
                       rounded-xl shadow-md hover:shadow-lg transition-transform duration-200
                       transform hover:-translate-y-1 overflow-hidden flex flex-col">
                {{-- GAMBAR --}}
                @if ($k->gambar)
                    <div class="w-full h-44 overflow-hidden">
                        <img src="{{ asset($k->gambar) }}" alt="{{ $k->nama_kelas }}"
                             class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                    </div>
                @else
                    <div class="h-44 bg-gray-100 dark:bg-gray-700 flex items-center justify-center text-gray-500">
                        Tidak ada gambar
                    </div>
                @endif

                    {{-- INFO UTAMA --}}
                    <div class="p-5 flex flex-col flex-1">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">
                            {{ $k->nama_kelas }}
                        </h2>

                    <div class="text-gray-600 dark:text-gray-300 text-sm space-y-1">
                        <p><i class="fa-solid fa-users mr-2 text-indigo-500"></i> {{ $k->tipe_kelas }}</p>
                        <p><i class="fa-solid fa-money-bill mr-2 text-green-600"></i> Rp {{ number_format($k->harga, 0, ',', '.') }}</p>
                        <p><i class="fa-solid fa-percent mr-2 text-yellow-500"></i> Diskon: {{ $k->diskon_persen }}%</p>
                        <p><i class="fa-solid fa-tag mr-2 text-blue-500"></i>
                            <span class="font-medium text-gray-800 dark:text-gray-100">Rp {{ number_format($k->harga_diskon, 0, ',', '.') }}</span>
                        </p>
                        <p><i class="fa-solid fa-hourglass-end mr-2 text-red-500"></i> Expired: {{ $k->expired_at ? \Carbon\Carbon::parse($k->expired_at)->format('d-m-Y') : '-' }}</p>
                    </div>

                    <p class="text-gray-600 dark:text-gray-400 text-sm mt-3 line-clamp-3">
                        {{ $k->deskripsi }}
                    </p>

                    {{-- ACTIONS --}}
                    <div class="mt-5 flex gap-2">
                        <button
                            class="flex-1 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium btnOpenEdit transition focus:outline-none focus:ring-2 focus:ring-blue-300"
                            data-id="{{ $k->id }}" data-nama="{{ $k->nama_kelas }}"
                            data-tipe="{{ $k->tipe_kelas }}" data-harga="{{ $k->harga }}"
                            data-deskripsi="{{ $k->deskripsi }}" data-expired="{{ $k->expired_at?->format('Y-m-d') }}"
                            data-kapasitas="{{ $k->kapasitas }}">
                            <i class="fa-solid fa-pen-to-square mr-2"></i> Edit
                        </button>

                        <form action="{{ route('kelas.destroy', $k->id) }}" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm font-medium">
                                <i class="fa-solid fa-trash mr-2"></i> Hapus
                            </button>
                        </form>

                        <button
                            class="flex-1 px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm font-medium"
                            onclick="openQrModal('{{ $k->id }}','{{ $k->nama_kelas }}')">
                            <i class="fa-solid fa-qrcode mr-2"></i> QR
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16 border border-dashed border-gray-300 dark:border-gray-700 rounded-xl text-gray-600 dark:text-gray-400">
                <i class="fa-solid fa-folder-open text-4xl mb-3 opacity-60"></i>
                <p>Belum ada data kelas yang tersedia.</p>
            </div>
        @endforelse
    </div>
</div>

    {{-- MODAL QR --}}
    <div id="qrModal" class="fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
        <div
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6
                w-full max-w-sm text-center relative">
            <h2 id="qrTitle" class="text-xl font-bold mb-4"></h2>
            <div id="qrContainer" class="flex justify-center mb-4">
                <img id="qrImage" src="" alt="QR Code" class="w-48 h-48 mx-auto">
            </div>
            <button onclick="closeQrModal()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                Tutup
            </button>
        </div>
    </div>

    {{-- MODAL CREATE & EDIT --}}
    @foreach (['Create' => 'Tambah Kelas', 'Edit' => 'Edit Kelas'] as $modalId => $modalTitle)
        <div id="modal{{ $modalId }}"
            class="hidden fixed inset-0 z-50 items-center justify-center 
   bg-black/60 backdrop-blur-sm p-4">
            <div
                class="bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 
            rounded-2xl w-full max-w-lg border 
            border-gray-300 dark:border-gray-700 shadow-2xl 
            animate-fadeIn flex flex-col max-h-[90vh]">
                <div class="overflow-y-auto px-6 py-6 flex-1">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold">{{ $modalTitle }}</h2>
                        <button id="btnClose{{ $modalId }}"
                            class="text-gray-400 hover:text-black dark:hover:text-white transition">
                            <i class="fa-solid fa-xmark text-xl"></i>
                        </button>
                    </div>
                    <form id="form{{ $modalId }}" action="{{ $modalId === 'Create' ? route('kelas.store') : '' }}"
                        method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        @if ($modalId === 'Edit')
                            @method('PUT')
                        @endif
                        <input type="hidden" name="id" id="{{ strtolower($modalId) }}Id">

                        <div>
                            <label class="block mb-1 font-medium">Nama Kelas</label>
                            <input type="text" name="nama_kelas" id="{{ strtolower($modalId) }}Nama"
                                class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 
                           rounded-md px-3 py-2 text-sm focus:ring-indigo-500"
                                required>
                        </div>

                        <div>
                            <label class="block mb-1 font-medium">Tipe Kelas</label>
                            <select name="tipe_kelas" id="{{ strtolower($modalId) }}Tipe"
                                class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 
                           rounded-md px-3 py-2 text-sm">
                                @foreach (['Pilates Group', 'Pilates Private', 'Yoga Group', 'Yoga Private'] as $tipe)
                                    <option value="{{ $tipe }}">{{ $tipe }}</option>
                                @endforeach
                            </select>
                        </div>
                    @else
                        <div>
                            <label class="block mb-1 font-medium">Harga</label>
                            <input type="number" name="harga" id="{{ strtolower($modalId) }}Harga"
                                class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-sm">
                        </div>

                        <div>
                            <label class="block mb-1 font-medium">Kapasitas</label>
                            <input type="number" name="kapasitas" id="{{ strtolower($modalId) }}Kapasitas"
                                class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-sm"
                                min="1" required>
                        </div>

                        <div>
                            <label class="block mb-1 font-medium">Deskripsi</label>
                            <textarea name="deskripsi" id="{{ strtolower($modalId) }}Deskripsi" rows="3"
                                class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-sm"></textarea>
                        </div>

                        <div>
                            <label class="block mb-1 font-medium">Expired At</label>
                            <input type="date" name="expired_at" id="{{ strtolower($modalId) }}Expired"
                                class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-sm">
                        </div>

                        <div>
                            <label class="block mb-1 font-medium">Gambar</label>
                            <input type="file" name="gambar"
                                class="w-full bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-md px-3 py-2 text-sm"
                                accept="image/*">
                        </div>
                    </form>
                </div>

                <div class="px-6 py-4 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex justify-end gap-2">
                    <button id="btnClose{{ $modalId }}Bottom"
                        class="px-4 py-2 bg-gray-300 hover:bg-gray-200 
                   dark:bg-gray-700 dark:hover:bg-gray-600 
                   text-gray-800 dark:text-white rounded-md text-sm">
                        Batal
                    </button>
                    <button type="submit" form="form{{ $modalId }}"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 
                   text-white text-sm rounded-md">
                        {{ $modalId === 'Create' ? 'Simpan' : 'Update' }}
                    </button>
                </div>
            </div>
        </div>
    @endforeach

    <style>
        .animate-fadeIn {
            animation: fadeIn .25s ease-in-out;
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

    {{-- JS --}}
    @vite('resources/js/kelas.js')
    <script>
        function openQrModal(nama, url) {
            document.getElementById('qrTitle').innerText = nama;
            document.getElementById('qrImage').src = url;
            document.getElementById('qrModal').classList.remove('hidden');
        }

        function closeQrModal() {
            document.getElementById('qrModal').classList.add('hidden');
        }
    </script>
@endsection
