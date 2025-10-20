@extends('layout.app')

@section('title', 'Manage Voucher')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-white">Manage Voucher</h1>
    {{-- <a href="{{ route('voucher.create') }}" class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded">
        + Tambah Voucher
    </a> --}}
</div>

<div class="bg-gray-800 p-6 rounded-lg shadow-lg">
    <table class="min-w-full table-auto text-white">
        <thead>
            <tr class="bg-gray-700">
                <th class="px-4 py-2">Kode Voucher</th>
                <th class="px-4 py-2">Nama Voucher</th>
                <th class="px-4 py-2">Tipe</th>
                <th class="px-4 py-2">Nominal</th>
                <th class="px-4 py-2">Berlaku Sampai</th>
                <th class="px-4 py-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            {{-- Contoh data statis --}}
            <tr class="border-b border-gray-600">
                <td class="px-4 py-2">WELCOME50</td>
                <td class="px-4 py-2">Diskon Selamat Datang</td>
                <td class="px-4 py-2">Persentase</td>
                <td class="px-4 py-2">50%</td>
                <td class="px-4 py-2">2025-12-31</td>
                <td class="px-4 py-2 flex gap-2">
                    <a href="#" class="px-2 py-1 bg-blue-500 rounded hover:bg-blue-600 text-sm">Edit</a>
                    <form action="#" method="POST" onsubmit="return confirm('Yakin hapus voucher ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-2 py-1 bg-red-500 rounded hover:bg-red-600 text-sm">
                            Hapus
                        </button>
                    </form>
                </td>
            </tr>
            {{-- Bisa loop data voucher dari database nanti --}}
        </tbody>
    </table>
</div>
@endsection
