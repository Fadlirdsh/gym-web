@extends('layout.app')
@section('title', 'Manajemen User & Member')
@section('content')

    <div class="container mx-auto px-4 py-8 space-y-10">

        {{-- SUCCESS ALERT --}}
        @if (session('success'))
            <div class="p-4 rounded-xl card text-green-700 dark:text-green-300 shadow">
                {{ session('success') }}
            </div>
        @endif

        <!-- FORM TAMBAH AKUN -->
        <div class="card p-6 space-y-4">
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-5">
                Tambah Akun Baru
            </h2>
            <form action="{{ route('users.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" name="name" placeholder="Nama Lengkap" class="input-fix" required>
                    <input type="email" name="email" placeholder="Email" class="input-fix" required>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="password" name="password" placeholder="Password" class="input-fix" required>
                    <input type="password" name="password_confirmation" placeholder="Konfirmasi Password" class="input-fix"
                        required>
                </div>
                <div class="phone-group">
                    <span class="phone-prefix">+62</span>
                    <input type="text" name="phone" placeholder="81234567890" class="input-fix phone-input" required>
                </div>

                <select name="role" class="input-fix w-full" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="pelanggan">Pelanggan</option>
                    <option value="trainer">Trainer</option>
                </select>
                <button class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-500 text-white tooltip">
                    + Buat Akun
                    <span class="tooltip-text">Klik untuk menambahkan akun baru</span>
                </button>
            </form>
        </div>

        <!-- TABEL USER -->
        <div class="card shadow-lg overflow-hidden">
            <div class="p-4 border-b border-gray-300 dark:border-gray-600 flex justify-between items-center">
                <h2 class="text-lg md:text-xl font-semibold text-gray-900 dark:text-white">
                    Daftar User & Member
                </h2>
                <span class="text-sm text-gray-600 dark:text-gray-400">
                    {{ count($members) }} data ditemukan
                </span>
            </div>

            <div class="table-wrapper overflow-x-auto">
                <table class="min-w-full text-sm md:text-base">
                    <thead class="thead-light">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status Member</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($members as $index => $user)
                            <tr>
                                <td data-label="No">{{ $index + 1 }}</td>
                                <td data-label="Nama" class="font-semibold text-gray-900 dark:text-white">{{ $user->name }}</td>
                                <td data-label="Email" class="text-gray-700 dark:text-gray-300">{{ $user->email }}</td>
                                <td data-label="Role" class="capitalize text-gray-700 dark:text-gray-300">{{ $user->role }}</td>
                                <td data-label="Status">
                                    @if ($user->member)
                                        <span class="badge-{{ $user->member->status === 'aktif' ? 'green' : 'red' }}">
                                            {{ ucfirst($user->member->status) }}
                                        </span>
                                    @else
                                        <span class="text-gray-500 dark:text-gray-400 text-xs italic">
                                            Belum jadi member
                                        </span>
                                    @endif
                                </td>
                                <td data-label="Aksi" class="text-center space-x-2">
                                    <div class="flex justify-center items-center gap-2">
                                        <a href="{{ route('users.edit', $user->id) }}"
                                            class="bg-yellow-400 hover:bg-yellow-300 text-black font-semibold px-3 py-1.5 rounded-lg tooltip">
                                            Edit
                                            <span class="tooltip-text">Ubah data user</span>
                                        </a>
                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                            class="inline-block"
                                            onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="bg-red-500 hover:bg-red-400 text-white px-3 py-1.5 rounded-lg tooltip">
                                                Hapus
                                                <span class="tooltip-text">Hapus user dari sistem</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
    @vite('resources/css/admin/manage.css')

@endsection