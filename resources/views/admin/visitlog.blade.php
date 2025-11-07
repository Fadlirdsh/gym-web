@extends('layout.app')

@section('title', 'Visit Log')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Visit Log</h1>

    {{-- 🔍 Filter range tanggal --}}
    <form method="GET" class="mb-4 flex space-x-2">
        <input type="date" name="from_date" value="{{ request('from_date') }}" class="border p-2 rounded">
        <input type="date" name="to_date" value="{{ request('to_date') }}" class="border p-2 rounded">
        <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Filter Range</button>
    </form>

    {{-- 📋 Tabel Visit Log --}}
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white border border-gray-300 text-sm">
            <thead class="bg-gray-200 text-gray-700">
                <tr>
                    <th class="p-2 border">Tanggal</th>
                    <th class="p-2 border">Nama Pelanggan</th>
                    <th class="p-2 border">Jam Reservasi</th>
                    <th class="p-2 border">Kelas</th>
                    <th class="p-2 border">Pengajar</th>
                    <th class="p-2 border">Status</th>
                    <th class="p-2 border">Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($visitLogs as $log)
                    <tr class="hover:bg-gray-50">
                        {{-- Kolom tanggal dari waktu log --}}
                        <td class="p-2 border text-center">
                            {{ $log->created_at ? $log->created_at->format('d/m/Y') : '-' }}
                        </td>

                        {{-- Nama pelanggan (relasi user) --}}
                        <td class="p-2 border">
                            {{ $log->user?->name ?? 'N/A' }}
                        </td>

                        {{-- Jam reservasi (dari relasi reservasi kalau ada) --}}
                        <td class="p-2 border text-center">
                            {{ $log->reservasi->jam_kelas ?? '-' }}
                        </td>

                        {{-- Jenis kelas (dari relasi kelas pada reservasi) --}}
                        <td class="p-2 border text-center">
                            {{ $log->reservasi->kelas->nama_kelas ?? '-' }}
                        </td>

                        {{-- Pengajar (opsional, ambil dari relasi reservasi jika ada kolom pengajar) --}}
                        <td class="p-2 border text-center">
                            {{ $log->reservasi->pengajar ?? '-' }}
                        </td>

                        {{-- Status --}}
                        <td class="p-2 border text-center">
                            <span class="px-2 py-1 rounded text-white 
                                {{ strtolower($log->status) == 'attended' || strtolower($log->status) == 'approved' ? 'bg-green-500' : 'bg-red-500' }}">
                                {{ ucfirst($log->status) }}
                            </span>
                        </td>

                        {{-- Catatan --}}
                        <td class="p-2 border">
                            {{ $log->catatan ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-4 text-center text-gray-500">Tidak ada data kunjungan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination (opsional, aktifkan jika pakai paginate) --}}
    {{-- <div class="mt-4">
        {{ $visitLogs->appends(request()->query())->links() }}
    </div> --}}
</div>
@endsection
