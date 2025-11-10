@extends('layout.app')

@section('title', 'Manage Voucher')

@section('content')
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-white">Manage Voucher</h1>
        <button type="button" data-bs-toggle="modal" data-bs-target="#addVoucherModal"
            class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded">
            + Tambah Voucher
        </button>
    </div>

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
                            {{-- <a href="{{ route('voucher.edit', $voucher->id) }}"
                                class="px-2 py-1 bg-blue-500 rounded hover:bg-blue-600 text-sm">Edit</a> --}}
                            <form action="{{ route('voucher.destroy', $voucher->id) }}" method="POST"
                                onsubmit="return confirm('Yakin hapus voucher ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2 py-1 bg-red-500 rounded hover:bg-red-600 text-sm">
                                    Hapus
                                </button>
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

        <!-- Modal Tambah -->
        <div class="modal fade" id="addVoucherModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="formAddVoucher">
                        @csrf
                        <div class="modal-body">
                            <select name="kelas_id" class="form-control mb-2" style="color: black">
                                <option value="">Semua Kelas</option>
                                @foreach ($kelas as $k)
                                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="kode" placeholder="Kode Voucher" class="form-control mb-2"
                                style="color: black">
                            <textarea name="deskripsi" placeholder="Deskripsi" class="form-control mb-2" style="color: black"></textarea>
                            <input type="number" name="diskon_persen" placeholder="Diskon (%)" class="form-control mb-2"
                                style="color: black">
                            <input type="date" name="tanggal_mulai" class="form-control mb-2" style="color: black">
                            <input type="date" name="tanggal_akhir" class="form-control mb-2" style="color: black">
                            <input type="number" name="kuota" placeholder="Kuota" class="form-control mb-2"
                                style="color: black">
                            <select name="role_target" class="form-control mb-2" style="color: black">
                                <option value="semua">Semua</option>
                                <option value="pelanggan">Pelanggan</option>
                                <option value="member">Member</option>
                            </select>
                            <select name="status" class="form-control mb-2" style="color: black">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Kirim URL dari Blade ke JS
        const voucherStoreUrl = "{{ route('voucher.store') }}";
    </script>
    @vite('resources/js/voucher.js')

@endsection
