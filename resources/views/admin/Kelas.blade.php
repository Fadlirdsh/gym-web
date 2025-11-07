@extends('layout.app')

@section('title', 'Manajemen Kelas')

@section('content')
<div class="container mx-auto py-8 space-y-8">

    {{-- ✅ Pesan sukses --}}
    @if (session('success'))
        <div class="mb-4 rounded-md bg-green-600/20 border border-green-500/40 text-green-300 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- ✅ Header --}}
    <div class="flex flex-col md:flex-row justify-between md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-white">Manajemen Kelas</h1>
            <p class="text-gray-400 text-sm">Kelola daftar kelas yang tersedia di Paradise Gym.</p>
        </div>
        <button id="btnOpenCreate"
            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow transition">
            + Tambah Kelas
        </button>
    </div>

    {{-- ✅ Daftar Kelas --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($kelas as $k)
            <div
                class="group bg-gray-800 border border-gray-700 rounded-xl shadow-md hover:shadow-xl transition-all duration-200 overflow-hidden flex flex-col justify-between">
                {{-- Gambar --}}
                @if ($k->gambar)
                    <img src="{{ asset($k->gambar) }}" alt="{{ $k->nama_kelas }}"
                        class="w-full h-48 object-cover group-hover:opacity-90 transition">
                @endif

                {{-- Info --}}
                <div class="p-5 flex flex-col flex-1">
                    <h2 class="text-lg font-semibold text-white mb-2">{{ $k->nama_kelas }}</h2>

                    <div class="text-gray-400 text-sm space-y-1">
                        <p><i class="fa-solid fa-users mr-1"></i> {{ $k->tipe_kelas }}</p>
                        <p><i class="fa-solid fa-money-bill mr-1"></i> Rp {{ number_format($k->harga, 0, ',', '.') }}</p>
                        <p><i class="fa-solid fa-percent mr-1"></i> Diskon: {{ $k->diskon_persen }}%</p>
                        <p><i class="fa-solid fa-tag mr-1"></i> Harga Setelah Diskon:
                            Rp {{ number_format($k->harga_diskon, 0, ',', '.') }}</p>
                        <p><i class="fa-solid fa-box mr-1"></i> {{ $k->tipe_paket }}</p>
                        <p><i class="fa-solid fa-ticket mr-1"></i> Token: {{ $k->jumlah_token ?? '-' }}</p>
                        <p><i class="fa-solid fa-hourglass-end mr-1"></i> Expired:
                            {{ $k->expired_at ? \Carbon\Carbon::parse($k->expired_at)->format('d-m-Y H:i') : '-' }}</p>
                    </div>

                    <p class="text-gray-400 text-sm mt-3 line-clamp-3">{{ $k->deskripsi }}</p>

                    {{-- Tombol Aksi --}}
                    <div class="flex gap-2 mt-5">
                        <button
                            class="flex-1 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md text-sm btnOpenEdit transition"
                            data-id="{{ $k->id }}"
                            data-nama="{{ $k->nama_kelas }}"
                            data-tipe="{{ $k->tipe_kelas }}"
                            data-harga="{{ $k->harga }}"
                            data-paket="{{ $k->tipe_paket }}"
                            data-deskripsi="{{ $k->deskripsi }}"
                            data-token="{{ $k->jumlah_token }}"
                            data-expired="{{ $k->expired_at ? \Carbon\Carbon::parse($k->expired_at)->format('Y-m-d\TH:i') : '' }}"
                            data-kapasitas="{{ $k->kapasitas }}">
                            Edit
                        </button>

                        <form action="{{ route('kelas.destroy', ['kelas' => $k->id]) }}" method="POST"
                            onsubmit="return confirm('Yakin hapus data ini?')" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="w-full px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm transition">
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div
                class="col-span-full text-center py-16 border border-dashed border-gray-600 rounded-xl text-gray-400">
                <i class="fa-solid fa-folder-open text-4xl mb-3"></i>
                <p>Belum ada data kelas yang tersedia.</p>
            </div>
        @endforelse
    </div>
</div>

{{-- ✅ Modal Create --}}
<div id="modalCreate"
    class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-gray-800 text-gray-200 rounded-xl w-full max-w-lg p-6 animate-fadeIn border border-gray-700">
        <h2 class="text-xl font-bold mb-4">Tambah Kelas</h2>
        <form action="{{ route('kelas.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block mb-1 font-medium">Nama Kelas</label>
                <input type="text" name="nama_kelas"
                    class="w-full bg-gray-900 border border-gray-700 rounded-md px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none"
                    required>
            </div>

            <div>
                <label class="block mb-1 font-medium">Tipe Kelas</label>
                <select name="tipe_kelas"
                    class="w-full bg-gray-900 border border-gray-700 rounded-md px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                    @foreach (['Pilates Group', 'Pilates Private', 'Yoga Group', 'Yoga Private'] as $tipe)
                        <option value="{{ $tipe }}">{{ $tipe }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block mb-1 font-medium">Harga</label>
                <input type="number" name="harga"
                    class="w-full bg-gray-900 border border-gray-700 rounded-md px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
            </div>

            <div>
                <label class="block mb-1 font-medium">Tipe Paket</label>
                <select name="tipe_paket" id="tipePaketCreate"
                    class="w-full bg-gray-900 border border-gray-700 rounded-md px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="">-- Pilih Tipe Paket --</option>
                    @foreach (['General', 'First Timer', 'Free Points', 'Classes'] as $paket)
                        <option value="{{ $paket }}">{{ $paket }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block mb-1 font-medium">Deskripsi</label>
                <textarea name="deskripsi"
                    class="w-full bg-gray-900 border border-gray-700 rounded-md px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 font-medium">Jumlah Token</label>
                    <input type="number" name="jumlah_token" class="w-full bg-gray-900 border border-gray-700 rounded-md px-3 py-2" min="0">
                </div>

                <div>
                    <label class="block mb-1 font-medium">Kapasitas</label>
                    <input type="number" name="kapasitas" id="kapasitas"
                        class="w-full bg-gray-900 border border-gray-700 rounded-md px-3 py-2" value="20" min="1"
                        required>
                </div>
            </div>

            <div>
                <label class="block mb-1 font-medium">Expired At</label>
                <input type="datetime-local" name="expired_at"
                    class="w-full bg-gray-900 border border-gray-700 rounded-md px-3 py-2">
            </div>

            <div>
                <label class="block mb-1 font-medium">Gambar</label>
                <input type="file" name="gambar"
                    class="w-full bg-gray-900 border border-gray-700 rounded-md px-3 py-2" accept="image/*">
            </div>

            <div class="flex justify-end gap-3 pt-3">
                <button type="button" id="btnCloseCreate"
                    class="px-4 py-2 bg-gray-700 text-white rounded-md hover:bg-gray-600 transition">Batal</button>
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ✅ Modal Edit --}}
<div id="modalEdit"
    class="hidden fixed inset-0 bg-black/70 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-gray-800 text-gray-200 rounded-xl w-full max-w-lg p-6 animate-fadeIn border border-gray-700">
        <h2 class="text-xl font-bold mb-4">Edit Kelas</h2>
        <form id="formEdit" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block mb-1 font-medium">Nama Kelas</label>
                <input type="text" id="editNama" name="nama_kelas"
                    class="w-full bg-gray-900 border border-gray-700 rounded-md px-3 py-2 focus:ring-2 focus:ring-indigo-500 outline-none" required>
            </div>

            <div>
                <label class="block mb-1 font-medium">Tipe Kelas</label>
                <select id="editTipe" name="tipe_kelas"
                    class="w-full bg-gray-900 border border-gray-700 rounded-md px-3 py-2">
                    @foreach (['Pilates Group', 'Pilates Private', 'Yoga Group', 'Yoga Private'] as $tipe)
                        <option value="{{ $tipe }}">{{ $tipe }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block mb-1 font-medium">Harga</label>
                <input type="number" id="editHarga" name="harga"
                    class="w-full bg-gray-900 border border-gray-700 rounded-md px-3 py-2">
            </div>

            <div>
                <label class="block mb-1 font-medium">Tipe Paket</label>
                <select name="tipe_paket" id="tipePaketEdit"
                    class="w-full bg-gray-900 border border-gray-700 rounded-md px-3 py-2">
                    <option value="">-- Pilih Tipe Paket --</option>
                    @foreach (['General', 'First Timer', 'Free Points', 'Classes'] as $paket)
                        <option value="{{ $paket }}">{{ $paket }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block mb-1 font-medium">Deskripsi</label>
                <textarea id="editDeskripsi" name="deskripsi"
                    class="w-full bg-gray-900 border border-gray-700 rounded-md px-3 py-2"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block mb-1 font-medium">Jumlah Token</label>
                    <input type="number" id="editToken" name="jumlah_token"
                        class="w-full bg-gray-900 border border-gray-700 rounded-md px-3 py-2" min="0">
                </div>

                <div>
                    <label class="block mb-1 font-medium">Kapasitas</label>
                    <input type="number" name="kapasitas" id="editKapasitas"
                        class="w-full bg-gray-900 border border-gray-700 rounded-md px-3 py-2" min="1" required>
                </div>
            </div>

            <div>
                <label class="block mb-1 font-medium">Expired At</label>
                <input type="datetime-local" id="editExpired" name="expired_at"
                    class="w-full bg-gray-900 border border-gray-700 rounded-md px-3 py-2">
            </div>

            <div>
                <label class="block mb-1 font-medium">Gambar</label>
                <input type="file" name="gambar"
                    class="w-full bg-gray-900 border border-gray-700 rounded-md px-3 py-2" accept="image/*">
                <div id="previewEditGambar" class="mt-3"></div>
            </div>

            <div class="flex justify-end gap-3 pt-3">
                <button type="button" id="btnCloseEdit"
                    class="px-4 py-2 bg-gray-700 text-white rounded-md hover:bg-gray-600 transition">Batal</button>
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">Update</button>
            </div>
        </form>
    </div>
</div>

{{-- Animation Style --}}
<style>
    .animate-fadeIn {
        animation: fadeIn 0.25s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.97);
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
