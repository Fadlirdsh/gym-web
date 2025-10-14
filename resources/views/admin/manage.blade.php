@extends('layout.app')

@section('title', 'Manage Member')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4 text-2xl font-bold text-white">Manage Member</h1>

        @if (session('success'))
            <div class="mb-4 text-green-400">{{ session('success') }}</div>
        @endif

        <!-- Form tambah member -->
        <form action="{{ route('users.store') }}" method="POST" class="mb-6 p-4 bg-gray-800 rounded">
            @csrf
            <input type="text" name="name" placeholder="Nama" class="mb-2 p-2 rounded w-full" required>
            <input type="email" name="email" placeholder="Email" class="mb-2 p-2 rounded w-full" required>
            <input type="password" name="password" placeholder="Password" class="mb-2 p-2 rounded w-full" required>
            <input type="password" name="password_confirmation" placeholder="Konfirmasi Password"
                class="mb-2 p-2 rounded w-full" required>

            <!-- Pilih kelas -->
            <select name="kelas_id" class="mb-2 p-2 rounded w-full bg-white text-black" required>
                <option value="">-- Pilih Kelas --</option>
                @foreach ($kelas as $item)
                    <option value="{{ $item->id }}">{{ $item->nama_kelas }}</option>
                @endforeach
            </select>

            <button type="submit" class="bg-indigo-500 px-4 py-2 rounded text-white hover:bg-indigo-400">
                Tambah Member
            </button>
        </form>

        <!-- Daftar member -->
        <table class="w-full text-left bg-gray-800 rounded overflow-hidden">
            <thead class="bg-gray-700 text-white">
                <tr>
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">Nama</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Kelas</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-white">
                @forelse($members as $index => $member)
                    <tr class="border-b border-gray-700">
                        <td class="px-4 py-2">{{ $index + 1 }}</td>
                        <td class="px-4 py-2">{{ $member->name }}</td>
                        <td class="px-4 py-2">{{ $member->email }}</td>
                        <td class="px-4 py-2">
                            @if ($member->kelas->isNotEmpty())
                                @foreach ($member->kelas as $kelas)
                                    {{ $kelas->nama_kelas }}@if (!$loop->last)
                                        ,
                                    @endif
                                @endforeach
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-2 space-x-4">
                            <a href="{{ route('users.edit', $member->id) }}"
                                class="bg-yellow-500 px-2 py-1 rounded hover:bg-yellow-400">Edit</a>
                            <form action="{{ route('users.destroy', $member->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 px-2 py-1 rounded hover:bg-red-400"
                                    onclick="return confirm('Yakin ingin menghapus member ini?')">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-4 text-center text-gray-300">Belum ada member</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endsection
