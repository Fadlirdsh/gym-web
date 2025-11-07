@extends('layout.app')

@section('title', 'Manage User & Member')

@section('content')
    <div class="container py-4">
        <h1 class="mb-4 text-2xl font-bold text-white">Manage User & Member</h1>

        @if (session('success'))
            <div class="mb-4 text-green-400">{{ session('success') }}</div>
        @endif

        {{-- ================== --}}
        {{-- Form buat akun pelanggan --}}
        {{-- ================== --}}
        <div class="mb-6 p-4 bg-gray-800 rounded">
            <h2 class="text-xl font-semibold text-white mb-2">Tambah Akun Pelanggan</h2>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <input type="text" name="name" placeholder="Nama" class="mb-2 p-2 rounded w-full" required>
                <input type="email" name="email" placeholder="Email" class="mb-2 p-2 rounded w-full" required>
                <input type="password" name="password" placeholder="Password" class="mb-2 p-2 rounded w-full" required>
                <input type="password" name="password_confirmation" placeholder="Konfirmasi Password"
                    class="mb-2 p-2 rounded w-full" required>

                <button type="submit" class="bg-indigo-500 px-4 py-2 rounded text-white hover:bg-indigo-400">
                    Buat Akun Pelanggan
                </button>
            </form>
        </div>

        {{-- ================== --}}
        {{-- Form buat member --}}
        {{-- ================== --}}
        <div class="mb-6 p-4 bg-gray-800 rounded">
            <h2 class="text-xl font-semibold text-white mb-2">Tambah Member untuk Pelanggan</h2>
            <form action="{{ route('members.store') }}" method="POST">
                @csrf
                {{-- Pilih pelanggan --}}
                {{-- Pilih pelanggan --}}
                <select name="user_id" class="mb-2 p-2 rounded w-full bg-white text-black" required>
                    <option value="">-- Pilih Pelanggan --</option>
                    @foreach ($pelanggan as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>

                {{-- Harga --}}
                <input type="number" name="harga" placeholder="Harga (Rp)" class="mb-2 p-2 rounded w-full" required>

                <button type="submit" class="bg-green-500 px-4 py-2 rounded text-white hover:bg-green-400">
                    Buat Member
                </button>
            </form>
        </div>

        {{-- ================== --}}
        {{-- Tabel daftar pelanggan & member --}}
        {{-- ================== --}}
        <table class="w-full text-left bg-gray-800 rounded overflow-hidden">
            <thead class="bg-gray-700 text-white">
                <tr>
                    <th class="px-4 py-2">#</th>
                    <th class="px-4 py-2">Nama</th>
                    <th class="px-4 py-2">Email</th>
                    <th class="px-4 py-2">Role</th>
                    <th class="px-4 py-2">Member Status</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-white">
                @forelse($members as $index => $member)
                    <tr class="border-b border-gray-700">
                        <td class="px-4 py-2">{{ $index + 1 }}</td>
                        <td class="px-4 py-2">{{ $member->name }}</td>
                        <td class="px-4 py-2">{{ $member->email }}</td>
                        <td class="px-4 py-2">{{ $member->role }}</td>
                        <td class="px-4 py-2">
                            @if ($member->member)
                                {{ ucfirst($member->member->status) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="px-4 py-2 space-x-2">
                            <a href="{{ route('users.edit', $member->id) }}"
                                class="bg-yellow-500 px-2 py-1 rounded hover:bg-yellow-400">Edit</a>
                            <form action="{{ route('users.destroy', $member->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 px-2 py-1 rounded hover:bg-red-400"
                                    onclick="return confirm('Yakin ingin menghapus pelanggan/member ini?')">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-4 text-center text-gray-300">Belum ada pelanggan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
