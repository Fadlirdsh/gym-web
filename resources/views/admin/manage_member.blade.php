@extends('layout.app')

@section('content')
    <div class="max-w-6xl mx-auto px-4 py-10 space-y-12">

        {{-- HEADER --}}
        <div>
            <h2 class="text-3xl font-bold text-gray-800 dark:text-white">
                Manage Member
            </h2>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Kelola member dan paket token.
            </p>
        </div>

        {{-- FLASH MESSAGE --}}
        @if (session('success'))
            <div
                class="rounded-lg bg-green-100 text-green-700 border border-green-300
                    dark:bg-green-600/20 dark:text-green-300 dark:border-green-500/40
                    px-5 py-3 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div
                class="rounded-lg bg-red-100 text-red-700 border border-red-300
                    dark:bg-red-600/20 dark:text-red-300 dark:border-red-500/40
                    px-5 py-3 text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- ACTION BUTTON --}}
        <div class="flex gap-3">
            <button id="openMemberModal"
                class="bg-indigo-600 hover:bg-indigo-500 text-white
                   px-4 py-2 rounded-lg text-sm font-medium shadow">
                + Tambah Member
            </button>

            <button id="openTokenModal"
                class="bg-gray-800 hover:bg-gray-700 text-white
                   px-4 py-2 rounded-lg text-sm font-medium shadow">
                + Tambah Paket Token
            </button>
        </div>

        {{-- TABLE MEMBER --}}
        <div>
            <h3 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">
                Daftar Member
            </h3>

            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="min-w-full bg-white dark:bg-gray-900 text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr class="text-left text-gray-600 dark:text-gray-300">
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Tipe Kelas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($members as $member)
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td class="px-4 py-3">{{ $member->id }}</td>
                                <td class="px-4 py-3">{{ $member->nama }}</td>
                                <td class="px-4 py-3">{{ $member->email }}</td>
                                <td class="px-4 py-3">{{ $member->tipe_kelas ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-gray-400">
                                    Belum ada member.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- TABLE TOKEN --}}
        <div>
            <h3 class="text-xl font-semibold mb-4 text-gray-800 dark:text-white">
                Daftar Paket Token
            </h3>

            <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700">
                <table class="min-w-full bg-white dark:bg-gray-900 text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-800">
                        <tr class="text-left text-gray-600 dark:text-gray-300">
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Token</th>
                            <th class="px-4 py-3">Tipe</th>
                            <th class="px-4 py-3">Harga</th>
                            <th class="px-4 py-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tokenPackages as $package)
                            <tr class="border-t border-gray-200 dark:border-gray-700">
                                <td class="px-4 py-3">{{ $package->id }}</td>
                                <td class="px-4 py-3">{{ $package->jumlah_token }}</td>
                                <td class="px-4 py-3">{{ $package->tipe_kelas }}</td>
                                <td class="px-4 py-3">
                                    Rp {{ number_format($package->harga, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 flex gap-2">
                                    <a href="{{ route('token-package.edit', $package->id) }}"
                                        class="px-3 py-1 rounded-md bg-gray-200 dark:bg-gray-700">
                                        Edit
                                    </a>
                                    <form action="{{ route('token-package.destroy', $package->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="px-3 py-1 rounded-md bg-red-600 text-white">
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-400">
                                    Belum ada paket token.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ================= MODAL TAMBAH MEMBER ================= --}}
    <div id="memberModal"
        class="hidden fixed inset-0 z-50 items-center justify-center
            bg-black/60 backdrop-blur-sm p-4">

        <div
            class="relative w-full max-w-md
                bg-white
                rounded-2xl shadow-2xl
                border border-slate-200">

            {{-- HEADER --}}
            <div class="px-6 py-5 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-800">
                    Tambah Member
                </h3>
                <p class="text-sm text-slate-500 mt-1">
                    Pilih pelanggan dan tipe kelas
                </p>

                <button onclick="closeMemberModal()"
                    class="absolute top-4 right-4 w-9 h-9 rounded-full
                       flex items-center justify-center
                       text-slate-400 hover:text-slate-600
                       hover:bg-slate-100 transition">
                    ✕
                </button>
            </div>

            {{-- BODY --}}
            <form action="{{ route('member.store') }}" method="POST" class="px-6 py-6 space-y-5">
                @csrf

                <div>
                    <label class="text-sm font-medium text-slate-600">
                        Pelanggan
                    </label>
                    <select name="user_id"
                        class="mt-2 w-full rounded-xl
                           bg-slate-50
                           border border-slate-300
                           px-4 py-2.5 text-sm
                           text-slate-700
                           focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach ($pelanggan as $user)
                            <option value="{{ $user->id }}">
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-600">
                        Tipe Kelas
                    </label>
                    <select name="tipe_kelas"
                        class="mt-2 w-full rounded-xl
                           bg-slate-50
                           border border-slate-300
                           px-4 py-2.5 text-sm
                           text-slate-700
                           focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach ($tipeKelasList as $tipe)
                            <option value="{{ $tipe }}">{{ $tipe }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- FOOTER --}}
                <div class="pt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeMemberModal()"
                        class="px-4 py-2.5 rounded-xl text-sm
                           bg-slate-100 text-slate-600
                           hover:bg-slate-200 transition">
                        Batal
                    </button>

                    <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-sm font-semibold
                           bg-indigo-600 hover:bg-indigo-500
                           text-white shadow-lg shadow-indigo-600/30 transition">
                        Simpan Member
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ================= MODAL TAMBAH TOKEN ================= --}}
    <div id="tokenModal"
        class="hidden fixed inset-0 z-50 items-center justify-center
            bg-black/60 backdrop-blur-sm p-4">

        <div
            class="relative w-full max-w-md
                bg-white
                rounded-2xl shadow-2xl
                border border-slate-200">

            <div class="px-6 py-5 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-800">
                    Tambah Paket Token
                </h3>
                <p class="text-sm text-slate-500 mt-1">
                    Atur jumlah token dan harga
                </p>

                <button onclick="closeTokenModal()"
                    class="absolute top-4 right-4 w-9 h-9 rounded-full
                       flex items-center justify-center
                       text-slate-400 hover:text-slate-600
                       hover:bg-slate-100 transition">
                    ✕
                </button>
            </div>

            <form action="{{ route('token-package.store') }}" method="POST" class="px-6 py-6 space-y-5">
                @csrf

                <div>
                    <label class="text-sm font-medium text-slate-600">
                        Jumlah Token
                    </label>
                    <input type="number" name="jumlah_token"
                        class="mt-2 w-full rounded-xl
                           bg-slate-50
                           border border-slate-300
                           px-4 py-2.5 text-sm
                           text-slate-700
                           focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-600">
                        Tipe Kelas
                    </label>
                    <select name="tipe_kelas"
                        class="mt-2 w-full rounded-xl
                           bg-slate-50
                           border border-slate-300
                           px-4 py-2.5 text-sm
                           text-slate-700
                           focus:ring-2 focus:ring-indigo-500">
                        @foreach ($tipeKelasList as $tipe)
                            <option value="{{ $tipe }}">{{ $tipe }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="text-sm font-medium text-slate-600">
                        Harga
                    </label>
                    <input type="number" name="harga"
                        class="mt-2 w-full rounded-xl
                           bg-slate-50
                           border border-slate-300
                           px-4 py-2.5 text-sm
                           text-slate-700
                           focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="pt-6 flex justify-end gap-3">
                    <button type="button" onclick="closeTokenModal()"
                        class="px-4 py-2.5 rounded-xl
                           bg-slate-100 text-slate-600">
                        Batal
                    </button>

                    <button type="submit"
                        class="px-6 py-2.5 rounded-xl font-semibold
                           bg-indigo-600 hover:bg-indigo-500
                           text-white shadow-lg">
                        Simpan Paket
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('openMemberModal').onclick = () =>
            document.getElementById('memberModal').classList.remove('hidden');

        document.getElementById('openTokenModal').onclick = () =>
            document.getElementById('tokenModal').classList.remove('hidden');

        function closeMemberModal() {
            document.getElementById('memberModal').classList.add('hidden');
        }

        function closeTokenModal() {
            document.getElementById('tokenModal').classList.add('hidden');
        }
    </script>
@endsection
