@extends('layout.app')

@section('title', 'Manajemen User & Member')

@section('content')
<div class="container mx-auto px-4 py-8 space-y-10">

    {{-- 🔔 Pesan sukses --}}
    @if (session('success'))
        <div class="mb-6 rounded-lg bg-green-600/20 border border-green-500/40 text-green-300 px-4 py-3 text-sm">
            {{ session('success') }}
        </div>
    @endif

        {{-- ================== --}}
        {{-- Form buat akun pelanggan --}}
        {{-- ================== --}}
        <div class="mb-6 p-4 bg-gray-800 rounded">
            <h2 class="text-xl font-semibold text-white mb-2">Tambah Akun</h2>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <input type="text" name="name" placeholder="Nama" class="mb-2 p-2 rounded w-full text-black" required>
                <input type="email" name="email" placeholder="Email" class="mb-2 p-2 rounded w-full text-black"
                    required>
                <input type="password" name="password" placeholder="Password" class="mb-2 p-2 rounded w-full text-black"
                    required>
                <input type="password" name="password_confirmation" placeholder="Konfirmasi Password"
                    class="mb-2 p-2 rounded w-full text-black" required>

                {{-- 🔹 Pilih Role --}}
                <div class="mb-3">
                    <select name="role" class="p-2 rounded w-full text-black" required>
                        <option value="pelanggan" style="color: black">Pelanggan</option>
                        <option value="trainer" styl e="color: black">Trainer</option>
                    </select>
                </div>

                <button type="submit" class="bg-indigo-500 px-4 py-2 rounded text-white hover:bg-indigo-400">
                    Buat Akun
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
                            {{ $member->member ? ucfirst($member->member->status) : '-' }}
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

    {{-- 🧍 Form Tambah Akun Pelanggan --}}
    <div class="bg-gray-800/80 backdrop-blur-sm border border-gray-700 rounded-2xl shadow-lg p-6">
        <h2 class="text-2xl font-semibold text-white mb-4 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Tambah Akun Pelanggan
        </h2>
        <form action="{{ route('users.store') }}" method="POST" class="space-y-3">
            @csrf
            <div class="grid md:grid-cols-2 gap-3">
                <input type="text" name="name" placeholder="Nama Lengkap"
                    class="p-3 rounded-lg w-full bg-gray-900 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 focus:outline-none" required>
                <input type="email" name="email" placeholder="Email"
                    class="p-3 rounded-lg w-full bg-gray-900 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 focus:outline-none" required>
            </div>

            <div class="grid md:grid-cols-2 gap-3">
                <input type="password" name="password" placeholder="Password"
                    class="p-3 rounded-lg w-full bg-gray-900 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 focus:outline-none" required>
                <input type="password" name="password_confirmation" placeholder="Konfirmasi Password"
                    class="p-3 rounded-lg w-full bg-gray-900 text-white border border-gray-700 focus:ring-2 focus:ring-indigo-500 focus:outline-none" required>
            </div>

            <button type="submit"
                class="mt-3 bg-indigo-600 hover:bg-indigo-500 transition-all px-5 py-2.5 rounded-lg font-semibold text-white shadow-md hover:shadow-indigo-500/20">
                + Buat Akun Pelanggan
            </button>
        </form>
    </div>

    {{-- 💳 Form Tambah Member --}}
    <div class="bg-gray-800/80 backdrop-blur-sm border border-gray-700 rounded-2xl shadow-lg p-6">
        <h2 class="text-2xl font-semibold text-white mb-4 flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3v7h6v-7c0-1.657-1.343-3-3-3z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 0v1m-6 0h6" />
            </svg>
            Tambah Member untuk Pelanggan
        </h2>

        <form action="{{ route('members.store') }}" method="POST" class="space-y-3">
            @csrf
            <select name="user_id" class="p-3 rounded-lg w-full bg-gray-900 text-white border border-gray-700 focus:ring-2 focus:ring-green-500 focus:outline-none" required>
                <option value="">-- Pilih Pelanggan --</option>
                @foreach ($pelanggan as $user)
                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                @endforeach
            </select>

            <input type="number" name="harga" placeholder="Harga (Rp)"
                class="p-3 rounded-lg w-full bg-gray-900 text-white border border-gray-700 focus:ring-2 focus:ring-green-500 focus:outline-none" required>

            <button type="submit"
                class="mt-3 bg-green-600 hover:bg-green-500 transition-all px-5 py-2.5 rounded-lg font-semibold text-white shadow-md hover:shadow-green-500/20">
                + Buat Member
            </button>
        </form>
    </div>

    {{-- 📋 Tabel Daftar Pelanggan & Member --}}
    <div class="bg-gray-800/80 border border-gray-700 rounded-2xl shadow-lg overflow-hidden">
        <div class="p-4 border-b border-gray-700 flex justify-between items-center">
            <h2 class="text-xl font-semibold text-white">Daftar User & Member</h2>
            <span class="text-sm text-gray-400">{{ count($members) }} data ditemukan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-gray-300">
                <thead class="bg-gray-700 text-white text-left">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Nama</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Role</th>
                        <th class="px-4 py-3">Member Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($members as $index => $member)
                        <tr class="border-b border-gray-700 hover:bg-gray-700/30 transition-colors">
                            <td class="px-4 py-3">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-white">{{ $member->name }}</td>
                            <td class="px-4 py-3">{{ $member->email }}</td>
                            <td class="px-4 py-3 capitalize">{{ $member->role }}</td>
                            <td class="px-4 py-3">
                                @if ($member->member)
                                    <span class="px-2 py-1 rounded text-xs font-medium
                                        {{ $member->member->status === 'aktif' ? 'bg-green-600/30 text-green-300' : 'bg-red-600/30 text-red-300' }}">
                                        {{ ucfirst($member->member->status) }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">Belum jadi member</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center space-x-2">
                                <a href="{{ route('users.edit', $member->id) }}"
                                    class="inline-block bg-yellow-500/80 hover:bg-yellow-400 text-black font-semibold px-3 py-1.5 rounded-lg transition">
                                    Edit
                                </a>
                                <form action="{{ route('users.destroy', $member->id) }}" method="POST" class="inline-block"
                                    onsubmit="return confirm('Yakin ingin menghapus pelanggan/member ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-600/80 hover:bg-red-500 text-white font-semibold px-3 py-1.5 rounded-lg transition">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-gray-400 py-6">Belum ada pelanggan terdaftar</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
