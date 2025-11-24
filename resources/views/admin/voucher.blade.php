<!-- Updated blade with modal form -->
@extends('layout.app')

@section('title', 'Manage Voucher')

@section('content')
<div class="relative">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-white">Manage Voucher</h1>

        <button id="showFormBtn" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded">
            + Tambah Voucher
        </button>
    </div>

    <!-- Modal Overlay -->
    <div id="addVoucherModal"
         class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center z-50 opacity-0 pointer-events-none hidden transition-opacity duration-300">

        <!-- Modal Box -->
        <div id="modalBox"
             class="bg-gray-800 rounded-lg shadow-lg w-full max-w-lg p-6 transform scale-0 transition-all duration-300">
            
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-white">Tambah Voucher</h2>
                <button id="closeModalBtn" class="text-white text-2xl leading-none">&times;</button>
            </div>

            <form id="formAddVoucher" method="POST" action="{{ route('voucher.store') }}">
                @csrf
                <select name="kelas_id" class="form-control mb-2" style="color: black">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelas as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>

                <input type="text" name="kode" placeholder="Kode Voucher" class="form-control mb-2" style="color: black">
                <textarea name="deskripsi" placeholder="Deskripsi" class="form-control mb-2" style="color: black"></textarea>
                <input type="number" name="diskon_persen" placeholder="Diskon (%)" class="form-control mb-2" style="color: black">
                <input type="date" name="tanggal_mulai" class="form-control mb-2" style="color: black">
                <input type="date" name="tanggal_akhir" class="form-control mb-2" style="color: black">
                <input type="number" name="kuota" placeholder="Kuota" class="form-control mb-2" style="color: black">

                <select name="role_target" class="form-control mb-2" style="color: black">
                    <option value="semua">Semua</option>
                    <option value="pelanggan">Pelanggan</option>
                    <option value="member">Member</option>
                </select>

                <select name="status" class="form-control mb-2" style="color: black">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded mt-2 w-full">Simpan</button>
            </form>

        </div>
    </div>

    <!-- Tabel voucher -->
    <div class="bg-gray-800 p-6 rounded-lg shadow-lg">
        <table class="min-w-full table-auto text-white">
            <thead>
                <tr class="bg-gray-700">
                    <th class="px-4 py-2">Kode Voucher</th>
                    <th class="px-4 py-2">Deskripsi</th>
                    <th class="px-4 py-2">Role Target</th>
                    <th class="px-4 py-2">Diskon (%)</th>
                    <th class="px-4 py-2">Kelas</th>
                    <th class="px-4 py-2">Berlaku Sampai</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Kuota</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($vouchers as $voucher)
                    <tr class="border-b border-gray-600">
                        <td class="px-4 py-2">{{ $voucher->kode }}</td>
                        <td class="px-4 py-2">{{ $voucher->deskripsi }}</td>
                        <td class="px-4 py-2 capitalize">{{ $voucher->role_target }}</td>
                        <td class="px-4 py-2">{{ $voucher->diskon_persen }}%</td>
                        <td class="px-4 py-2">{{ $voucher->kelas ? $voucher->kelas->nama_kelas : 'Semua Kelas' }}</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($voucher->tanggal_akhir)->format('Y-m-d') }}</td>
                        <td class="px-4 py-2 capitalize">{{ $voucher->status }}</td>
                        <td class="px-4 py-2">{{ $voucher->kuota }}</td>
                        <td class="px-4 py-2 flex gap-2">
                            <form action="{{ route('voucher.destroy', $voucher->id) }}" method="POST" onsubmit="return confirm('Yakin hapus voucher ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2 py-1 bg-red-500 rounded hover:bg-red-600 text-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-2 text-center text-gray-400">Belum ada voucher</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    const showFormBtn = document.getElementById('showFormBtn');
    const modal = document.getElementById('addVoucherModal');
    const modalBox = document.getElementById('modalBox');
    const closeModalBtn = document.getElementById('closeModalBtn');

    // SHOW MODAL
    showFormBtn.addEventListener('click', () => {
        modal.classList.remove('pointer-events-none', 'hidden');
        modal.classList.add('opacity-100');
        modalBox.classList.remove('scale-0');
        modalBox.classList.add('scale-100');
    });

    // CLOSE MODAL via X button
    closeModalBtn.addEventListener('click', () => {
        modal.classList.add('pointer-events-none', 'hidden');
        modal.classList.remove('opacity-100');
        modalBox.classList.remove('scale-100');
        modalBox.classList.add('scale-0');
    });

    // CLOSE MODAL when clicking outside the box
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.classList.add('pointer-events-none', 'hidden');
            modal.classList.remove('opacity-100');
            modalBox.classList.remove('scale-100');
            modalBox.classList.add('scale-0');
        }
    });
</script>

@endsection
