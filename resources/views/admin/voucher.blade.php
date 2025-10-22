@extends('layout.app')

@section('title', 'Manage Voucher')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-white">Manage Voucher</h1>
    <a href="{{ route('voucher.create') }}" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded">
        + Tambah Voucher
    </a>
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
                <td class="px-4 py-2">{{ $voucher->tanggal_akhir->format('Y-m-d') }}</td>
                <td class="px-4 py-2 capitalize">{{ $voucher->status }}</td>
                <td class="px-4 py-2">{{ $voucher->kuota }}</td>
                <td class="px-4 py-2 flex gap-2">
                    <a href="{{ route('voucher.edit', $voucher->id) }}" class="px-2 py-1 bg-blue-500 rounded hover:bg-blue-600 text-sm">Edit</a>
                    <form action="{{ route('voucher.destroy', $voucher->id) }}" method="POST" onsubmit="return confirm('Yakin hapus voucher ini?');">
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
</div>
@endsection
