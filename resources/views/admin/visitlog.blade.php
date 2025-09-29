@extends('layout.app')

@section('title', 'Visit Log')

@section('content')
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Visit Log</h1>

        {{-- Filter per tanggal --}}
        <form method="GET" class="mb-4 flex space-x-2">
            <input type="date" name="date" value="{{ request('date') }}" class="border p-2 rounded">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Filter</button>
        </form>

        {{-- Filter range tanggal --}}
        <form method="GET" class="mb-4 flex space-x-2">
            <input type="date" name="from_date" value="{{ request('from_date') }}" class="border p-2 rounded">
            <input type="date" name="to_date" value="{{ request('to_date') }}" class="border p-2 rounded">
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded">Filter Range</button>
        </form>

        {{-- Tabel data --}}
        <table class="min-w-full bg-white border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2 border">Waktu</th>
                    <th class="p-2 border">Nama Pengguna</th>
                    <th class="p-2 border">Kelas / Reservasi</th>
                    <th class="p-2 border">Status</th>
                    <th class="p-2 border">Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($visitLogs as $log)
                    <tr>
                        <td class="p-2 border">{{ $log->created_at->format('d/m/Y H:i') }}</td>
                        <td class="p-2 border">{{ $log->user?->name ?? 'N/A' }}</td>
                        <td class="p-2 border">{{ $log->reservasi->kelas->nama_kelas ?? 'N/A' }}</td>
                        <td class="p-2 border">{{ ucfirst($log->status) }}</td>
                        <td class="p-2 border">{{ $log->catatan }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-2 border text-center text-gray-500">Tidak ada data</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Pagination (aktifkan kalau pakai paginate di controller) --}}
        {{-- <div class="mt-4">
            {{ $visitLogs->appends(request()->query())->links() }}
        </div> --}}
    </div>
@endsection
