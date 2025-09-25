@extends('layout.app')

@section('title', 'Manajemen Kelas')

@section('content')
    <div class="container mx-auto py-6 space-y-6">

        {{-- Pesan sukses --}}
        @if (session('success'))
            <div class="mb-4 rounded bg-green-100 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-100">Daftar Kelas</h1>
            <button id="btnOpenCreate" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                + Tambah Kelas
            </button>
        </div>

        {{-- List Card Kelas --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($kelas as $k)
                <div class="bg-[#0F172A] text-white rounded-lg shadow-md p-6 flex flex-col justify-between">
                    <div>
                        <h2 class="text-xl font-bold">{{ $k->nama_kelas }}</h2>
                        <div class="flex flex-wrap gap-4 mt-3 text-gray-300 text-sm">
                            <span class="flex items-center gap-1">
                                <i class="fa-solid fa-users"></i> {{ $k->tipe_kelas }}
                                <span class="flex items-center gap-1">
                                    <i class="fa-solid fa-money-bill"></i>
                                    Rp {{ number_format($k->harga, 0, ',', '.') }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="fa-solid fa-percent"></i>
                                    Diskon: {{ $k->diskon_persen }}%
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="fa-solid fa-tag"></i>
                                    Harga Setelah Diskon: Rp {{ number_format($k->harga_diskon, 0, ',', '.') }}
                                </span>

                                <span class="flex items-center gap-1">
                                    <i class="fa-solid fa-box"></i> {{ $k->tipe_paket }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="fa-solid fa-ticket"></i> Token: {{ $k->jumlah_token ?? '-' }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="fa-solid fa-hourglass-end"></i>
                                    Expired:
                                    {{ $k->expired_at ? \Carbon\Carbon::parse($k->expired_at)->format('d-m-Y H:i') : '-' }}
                                </span>
                        </div>
                        <p class="mt-3 text-gray-400 text-sm">{{ $k->deskripsi }}</p>
                    </div>

                    <div class="flex gap-2 mt-4">
                        <button class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 btnOpenEdit transition"
                            data-id="{{ $k->id }}" data-nama="{{ $k->nama_kelas }}"
                            data-tipe="{{ $k->tipe_kelas }}" data-harga="{{ $k->harga }}"
                            data-paket="{{ $k->tipe_paket }}" data-deskripsi="{{ $k->deskripsi }}"
                            data-token="{{ $k->jumlah_token }}"
                            data-expired="{{ $k->expired_at ? \Carbon\Carbon::parse($k->expired_at)->format('Y-m-d\TH:i') : '' }}">
                            Edit
                        </button>

                        <form action="{{ route('kelas.destroy', ['kelas' => $k->id]) }}" method="POST"
                            onsubmit="return confirm('Yakin hapus data ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700 transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-12 text-gray-400 border border-dashed border-gray-600 rounded-lg">
                    <i class="fa-solid fa-folder-open text-4xl mb-2"></i>
                    <p>Belum ada data kelas.</p>
                </div>
            @endforelse
        </div>
    </div>


    {{-- Modal Create --}}
    <div id="modalCreate" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 animate-fadeIn">
            <h2 class="text-xl font-bold mb-4">Tambah Kelas</h2>
            <form action="{{ route('kelas.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block font-medium">Nama Kelas</label>
                    <input type="text" name="nama_kelas" class="w-full border rounded px-3 py-2" required>
                </div>
                @php
                    $tipeKelas = ['Pilates Group', 'Pilates Private', 'Yoga Group', 'Yoga Private'];
                @endphp

                <div>
                    <label class="block font-medium">Tipe Kelas</label>
                    <select name="tipe_kelas" class="w-full border rounded px-3 py-2">
                        @foreach ($tipeKelas as $tipe)
                            <option value="{{ $tipe }}" {{ old('tipe_kelas') == $tipe ? 'selected' : '' }}>
                                {{ $tipe }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-medium">Harga</label>
                    <input type="number" name="harga" class="w-full border rounded px-3 py-2">
                </div>
                {{-- <div>
                    <label class="block font-medium">Diskon</label>
                    <input type="number" name="diskon" class="w-full border rounded px-3 py-2">
                </div> --}}
                {{-- Tipe paket --}}
                @php
                    $tipePaket = ['General', 'First Timer', 'Free Points', 'Classes'];
                @endphp
                <div>
                    <label class="block font-medium">Tipe Paket</label>
                    <select name="tipe_paket" id="tipePaketCreate" class="w-full border rounded px-3 py-2">
                        <option value="">-- Pilih Tipe Paket --</option>
                        @foreach ($tipePaket as $paket)
                            <option value="{{ $paket }}" {{ old('tipe_paket') == $paket ? 'selected' : '' }}>
                                {{ $paket }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-medium">Deskripsi</label>
                    <textarea name="deskripsi" class="w-full border rounded px-3 py-2"></textarea>
                </div>
                {{-- <div>
                    <label class="block font-medium">Waktu Mulai</label>
                    <input type="datetime-local" name="waktu_mulai" class="w-full border rounded px-3 py-2">
                </div> --}}
                <div>
                    <label class="block font-medium">Jumlah Token</label>
                    <input type="number" name="jumlah_token" class="w-full border rounded px-3 py-2" min="0">
                </div>
                <div>
                    <label class="block font-medium">Expired At</label>
                    <input type="datetime-local" name="expired_at" class="w-full border rounded px-3 py-2">
                </div>
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" id="btnCloseCreate"
                        class="px-4 py-2 bg-gray-500 text-black rounded hover:bg-gray-600">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-green-600 text-black rounded hover:bg-green-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div id="modalEdit" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 animate-fadeIn">
            <h2 class="text-xl font-bold mb-4">Edit Kelas</h2>
            <form id="formEdit" method="POST" class="space-y-3">
                @csrf
                @method('PUT')
                <div>
                    <label class="block font-medium">Nama Kelas</label>
                    <input type="text" id="editNama" name="nama_kelas" class="w-full border rounded px-3 py-2" required>
                </div>
                @php
                    $tipeKelas = ['Pilates Group', 'Pilates Private', 'Yoga Group', 'Yoga Private'];
                @endphp

                <select id="editTipe" name="tipe_kelas" class="w-full border rounded px-3 py-2">
                    @foreach ($tipeKelas as $tipe)
                        <option value="{{ $tipe }}">{{ $tipe }}</option>
                    @endforeach
                </select>
                <div>
                    <label class="block font-medium">Harga</label>
                    <input type="number" id="editHarga" name="harga" class="w-full border rounded px-3 py-2">
                </div>
                {{-- <div>
                    <label class="block font-medium">Diskon</label>
                    <input type="number" id="editDiskon" name="diskon" class="w-full border rounded px-3 py-2">
                </div> --}}
                {{-- Tipe Paket --}}
                <select name="tipe_paket" id="tipePaketEdit" class="w-full border rounded px-3 py-2">
                    <option value="">-- Pilih Tipe Paket --</option>
                    @foreach ($tipePaket as $paket)
                        <option value="{{ $paket }}">{{ $paket }}</option>
                    @endforeach
                </select>

                <div>
                    <label class="block font-medium">Deskripsi</label>
                    <textarea id="editDeskripsi" name="deskripsi" class="w-full border rounded px-3 py-2"></textarea>
                </div>
                {{-- <div>
                    <label class="block font-medium">Waktu Mulai</label>
                    <input type="datetime-local" name="waktu_mulai" id="editWaktu"
                        class="w-full border rounded px-3 py-2">
                </div> --}}
                <div>
                    <label class="block font-medium">Jumlah Token</label>
                    <input type="number" id="editToken" name="jumlah_token" class="w-full border rounded px-3 py-2"
                        min="0">
                </div>
                <div>
                    <label class="block font-medium">Expired At</label>
                    <input type="datetime-local" id="editExpired" name="expired_at"
                        class="w-full border rounded px-3 py-2">
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" id="btnCloseEdit"
                        class="px-4 py-2 bg-gray-500 text-black rounded hover:bg-gray-600">Batal</button>
                    <button type="submit"
                        class="px-4 py-2 bg-gray-600 text-black rounded hover:bg-blue-700">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal Create
        const btnOpenCreate = document.getElementById("btnOpenCreate");
        const modalCreate = document.getElementById("modalCreate");
        const btnCloseCreate = document.getElementById("btnCloseCreate");

        btnOpenCreate.addEventListener("click", () => {
            modalCreate.classList.remove("hidden");
            modalCreate.classList.add("flex");
        });

        btnCloseCreate.addEventListener("click", () => {
            modalCreate.classList.add("hidden");
            modalCreate.classList.remove("flex");
        });

        // Modal Edit
        const modalEdit = document.getElementById("modalEdit");
        const btnCloseEdit = document.getElementById("btnCloseEdit");
        const formEdit = document.getElementById("formEdit");
        const editTipe = document.getElementById("editTipe");

        document.querySelectorAll(".btnOpenEdit").forEach(btn => {
            btn.addEventListener("click", () => {
                modalEdit.classList.remove("hidden");
                modalEdit.classList.add('flex');

                // Set form action
                formEdit.action = "/users/kelas/" + btn.dataset.id;

                // Isi field edit
                document.getElementById("editNama").value = btn.dataset.nama;
                document.getElementById("editHarga").value = btn.dataset.harga;
                // document.getElementById("editDiskon").value = btn.dataset.diskon;
                document.getElementById("editPaket").value = btn.dataset.paket;
                document.getElementById("editDeskripsi").value = btn.dataset.deskripsi;
                document.getElementById("editWaktu").value = btn.dataset.waktu;
                document.getElementById("editToken").value = btn.dataset.token;
                document.getElementById("editExpired").value = btn.dataset.expired;

                // Pilih tipe kelas sesuai data
                editTipe.value = btn.dataset.tipe;

                // Update hide/show token & expired sesuai tipe paket
                toggleTokenFieldsEdit();
            });
        });

        btnCloseEdit.addEventListener("click", () => {
            modalEdit.classList.add("hidden");
            modalEdit.classList.remove("flex");
        });

        // ===== Tambahan: Hide/Show token & expired =====

        // Modal Create
        const tipeInputCreate = document.querySelector('#modalCreate [name="tipe_paket"]');
        const tokenInputCreate = document.querySelector('#modalCreate [name="jumlah_token"]');
        const expiredInputCreate = document.querySelector('#modalCreate [name="expired_at"]');

        function toggleTokenFieldsCreate() {
            if (tipeInputCreate.value.toLowerCase() === 'classes') {
                tokenInputCreate.parentElement.style.display = 'block';
                expiredInputCreate.parentElement.style.display = 'block';
                tokenInputCreate.setAttribute('required', 'required'); // wajib isi
                expiredInputCreate.setAttribute('required', 'required'); // wajib isi
                tokenInputCreate.setAttribute('min', '1'); // minimal 1
            } else {
                tokenInputCreate.parentElement.style.display = 'none';
                expiredInputCreate.parentElement.style.display = 'none';
                tokenInputCreate.value = '';
                expiredInputCreate.value = '';
                tokenInputCreate.removeAttribute('required');
                expiredInputCreate.removeAttribute('required');
                tokenInputCreate.removeAttribute('min');
            }
        }

        tipeInputCreate.addEventListener('input', toggleTokenFieldsCreate);
        toggleTokenFieldsCreate(); // inisialisasi saat modal terbuka

        // Modal Edit
        const tipeInputEdit = document.querySelector('#modalEdit [name="tipe_paket"]');
        const tokenInputEdit = document.querySelector('#modalEdit [name="jumlah_token"]');
        const expiredInputEdit = document.querySelector('#modalEdit [name="expired_at"]');

        function toggleTokenFieldsEdit() {
            if (tipeInputEdit.value.toLowerCase() === 'classes') {
                tokenInputEdit.parentElement.style.display = 'block';
                expiredInputEdit.parentElement.style.display = 'block';
            } else {
                tokenInputEdit.parentElement.style.display = 'none';
                expiredInputEdit.parentElement.style.display = 'none';
                tokenInputEdit.value = '';
                expiredInputEdit.value = '';
            }
        }
        tipeInputEdit.addEventListener('input', toggleTokenFieldsEdit);
    </script>

    <style>
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
@endsection
