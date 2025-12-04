@extends('layout.app')

@section('content')
    <div class="max-w-5xl mx-auto">

        <h2 class="text-3xl font-bold text-white mb-6">Manage Member</h2>

        @if (session('success'))
            <div class="bg-green-600 text-white p-3 rounded-lg mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-600 text-white p-3 rounded-lg mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- CARD FORM TAMBAH MEMBER -->
        <div class="bg-gray-800/80 border border-gray-700 rounded-2xl shadow-xl p-6 mb-10">
            <h3 class="text-xl font-semibold text-white mb-4">Tambah Member dari Pelanggan</h3>

            <form action="{{ route('member.store') }}" method="POST">
                @csrf

                {{-- PILIH PELANGGAN --}}
                <label class="text-gray-300">Pilih Pelanggan</label>
                <select name="user_id" class="mt-2 p-3 rounded-lg w-full bg-gray-900 text-white border border-gray-700"
                    required>
                    <option value="">-- Pilih Pelanggan --</option>
                    @foreach ($pelanggan as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>

                {{-- PILIH TIPE KELAS --}}
                <label class="block mt-4 text-gray-300">Tipe Kelas</label>
                <select name="tipe_kelas" class="mt-2 p-3 rounded-lg w-full bg-gray-900 text-white border border-gray-700"
                    required>
                    <option value="">-- Pilih Tipe Kelas --</option>

                    @foreach ($tipeKelasList as $tipe)
                        <option value="{{ $tipe }}">
                            {{ $tipe }}
                        </option>
                    @endforeach
                </select>

                <button type="submit"
                    class="mt-4 bg-green-600 hover:bg-green-700 px-5 py-2.5 rounded-lg font-semibold text-white">
                    + Buat Member
                </button>
            </form>
        </div>

        <!-- TABLE MEMBER -->
        <h3 class="text-2xl font-semibold text-white mb-4">Daftar Member</h3>

        <div class="overflow-hidden rounded-xl border border-gray-700 shadow-lg mb-10">
            <table class="min-w-full bg-gray-900 text-white">
                <thead class="bg-gray-800/60">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Email</th>
                        <th class="px-4 py-3 text-left">Tipe Kelas</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($members as $member)
                        <tr class="border-t border-gray-700">
                            <td class="px-4 py-3">{{ $member->id }}</td>
                            <td class="px-4 py-3">{{ $member->nama }}</td>
                            <td class="px-4 py-3">{{ $member->email }}</td>
                            <td class="px-4 py-3">{{ $member->tipe_kelas ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-3 text-center text-gray-400">
                                Belum ada member terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- ============================= -->
        <!-- BAGIAN TOKEN PACKAGE ADMIN -->
        <h3 class="text-2xl font-semibold text-white mb-4">Manage Token Packages</h3>

        <!-- FORM TAMBAH TOKEN PACKAGE -->
        <div class="bg-gray-800/80 border border-gray-700 rounded-2xl shadow-xl p-6 mb-6">
            <h4 class="text-lg font-semibold text-white mb-4">Tambah Paket Token Baru</h4>

            <form action="{{ route('token-package.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="text-gray-300">Jumlah Token</label>
                        <input type="number" name="jumlah_token"
                            class="mt-2 p-2 w-full rounded-lg bg-gray-900 text-white border border-gray-700" required>
                    </div>
                    <div>
                        <label class="text-gray-300">Tipe Kelas</label>
                        <select name="tipe_kelas"
                            class="mt-2 p-2 w-full rounded-lg bg-gray-900 text-white border border-gray-700" required>
                            <option value="">-- Pilih Tipe Kelas --</option>
                            @foreach ($tipeKelasList as $tipe)
                                <option value="{{ $tipe }}">{{ $tipe }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-gray-300">Harga</label>
                        <input type="number" name="harga"
                            class="mt-2 p-2 w-full rounded-lg bg-gray-900 text-white border border-gray-700" required>
                    </div>
                </div>
                <button type="submit"
                    class="mt-4 bg-blue-600 hover:bg-blue-700 px-5 py-2.5 rounded-lg font-semibold text-white">
                    + Tambah Paket
                </button>
            </form>
        </div>

        <!-- TABLE TOKEN PACKAGE -->
        <div class="overflow-hidden rounded-xl border border-gray-700 shadow-lg">
            <table class="min-w-full bg-gray-900 text-white">
                <thead class="bg-gray-800/60">
                    <tr>
                        <th class="px-4 py-3 text-left">ID</th>
                        <th class="px-4 py-3 text-left">Jumlah Token</th>
                        <th class="px-4 py-3 text-left">Tipe Kelas</th>
                        <th class="px-4 py-3 text-left">Harga</th>
                        <th class="px-4 py-3 text-left">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($tokenPackages as $package)
                        <tr class="border-t border-gray-700">
                            <td class="px-4 py-3">{{ $package->id }}</td>
                            <td class="px-4 py-3">{{ $package->jumlah_token }}</td>
                            <td class="px-4 py-3">{{ $package->tipe_kelas }}</td>
                            <td class="px-4 py-3">Rp {{ number_format($package->harga, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 flex gap-2">
                                <a href="{{ route('token-package.edit', $package->id) }}"
                                    class="bg-yellow-600 hover:bg-yellow-700 px-3 py-1 rounded-lg">Edit</a>
                                <form action="{{ route('token-package.destroy', $package->id) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-600 hover:bg-red-700 px-3 py-1 rounded-lg">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-3 text-center text-gray-400">
                                Belum ada paket token.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
@endsection
w
