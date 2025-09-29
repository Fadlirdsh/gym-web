@extends('layout.app')

@section('title', 'Visit Log')

@section('content')
<div class="container mx-auto p-6">
    <h1 class="text-2xl font-bold mb-4">Visit Log</h1>

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
            @foreach($visitLogs as $log)
                <tr>
                    <td class="p-2 border">{{ $log->created_at }}</td>
                    <td class="p-2 border">{{ $log->user->name }}</td>
                    <td class="p-2 border">{{ $log->reservasi->kelas->nama_kelas ?? 'N/A' }}</td>
                    <td class="p-2 border">{{ ucfirst($log->status) }}</td>
                    <td class="p-2 border">{{ $log->catatan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- <div class="mt-4">
        {{ $visitLogs->links() }}
    </div> --}}
</div>
@endsection
