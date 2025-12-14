@extends('layout.app')

@section('title', 'Manajemen Kelas')

@section('content')
    <div class="container mx-auto px-4 py-10 space-y-10">

        {{-- SUCCESS NOTIFICATION --}}
        @if (session('success'))
            <div
                class="mb-6 rounded-lg bg-green-100 text-green-700 border border-green-300
            dark:bg-green-600/20 dark:text-green-300 dark:border-green-500/40
            px-5 py-3 text-sm shadow">
                {{ session('success') }}
            </div>
        @endif

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-800 dark:text-white">
                    Manajemen Kelas
                </h1>
                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                    Kelola daftar kelas aktif di Paradise Gym dengan mudah.
                </p>
            </div>

            {{-- ADD BUTTON --}}
            <button id="btnOpenCreate"
                class="flex items-center justify-center gap-2 px-5 py-2.5 
               bg-indigo-600 hover:bg-indigo-500 text-white font-semibold 
               rounded-lg shadow-md transition">
                <i class="fa-solid fa-plus"></i> Tambah Kelas
            </button>
        </div>

        {{-- DAFTAR KELAS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($kelas as $k)
                <div
                    class="group bg-white dark:bg-gray-800/80 
                   border border-gray-300 dark:border-gray-700 
                   rounded-2xl shadow-lg hover:shadow-xl 
                   hover:-translate-y-1 transition-all duration-200 
                   overflow-hidden backdrop-blur-sm flex flex-col">

                    {{-- GAMBAR --}}
                    @if ($k->gambar)
                        <img src="{{ asset($k->gambar) }}"
                            class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div
                            class="h-48 bg-gray-200 dark:bg-gray-700 text-gray-500 dark:text-gray-400 
                            flex items-center justify-center text-sm">
                            Tidak ada gambar
                        </div>
                    @endif

                    {{-- INFO UTAMA --}}
                    <div class="p-5 flex flex-col flex-1">
                        <h2 class="text-lg font-semibold text-gray-800 dark:text-black mb-2">
                            {{ $k->nama_kelas }}
                        </h2>

                        <div class="text-gray-600 dark:text-gray-400 text-sm space-y-1">
                            <p><i class="fa-solid fa-users mr-1 text-indigo-500"></i> {{ $k->tipe_kelas }}</p>
                            <p><i class="fa-solid fa-money-bill mr-1 text-green-600"></i>
                                Rp {{ number_format($k->harga, 0, ',', '.') }}
                            </p>
                            <p><i class="fa-solid fa-percent mr-1 text-yellow-500"></i>
                                Diskon: {{ $k->diskon_persen }}%
                            </p>
                            <p><i class="fa-solid fa-tag mr-1 text-blue-500"></i>
                                Harga Setelah Diskon:
                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                    Rp {{ number_format($k->harga_diskon, 0, ',', '.') }}
                                </span>
                            </p>
                            <p><i class="fa-solid fa-hourglass-end mr-1 text-red-500"></i>
                                Expired: {{ $k->expired_at ? \Carbon\Carbon::parse($k->expired_at)->format('d-m-Y') : '-' }}
                            </p>
                        </div>

                        <p class="text-gray-600 dark:text-gray-400 text-sm mt-3 line-clamp-3">
                            {{ $k->deskripsi }}
                        </p>

                        {{-- AKSI --}}
                        <div class="flex flex-col sm:flex-row gap-2 mt-5">
                            {{-- EDIT BUTTON --}}
                            <button
                                class="flex-1 px-3 py-2 bg-blue-600 hover:bg-blue-700 
                               text-white rounded-md text-sm font-medium btnOpenEdit transition"
                                data-id="{{ $k->id }}" data-nama="{{ $k->nama_kelas }}"
                                data-tipe="{{ $k->tipe_kelas }}" data-harga="{{ $k->harga }}"
                                data-deskripsi="{{ $k->deskripsi }}" data-expired="{{ $k->expired_at?->format('Y-m-d') }}"
                                data-kapasitas="{{ $k->kapasitas }}">
                                <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
                            </button>

                            {{-- DELETE --}}
                            <form action="{{ route('kelas.destroy', $k->id) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus kelas ini?')" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full px-3 py-2 bg-red-600 hover:bg-red-700 
                                   text-white rounded-md text-sm font-medium">
                                    <i class="fa-solid fa-trash mr-1"></i> Hapus
                                </button>
                            </form>

                            {{-- QR --}}
                            <button
                                class="flex-1 px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md text-sm font-medium"
                                onclick="openQrModal('{{ $k->id }}','{{ $k->nama_kelas }}')">
                                <i class="fa-solid fa-qrcode mr-1"></i> QR
                            </button>

                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="col-span-full text-center py-16 
                    border border-dashed border-gray-400 dark:border-gray-700 
                    rounded-xl text-gray-600 dark:text-gray-400">
                    <i class="fa-solid fa-folder-open text-4xl mb-3 opacity-60"></i>
                    <p>Belum ada data kelas yang tersedia.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- MODAL QR --}}
    <div id="qrModal"
        class="hidden fixed inset-0 z-50  items-center justify-center
            bg-black/60 backdrop-blur-sm p-4">

        <div
            class="relative w-full max-w-sm bg-white
               rounded-2xl shadow-2xl border border-gray-200
               text-center overflow-hidden">

            {{-- HEADER --}}
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 id="qrTitle" class="text-lg font-semibold text-gray-800">
                    QR Kelas
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Tunjukkan QR ini kepada pelanggan
                </p>
            </div>

            {{-- BODY --}}
            <div class="px-6 py-6">
                <div id="qrContainer" class="flex justify-center">
                    <img id="qrImage" src="" alt="QR Code" class="w-48 h-48 rounded-lg border border-gray-200">
                </div>
            </div>

            {{-- FOOTER --}}
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-center">
                <button onclick="closeQrModal()"
                    class="px-6 py-2 rounded-lg text-sm font-medium
                       bg-gray-800 hover:bg-gray-700
                       text-white transition">
                    Tutup
                </button>
            </div>

        </div>
    </div>


    {{-- MODAL CREATE & EDIT --}}
    @foreach (['Create' => 'Tambah Kelas', 'Edit' => 'Edit Kelas'] as $modalId => $modalTitle)
        <div id="modal{{ $modalId }}"
            class="hidden fixed inset-0 z-50 items-center justify-center
            bg-black/50 backdrop-blur-sm p-4">

            <div
                class="w-full max-w-xl bg-white text-gray-800
                rounded-2xl shadow-2xl border border-gray-200
                flex flex-col max-h-[90vh]">

                {{-- HEADER --}}
                <div class="px-6 py-4 border-b border-gray-200 bg-white">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-semibold tracking-tight">
                            {{ $modalTitle }}
                        </h2>
                        <button id="btnClose{{ $modalId }}"
                            class="w-9 h-9 rounded-full flex items-center justify-center
                               text-gray-500 hover:text-red-500
                               hover:bg-red-100 transition">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                </div>

                {{-- BODY --}}
                <form id="form{{ $modalId }}" action="{{ $modalId === 'Create' ? route('kelas.store') : '' }}"
                    method="POST" enctype="multipart/form-data"
                    class="flex-1 overflow-y-auto px-6 py-6 space-y-5 bg-white">
                    @csrf
                    
                    @if ($modalId === 'Edit')
                        @method('PUT')
                    @endif

                    <input type="hidden" name="id" id="{{ strtolower($modalId) }}Id">

                    @php
                        $inputClass = 'w-full rounded-lg border border-gray-300
                               bg-white text-gray-800
                               px-4 py-2.5 text-sm
                               placeholder-gray-400
                               focus:outline-none focus:ring-2
                               focus:ring-indigo-500 focus:border-indigo-500
                               transition';
                    @endphp

                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">
                            Nama Kelas
                        </label>
                        <input type="text" name="nama_kelas" id="{{ strtolower($modalId) }}Nama"
                            class="{{ $inputClass }}" placeholder="Contoh: Pilates Beginner" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">
                                Tipe Kelas
                            </label>
                            <select name="tipe_kelas" id="{{ strtolower($modalId) }}Tipe" class="{{ $inputClass }}">
                                @foreach (['Pilates Group', 'Pilates Private', 'Yoga Group', 'Yoga Private'] as $tipe)
                                    <option value="{{ $tipe }}">{{ $tipe }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">
                                Harga
                            </label>
                            <input type="number" name="harga" id="{{ strtolower($modalId) }}Harga"
                                class="{{ $inputClass }}" placeholder="150000">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">
                                Kapasitas
                            </label>
                            <input type="number" name="kapasitas" min="1"
                                id="{{ strtolower($modalId) }}Kapasitas" class="{{ $inputClass }}" required>
                        </div>

                        <div>
                            <label class="block mb-1 text-sm font-medium text-gray-700">
                                Expired
                            </label>
                            <input type="date" name="expired_at" id="{{ strtolower($modalId) }}Expired"
                                class="{{ $inputClass }}">
                        </div>
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">
                            Deskripsi
                        </label>
                        <textarea name="deskripsi" rows="3" id="{{ strtolower($modalId) }}Deskripsi" class="{{ $inputClass }}"
                            placeholder="Deskripsi singkat kelas..."></textarea>
                    </div>

                    <div>
                        <label class="block mb-1 text-sm font-medium text-gray-700">
                            Gambar
                        </label>
                        <input type="file" name="gambar" accept="image/*"
                            class="block w-full text-sm text-gray-600
                              file:mr-4 file:py-2 file:px-4
                              file:rounded-lg file:border-0
                              file:text-sm file:font-medium
                              file:bg-gray-100 file:text-gray-700
                              hover:file:bg-gray-200 transition">
                    </div>
                </form>

                {{-- FOOTER --}}
                <div class="px-6 py-4 border-t border-gray-200
                    bg-gray-50 flex justify-end gap-3">
                    <button id="btnClose{{ $modalId }}Bottom"
                        class="px-4 py-2 rounded-lg text-sm
                           bg-white border border-gray-300
                           hover:bg-gray-100 transition">
                        Batal
                    </button>
                    <button type="submit" form="form{{ $modalId }}"
                        class="px-5 py-2 rounded-lg text-sm font-medium
                           bg-indigo-600 hover:bg-indigo-500
                           text-white shadow-md transition">
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


<script>
    const updateRouteTemplate = "{{ route('kelas.update', ':id') }}";
</script>

    {{-- JS --}}
    @vite('resources/js/kelas.js')

@endsection
