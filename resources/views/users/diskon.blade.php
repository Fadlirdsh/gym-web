@extends('layout.app')

@section('title', 'Diskon')

@section('content')
    <div class="container mx-auto p-6">

        <h1 class="text-2xl font-bold mb-4">Manage Diskon</h1>

        <!-- Tombol Tambah Diskon -->
        <button id="btnOpenCreate" class="bg-blue-500 text-white px-4 py-2 rounded">
            + Tambah Diskon
        </button>

        <!-- Tabel Diskon -->
        <table class="w-full mt-4 border border-gray-300">
            <thead class="bg-gray-100">
                <tr>
                    <th class="p-2 border">ID</th>
                    <th class="p-2 border">Kelas</th>
                    <th class="p-2 border">Nama Diskon</th>
                    <th class="p-2 border">Persentase</th>
                    <th class="p-2 border">Mulai</th>
                    <th class="p-2 border">Berakhir</th>
                    <th class="p-2 border">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($diskons as $diskon)
                    <tr>
                        <td class="p-2 border">{{ $diskon->id }}</td>
                        <td class="p-2 border">{{ $diskon->kelas->nama_kelas ?? '-' }}</td>
                        <td class="p-2 border">{{ $diskon->nama_diskon }}</td>
                        <td class="p-2 border">{{ $diskon->persentase }}%</td>
                        <td class="p-2 border">{{ $diskon->tanggal_mulai }}</td>
                        <td class="p-2 border">{{ $diskon->tanggal_berakhir }}</td>
                        <td class="p-2 border space-x-1">
                            <!-- Tombol Edit pakai modal -->
                            <button class="btnOpenEdit bg-yellow-500 text-white px-2 py-1 rounded"
                                data-id="{{ $diskon->id }}" data-kelas_id="{{ $diskon->kelas_id }}"
                                data-nama_diskon="{{ $diskon->nama_diskon }}" data-persentase="{{ $diskon->persentase }}"
                                data-tanggal_mulai="{{ $diskon->tanggal_mulai }}"
                                data-tanggal_berakhir="{{ $diskon->tanggal_berakhir }}">
                                Edit
                            </button>

                            <!-- Hapus -->
                            <form action="{{ route('diskon.destroy', $diskon->id) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded"
                                    onclick="return confirm('Yakin hapus diskon ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-2 text-center">Belum ada data diskon</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Modal Tambah Diskon -->
        <div id="modalCreate" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow-lg w-96">
                <h2 class="text-xl font-bold mb-4">Tambah Diskon</h2>

                <form action="{{ route('diskon.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block">Pilih Kelas</label>
                        <select name="kelas_id" class="border p-2 w-full">
                            @foreach ($kelas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block">Nama Diskon</label>
                        <input type="text" name="nama_diskon" class="border p-2 w-full" required>
                    </div>

                    <div>
                        <label class="block">Persentase (%)</label>
                        <input type="number" name="persentase" min="1" max="100" class="border p-2 w-full"
                            required>
                    </div>

                    <div>
                        <label class="block">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" class="border p-2 w-full" required>
                    </div>

                    <div>
                        <label class="block">Tanggal Berakhir</label>
                        <input type="date" name="tanggal_berakhir" class="border p-2 w-full" required>
                    </div>

                    <div class="flex justify-between mt-4">
                        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Simpan</button>
                        <button type="button" id="btnCloseCreate"
                            class="bg-gray-500 text-white px-4 py-2 rounded">Batal</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Edit Diskon -->
        <div id="modalEdit" class="hidden fixed inset-0 bg-black bg-opacity-50 items-center justify-center z-50">
            <div class="bg-white p-6 rounded shadow-lg w-96">
                <h2 class="text-xl font-bold mb-4">Edit Diskon</h2>

                <form id="formEditDiskon" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="id" id="editId">

                    <div>
                        <label class="block">Pilih Kelas</label>
                        <select name="kelas_id" id="editKelasId" class="border p-2 w-full">
                            @foreach ($kelas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block">Nama Diskon</label>
                        <input type="text" name="nama_diskon" id="editNamaDiskon" class="border p-2 w-full" required>
                    </div>

                    <div>
                        <label class="block">Persentase (%)</label>
                        <input type="number" name="persentase" id="editPersentase" min="1" max="100"
                            class="border p-2 w-full" required>
                    </div>

                    <div>
                        <label class="block">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="editTanggalMulai" class="border p-2 w-full"
                            required>
                    </div>

                    <div>
                        <label class="block">Tanggal Berakhir</label>
                        <input type="date" name="tanggal_berakhir" id="editTanggalBerakhir" class="border p-2 w-full"
                            required>
                    </div>

                    <div class="flex justify-between mt-4">
                        <button type="submit" class="bg-yellow-500 text-white px-4 py-2 rounded">Update</button>
                        <button type="button" id="btnCloseEdit"
                            class="bg-gray-500 text-white px-4 py-2 rounded">Batal</button>
                    </div>
                </form>
            </div>
        </div>

    </div>

    <!-- JavaScript untuk modal -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Modal Tambah
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

            document.querySelectorAll('.btnOpenEdit').forEach(btn => {
                btn.addEventListener('click', function() {
                    let id = this.dataset.id;
                    let kelas_id = this.dataset.kelas_id;
                    let nama_diskon = this.dataset.nama_diskon;
                    let persentase = this.dataset.persentase;
                    let tanggal_mulai = this.dataset.tanggal_mulai;
                    let tanggal_berakhir = this.dataset.tanggal_berakhir;

                    // isi form modal
                    document.getElementById('editId').value = id;
                    document.getElementById('editKelasId').value = kelas_id;
                    document.getElementById('editNamaDiskon').value = nama_diskon;
                    document.getElementById('editPersentase').value = persentase;
                    document.getElementById('editTanggalMulai').value = tanggal_mulai;
                    document.getElementById('editTanggalBerakhir').value = tanggal_berakhir;

                    // set action form
                    document.getElementById('formEditDiskon').action = "/diskon/" + id;

                    // tampilkan modal
                    modalEdit.classList.remove("hidden");
                    modalEdit.classList.add("flex");
                });
            });

            btnCloseEdit.addEventListener("click", () => {
                modalEdit.classList.add("hidden");
                modalEdit.classList.remove("flex");
            });
        });
    </script>
@endsection
