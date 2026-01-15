@extends('layout.app')

@section('title', 'Manage Member')

@section('content')

    <div class="container mx-auto px-4 py-8 space-y-10">

        {{-- SUCCESS --}}
        @if (session('success'))
            <div class="p-4 rounded-xl bg-green-100 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        {{-- FORM TAMBAH MEMBER --}}
        <div class="card p-6">
            <h2 class="text-xl font-semibold mb-4">Tambah Member dari Pelanggan</h2>

            <form action="{{ route('member.store') }}" method="POST">
                @csrf
                <select name="user_id" class="input-fix w-full mb-4" required>
                    <option value="">-- Pilih Pelanggan --</option>
                    @foreach ($pelanggan as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>

                <button class="bg-green-600 text-white px-4 py-2 rounded">
                    + Buat Member
                </button>
            </form>
        </div>

        {{-- TABLE MEMBER --}}
        <div class="card overflow-hidden">
            <div class="p-4 border-b flex justify-between">
                <h2 class="font-semibold">Daftar Member</h2>
                <span class="text-sm">{{ count($members) }} data</span>
            </div>

            <table class="min-w-full text-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($members as $member)
                        <tr class="border-t">
                            <td>{{ $member->id }}</td>
                            <td>{{ $member->user->name }}</td>
                            <td>{{ $member->user->email }}</td>
                            <td>
                                @if ($member->status === 'aktif')
                                    <span class="text-green-600 font-semibold">Aktif</span>
                                @else
                                    <span class="text-red-500">{{ ucfirst($member->status) }}</span>
                                @endif
                            </td>
                            <td>
                                {{-- PENDING → ACTIVATE --}}
                                @if ($member->status === 'pending')
                                    <button
                                        onclick="openActivateModal({{ $member->user_id }}, '{{ $member->user->name }}')"
                                        class="bg-orange-500 text-white px-3 py-1 rounded">
                                        Activate
                                    </button>

                                    {{-- AKTIF → TOPUP --}}
                                @elseif ($member->status === 'aktif')
                                    <button onclick="openTopupModal({{ $member->id }}, '{{ $member->user->name }}')"
                                        class="bg-blue-600 text-white px-3 py-1 rounded">
                                        Top Up Token
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>

    {{-- MODAL ACTIVATE --}}
    <div id="activateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white p-6 rounded w-full max-w-md">
            <h3 class="text-lg font-semibold mb-3">
                Konfirmasi Aktivasi Member
            </h3>

            <p class="mb-4">
                Yakin ingin mengaktifkan membership untuk
                <strong id="activateName"></strong> selama 1 bulan?
            </p>

            <form action="{{ route('admin.cash.member') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" id="activateUserId">

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeActivateModal()" class="px-4 py-2 border rounded">
                        Batal
                    </button>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">
                        Ya, Aktifkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- MODAL TOPUP TOKEN (TETAP) --}}
    <div id="topupModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white p-6 rounded w-full max-w-md">
            <h3 class="text-lg font-semibold mb-4">
                Top Up Token untuk <span id="memberName"></span>
            </h3>

            <form action="{{ route('admin.cash.token') }}" method="POST">
                @csrf
                <input type="hidden" name="member_id" id="memberId">

                <select name="token_package_id" class="input-fix w-full mb-4" required>
                    <option value="">-- Pilih Paket Token --</option>
                    @foreach ($tokenPackages as $package)
                        <option value="{{ $package->id }}">
                            {{ $package->nama_paket }} — {{ $package->jumlah_token }} token
                        </option>
                    @endforeach
                </select>

                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeTopupModal()" class="border px-4 py-2 rounded">
                        Batal
                    </button>
                    <button class="bg-blue-600 text-white px-4 py-2 rounded">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- SCRIPT --}}
    <script>
        function openActivateModal(userId, name) {
            document.getElementById('activateUserId').value = userId;
            document.getElementById('activateName').innerText = name;
            document.getElementById('activateModal').classList.remove('hidden');
            document.getElementById('activateModal').classList.add('flex');
        }

        function closeActivateModal() {
            document.getElementById('activateModal').classList.add('hidden');
        }

        function openTopupModal(id, name) {
            document.getElementById('memberId').value = id;
            document.getElementById('memberName').innerText = name;
            document.getElementById('topupModal').classList.remove('hidden');
            document.getElementById('topupModal').classList.add('flex');
        }

        function closeTopupModal() {
            document.getElementById('topupModal').classList.add('hidden');
        }
    </script>

    @vite('resources/css/admin/manage_member.css')
@endsection
