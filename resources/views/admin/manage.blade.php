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

        {{-- =========================== --}}
        {{-- FORM BUAT AKUN --}}
        {{-- =========================== --}}
        <div class="bg-gray-800/80 backdrop-blur-sm border border-gray-700 rounded-2xl shadow-lg p-6">
            <h2 class="text-2xl font-semibold text-white mb-4 flex items-center gap-2">
                Tambah Akun
            </h2>

            <form action="{{ route('users.store') }}" method="POST" class="space-y-3">
                @csrf

                <input type="text" name="name" placeholder="Nama"
                    class="p-3 rounded-lg w-full bg-gray-900 text-white border border-gray-700" required>

                <input type="email" name="email" placeholder="Email"
                    class="p-3 rounded-lg w-full bg-gray-900 text-white border border-gray-700" required>

                <input type="password" name="password" placeholder="Password"
                    class="p-3 rounded-lg w-full bg-gray-900 text-white border border-gray-700" required>

                <input type="password" name="password_confirmation" placeholder="Konfirmasi Password"
                    class="p-3 rounded-lg w-full bg-gray-900 text-white border border-gray-700" required>

                <div class="flex">
                    <span
                        class="p-3 bg-gray-900 text-white border border-gray-700 border-r-0 rounded-l-lg flex items-center">
                        +62
                    </span>
                    <input type="text" name="phone" placeholder="81234567890"
                        class="p-3 w-full bg-gray-900 text-white border border-gray-700 border-l-0 rounded-r-lg" required
                        pattern="[0-9]{6,15}" title="Masukkan nomor tanpa 0 di depan">
                </div>

                <select name="role" class="p-3 rounded-lg w-full bg-gray-900 text-white border border-gray-700" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="pelanggan">Pelanggan</option>
                    <option value="trainer">Trainer</option>
                </select>

                <button type="submit" class="mt-3 bg-indigo-600 px-5 py-2.5 rounded-lg font-semibold text-white">
                    + Buat Akun
                </button>
            </form>
        </div>

        {{-- =========================== --}}
        {{-- FORM BUAT MEMBER --}}
        {{-- =========================== --}}
        <div class="bg-gray-800/80 border border-gray-700 rounded-2xl shadow-lg p-6">
            <h2 class="text-2xl font-semibold text-white mb-4">Tambah Member untuk Pelanggan</h2>

            <form action="{{ route('members.store') }}" method="POST" class="space-y-3">
                @csrf

                <select name="user_id" class="p-3 rounded-lg w-full bg-gray-900 text-white border border-gray-700" required>
                    <option value="">-- Pilih Pelanggan --</option>
                    @foreach ($pelanggan as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>

                <button type="submit" class="mt-3 bg-green-600 px-5 py-2.5 rounded-lg font-semibold text-white">
                    + Buat Member
                </button>
            </form>
        </div>

        {{-- =========================== --}}
        {{-- TABLE USER --}}
        {{-- =========================== --}}
        <div class="bg-gray-800/80 border border-gray-700 rounded-2xl shadow-lg overflow-hidden">
            <div class="p-4 border-b border-gray-700 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-white">Daftar User & Member</h2>
                <span class="text-sm text-gray-400">{{ count($members) }} data ditemukan</span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-gray-300">
                    <thead class="bg-gray-700 text-white">
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Member Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($members as $index => $user)
                            <tr class="border-b border-gray-700 hover:bg-gray-700/30">
                                <td class="px-4 py-3">{{ $index + 1 }}</td>

                                <td class="px-4 py-3 text-white">{{ $user->name }}</td>

                                <td class="px-4 py-3">{{ $user->email }}</td>

                                <td class="px-4 py-3 capitalize">{{ $user->role }}</td>

                                <td class="px-4 py-3">
                                    @if ($user->member)
                                        <span
                                            class="px-2 py-1 rounded text-xs font-medium
                                            {{ $user->member->status === 'aktif' ? 'bg-green-600/30 text-green-300' : 'bg-red-600/30 text-red-300' }}">
                                            {{ ucfirst($user->member->status) }}
                                        </span>
                                    @else
                                        <span class="text-gray-400 text-xs">Belum jadi member</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-center space-x-2">

                                    {{-- TOMBOL EDIT --- FIXED --}}
                                    <button
                                        onclick="openEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}')"
                                        class="bg-yellow-500/80 hover:bg-yellow-400 text-black font-semibold px-3 py-1.5 rounded-lg">
                                        Edit
                                    </button>

                                    {{-- DELETE --}}
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                        class="inline-block"
                                        onsubmit="return confirm('Yakin ingin menghapus pelanggan/member ini?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            class="bg-red-600/80 hover:bg-red-500 text-white font-semibold px-3 py-1.5 rounded-lg">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-gray-400 py-6">
                                    Belum ada pelanggan terdaftar
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- =========================== --}}
            {{-- MODAL EDIT USER --}}
            {{-- =========================== --}}
            <div id="editModal" class="fixed inset-0 bg-black/60 z-50 hidden">
                <div class="flex items-center justify-center w-full h-full">
                    <div class="bg-gray-800 w-full max-w-lg p-6 rounded-xl border border-gray-700">

                        <h2 class="text-xl font-semibold text-white mb-4">Edit User</h2>

                        <form id="editForm" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label class="text-gray-300">Nama</label>
                                <input id="editName" type="text" name="name"
                                    class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg" required />
                            </div>

                            <div class="mb-3">
                                <label class="text-gray-300">Email</label>
                                <input id="editEmail" type="email" name="email"
                                    class="w-full bg-gray-700 text-white px-3 py-2 rounded-lg" required />
                            </div>

                            <div class="flex justify-end gap-2 mt-6">
                                <button type="button" onclick="closeModal()"
                                    class="px-4 py-2 bg-gray-600 text-white rounded-lg">
                                    Batal
                                </button>

                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">
                                    Simpan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>


        @vite('resources/js/manage.js')

    @endsection
