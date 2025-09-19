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
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="fa-solid fa-money-bill"></i>
                                Rp {{ number_format($k->harga - ($k->harga * ($k->diskon ?? 0)) / 100, 0, ',', '.') }}
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="fa-solid fa-money-bill"></i> Rp {{ number_format($k->harga, 0, ',', '.') }}
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="fa-solid fa-percent"></i> {{ $k->diskon }}%
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="fa-solid fa-box"></i> {{ $k->tipe_paket }}
                            </span>
                            <span class="flex items-center gap-1">
                                <i class="fa-solid fa-clock"></i> {{ $k->waktu_mulai }}
                            </span>
                        </div>
                        <p class="mt-3 text-gray-400 text-sm">{{ $k->deskripsi }}</p>
                    </div>

                    <div class="flex gap-2 mt-4">
                        <button class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 btnOpenEdit transition"
                            data-id="{{ $k->id }}" data-nama="{{ $k->nama_kelas }}"
                            data-tipe="{{ $k->tipe_kelas }}" data-harga="{{ $k->harga }}"
                            data-diskon="{{ $k->diskon }}" data-paket="{{ $k->tipe_paket }}"
                            data-deskripsi="{{ $k->deskripsi }}"
                            data-waktu="{{ \Carbon\Carbon::parse($k->waktu_mulai)->format('H:i') }}">
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
    <!-- Modal Create -->
    <div id="modalCreate" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-lg p-6 animate-fadeIn">
            <h2 class="text-xl font-bold mb-4">Tambah Kelas</h2>
            <form action="{{ route('kelas.store') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="block font-medium">Nama Kelas</label>
                    <input type="text" name="nama_kelas" class="w-full border rounded px-3 py-2" required>
                </div>
                <div>
                    <label class="block font-medium">Tipe Kelas</label>
                    <input type="text" name="tipe_kelas" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium">Harga</label>
                    <input type="number" name="harga" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium">Diskon</label>
                    <input type="number" name="diskon" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium">Tipe Paket</label>
                    <input type="text" name="tipe_paket" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium">Deskripsi</label>
                    <textarea name="deskripsi" class="w-full border rounded px-3 py-2"></textarea>
                </div>
                <div>
                    <label class="block font-medium">Waktu Mulai</label>
                    <input type="time" name="waktu_mulai" class="w-full border rounded px-3 py-2">
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
                <div>
                    <label class="block font-medium">Tipe Kelas</label>
                    <input type="text" id="editTipe" name="tipe_kelas" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium">Harga</label>
                    <input type="number" id="editHarga" name="harga" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium">Diskon</label>
                    <input type="number" id="editDiskon" name="diskon" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium">Tipe Paket</label>
                    <input type="text" id="editPaket" name="tipe_paket" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-medium">Deskripsi</label>
                    <textarea id="editDeskripsi" name="deskripsi" class="w-full border rounded px-3 py-2"></textarea>
                </div>
                <div>
                    <label class="block font-medium">Waktu Mulai</label>
                    <input type="time" id="editWaktu" name="waktu_mulai" class="w-full border rounded px-3 py-2">
                </div>
                <div class="flex justify-end gap-2 pt-4">
                    <button type="button" id="btnCloseEdit"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text- rounded hover:bg-blue-700">Update</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal Create
        const btnOpenCreate = document.getElementById("btnOpenCreate");
        const modalCreate = document.getElementById("modalCreate");
        const btnCloseCreate = document.getElementById("btnCloseCreate");

        btnOpenCreate.addEventListener("click", () => modalCreate.classList.remove("hidden"));
        modalCreate.classList.add("flex");

        btnCloseCreate.addEventListener("click", () => modalCreate.classList.add("hidden"));
        modalCreate.classList.remove("flex");

        // Modal Edit
        const modalEdit = document.getElementById("modalEdit");
        const btnCloseEdit = document.getElementById("btnCloseEdit");
        const formEdit = document.getElementById("formEdit");


        document.querySelectorAll(".btnOpenEdit").forEach(btn => {
            btn.addEventListener("click", () => {
                modalEdit.classList.remove("hidden");
                modalEdit.classList.add('flex');
                formEdit.action = "/users/kelas/" + btn.dataset.id;
                document.getElementById("editNama").value = btn.dataset.nama;
                document.getElementById("editTipe").value = btn.dataset.tipe;
                document.getElementById("editHarga").value = btn.dataset.harga;
                document.getElementById("editDiskon").value = btn.dataset.diskon;
                document.getElementById("editPaket").value = btn.dataset.paket;
                document.getElementById("editDeskripsi").value = btn.dataset.deskripsi;
                document.getElementById("editWaktu").value = btn.dataset.waktu;
            });
        });

        btnCloseEdit.addEventListener("click", () =>
            modalEdit.classList.add("hidden"));
        modalEdit.classList.remove('flex');
    </script>

    {{-- Animasi sederhana --}}
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
