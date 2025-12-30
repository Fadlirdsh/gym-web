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

                    {{-- CONTENT --}}
                    <div class="p-5 flex flex-col flex-1">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">
                            {{ $k->nama_kelas }}
                        </h2>

                        <div class="text-gray-600 dark:text-gray-300 text-sm space-y-1">
                            <p><i class="fa-solid fa-users mr-2 text-indigo-500"></i> {{ $k->tipe_kelas }}</p>
                            <p><i class="fa-solid fa-money-bill mr-2 text-green-600"></i> Rp
                                {{ number_format($k->harga, 0, ',', '.') }}
                            </p>
                            <p><i class="fa-solid fa-percent mr-2 text-yellow-500"></i> Diskon: {{ $k->diskon_persen }}%</p>
                            <p><i class="fa-solid fa-tag mr-2 text-blue-500"></i>
                                <span class="font-medium text-gray-800 dark:text-gray-100">Rp
                                    {{ number_format($k->harga_diskon, 0, ',', '.') }}</span>
                            </p>
                            <p><i class="fa-solid fa-hourglass-end mr-2 text-red-500"></i> Expired:
                                {{ $k->expired_at ? \Carbon\Carbon::parse($k->expired_at)->format('d-m-Y') : '-' }}
                            </p>
                        </div>

                        <p class="text-gray-600 dark:text-gray-400 text-sm mt-3 line-clamp-3">
                            {{ $k->deskripsi }}
                        </p>

                        {{-- ACTIONS --}}
                        <div class="mt-5 flex gap-2">
                            <button
                                class="flex-1 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm font-medium btnOpenEdit transition focus:outline-none focus:ring-2 focus:ring-blue-300"
                                data-id="{{ $k->id }}" data-nama="{{ $k->nama_kelas }}" data-tipe="{{ $k->tipe_kelas }}"
                                data-harga="{{ $k->harga }}" data-deskripsi="{{ $k->deskripsi }}"
                                data-expired="{{ $k->expired_at?->format('Y-m-d') }}" data-kapasitas="{{ $k->kapasitas }}">
                                <i class="fa-solid fa-pen-to-square mr-2"></i> Edit
                            </button>

                            <form action="{{ route('kelas.destroy', $k->id) }}" method="POST" class="flex-1">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-full px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm font-medium">
                                    <i class="fa-solid fa-trash mr-2"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="col-span-full text-center py-16 border border-dashed border-gray-300 dark:border-gray-700 rounded-xl text-gray-600 dark:text-gray-400">
                    <i class="fa-solid fa-folder-open text-4xl mb-3 opacity-60"></i>
                    <p>Belum ada data kelas yang tersedia.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- =========================
    MODAL QR
    ========================= --}}
    <div id="qrModal" class="fixed inset-0 z-50 hidden" aria-hidden="true">
        <div class="absolute inset-0 bg-black/40 modal-overlay" aria-hidden="true"></div>

        <div class="flex items-center justify-center min-h-screen px-4">
            <div id="qrBox"
                class="bg-white dark:bg-gray-900 rounded-xl shadow-2xl p-6 w-full max-w-sm transform scale-95 opacity-0 animate-showModal">
                <h3 id="qrTitle" class="text-xl font-semibold text-gray-900 dark:text-white mb-4"></h3>

                <div id="qrContainer" class="flex items-center justify-center mb-6">
                    <img id="qrImage" src="" alt="QR Code" class="w-40 h-40 rounded-lg shadow-sm mx-auto" />
                </div>

                <button onclick="closeQrModal()"
                    class="w-full py-2 bg-red-600 hover:bg-red-700 text-white rounded-md font-semibold">
                    Tutup
                </button>
            </div>
        </div>
    </div>


    {{-- =========================
    MODAL CREATE & EDIT
    ========================= --}}
    @foreach (['Create' => 'Tambah Kelas', 'Edit' => 'Edit Kelas'] as $modalId => $modalTitle)
        <div id="modal{{ $modalId }}" class="fixed inset-0 z-50 hidden">

            {{-- Overlay --}}
            <div class="absolute inset-0 bg-black/40" onclick="hideElementById('modal{{ $modalId }}')">
            </div>

            {{-- Modal Wrapper (TIDAK FULL LAYAR) --}}
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="bg-white dark:bg-gray-900
                                                           w-full max-w-md md:max-w-lg
                                                           rounded-xl
                                                           shadow-2xl
                                                           transform scale-95 opacity-0 animate-showModal
                                                           flex flex-col
                                                           max-h-[90vh]">

                    {{-- HEADER --}}
                    <div class="flex items-center justify-between
                                                               px-6 py-4
                                                               border-b border-gray-200 dark:border-gray-800">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $modalTitle }}
                        </h3>
                        <button class="text-gray-500 hover:text-gray-700
                                                                   dark:text-gray-400 dark:hover:text-white"
                            onclick="hideElementById('modal{{ $modalId }}')">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>

                    {{-- BODY --}}
                    <div class="flex-1 overflow-y-auto px-6 py-5">
                        <form id="form{{ $modalId }}" action="{{ $modalId === 'Create' ? route('kelas.store') : '' }}"
                            method="POST" enctype="multipart/form-data" class="space-y-4">

                            @csrf
                            @if ($modalId === 'Edit') @method('PUT') @endif
                            <input type="hidden" name="id" id="{{ strtolower($modalId) }}Id">

                            {{-- Nama Kelas --}}
                            <div>
                                <label class="label-modern">Nama Kelas</label>
                                <input id="{{ $modalId === 'Create' ? 'createNama' : 'editNama' }}" name="nama_kelas"
                                    type="text" class="input-modern" required>
                            </div>

                            {{-- Tipe Kelas --}}
                            <div>
                                <label class="label-modern">Tipe Kelas</label>
                                <select id="{{ $modalId === 'Create' ? 'createTipe' : 'editTipe' }}" name="tipe_kelas"
                                    class="input-modern">
                                    @foreach (['Pilates Group', 'Pilates Private', 'Yoga Group', 'Yoga Private'] as $tipe)
                                        <option value="{{ $tipe }}">{{ $tipe }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Harga & Kapasitas --}}
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="label-modern">Harga</label>
                                    <input id="{{ strtolower($modalId) }}Harga" name="harga" class="input-modern">
                                </div>

                                <div>
                                    <label class="label-modern">Kapasitas</label>
                                    <input id="{{ strtolower($modalId) }}Kapasitas" name="kapasitas" type="number" min="1"
                                        class="input-modern" required>
                                </div>
                            </div>

                            {{-- Deskripsi --}}
                            <div>
                                <label class="label-modern">Deskripsi</label>
                                <textarea id="{{ strtolower($modalId) }}Deskripsi" name="deskripsi" rows="3"
                                    class="input-modern resize-none"></textarea>
                            </div>

                       
                            {{-- Expired --}}
                            <div>
                                <label class="label-modern">Kadaluarsa</label>
                                <input type="date" name="expired_at" id="{{ strtolower($modalId) }}Expired" class="input-modern"
                                    min="{{ now()->toDateString() }}">
                            </div>


                            {{-- Gambar --}}
                            <div>
                                <label class="label-modern">Gambar</label>
                                <input name="gambar" type="file" accept="image/*" class="input-modern">
                            </div>
                        </form>
                    </div>

                    {{-- FOOTER --}}
                    <div class="px-6 py-4
                                                               border-t border-gray-200 dark:border-gray-800
                                                               bg-gray-50 dark:bg-gray-900
                                                               flex justify-end gap-3">
                        <button class="btn-cancel" onclick="hideElementById('modal{{ $modalId }}')">
                            Batal
                        </button>
                        <button type="submit" form="form{{ $modalId }}" class="btn-primary">
                            {{ $modalId === 'Create' ? 'Simpan' : 'Update' }}
                        </button>
                    </div>

                </div>
            </div>
        </div>
    @endforeach



    {{-- =========================
    EXTRA CSS (Style B - Material)
    ========================= --}}
    <style>
        /* Animations */
        @keyframes showModal {
            from {
                opacity: 0;
                transform: scale(.96);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .animate-showModal {
            animation: showModal 0.22s ease-out forwards;
        }


        /* Inputs */
        .input-modern {
            width: 100%;
            display: block;
            padding: 0.6rem 0.9rem;
            border-radius: 10px;
            border: 1px solid rgba(0, 0, 0, 0.08);
            background: #ffffff;
            color: #111827;
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.03);
            transition: box-shadow .12s, border-color .12s, transform .12s;
            font-size: 0.95rem;
        }

        .dark .input-modern {
            background: #111827;
            color: #e5e7eb;
            border: 1px solid rgba(255, 255, 255, 0.06);
            box-shadow: none;
        }

        .input-modern:focus {
            outline: none;
            border-color: rgba(99, 102, 241, 1);
            box-shadow: 0 6px 18px rgba(99, 102, 241, 0.08);
        }

        /* Buttons */
        .btn-primary {
            background: #4f46e5;
            color: white;
            padding: .56rem 1.25rem;
            border-radius: 10px;
            font-weight: 600;
            box-shadow: 0 6px 18px rgba(79, 70, 229, 0.12);
            transition: transform .12s, box-shadow .12s, background .12s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            background: #4338ca;
            box-shadow: 0 10px 30px rgba(67, 56, 202, 0.12);
        }


        .btn-cancel {
            background: #f3f4f6;
            color: #111827;
            padding: .56rem 1.25rem;
            border-radius: 10px;
            font-weight: 600;
            border: 1px solid rgba(0, 0, 0, 0.04);
        }

        .dark .btn-cancel {
            background: #1f2937;
            color: #e5e7eb;
            border: 1px solid rgba(255, 255, 255, 0.04);
        }

        /* Card subtle */
        .shadow-2xl {
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.08);
        }

        .rounded-xl {
            border-radius: 12px;
        }

        /* Smooth theme transition */
        body,
        .container {
            transition: background-color .18s, color .18s;
        }

        /* Utility tweaks */
        input[type=file].input-modern {
            padding: .45rem .75rem;
        }
    </style>

    {{-- JS assets (ke file yang sama seperti sebelumnya) --}}
    @vite('resources/js/kelas.js')

    {{-- Small compatibility helpers (used by some inline onclicks) --}}
    <script>
        function showElementById(id) { const el = document.getElementById(id); if (el) el.classList.remove('hidden'); }
        function hideElementById(id) { const el = document.getElementById(id); if (el) el.classList.add('hidden'); }

        /* Close modals with ESC (extra safety) */
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                ['modalCreate', 'modalEdit', 'qrModal'].forEach(id => {
                    const el = document.getElementById(id);
                    if (el && !el.classList.contains('hidden')) hideElementById(id);
                });
            }
        });
    </script>

@endsection