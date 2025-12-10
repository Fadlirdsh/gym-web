@extends('layout.app')

@section('title', 'Manajemen User & Member')

@section('content')

{{-- ============================ --}}
{{-- GLOBAL FIX STYLE (LIGHT MODE) --}}
{{-- ============================ --}}
<style>

  /* Light mode card fix */
  .card-light {
    background: #ffffff !important;
    border: 1px solid rgba(0,0,0,0.08) !important;
    box-shadow: 0 4px 14px rgba(0,0,0,0.06) !important;
  }

  /* Dark mode card fix */
  .card-dark {
    background: rgba(31,41,55,0.9) !important;
    border: 1px solid rgba(148,163,184,0.2) !important;
    backdrop-filter: blur(8px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.45);
  }

  /* Input fix */
  .input-fix {
    background: #ffffff !important;
    border: 1px solid rgba(0,0,0,0.15) !important;
    color: #1e293b !important;
  }

  @media (prefers-color-scheme: dark) {
    .input-fix {
      background: #0f172a !important;
      border-color: rgba(148,163,184,0.25) !important;
      color: #e2e8f0 !important;
    }
  }

  /* Table header fix */
  .thead-light {
    background: #f1f5f9 !important;
    color: #1e293b !important;
  }

  @media (prefers-color-scheme: dark) {
    .thead-light {
      background: rgba(51,65,85,0.6) !important;
      color: #f8fafc !important;
    }
  }

  /* Divider fix */
  .border-fix {
    border-color: rgba(0,0,0,0.12) !important;
  }

  @media (prefers-color-scheme: dark) {
    .border-fix {
      border-color: rgba(148,163,184,0.2) !important;
    }
  }

  /* Modal */
  .modal-white {
    background: #ffffff !important;
    border: 1px solid rgba(0,0,0,0.12) !important;
    box-shadow: 0 8px 32px rgba(0,0,0,0.2) !important;
  }

  @media (prefers-color-scheme: dark) {
    .modal-white {
      background: #1e293b !important;
      border-color: rgba(148,163,184,0.25) !important;
    }
  }

  /* Badge fix */
  .badge-green {
    background: #d1fae5 !important;
    color: #065f46 !important;
    border: 1px solid #6ee7b7 !important;
  }

  .badge-red {
    background: #fee2e2 !important;
    color: #991b1b !important;
    border: 1px solid #fca5a5 !important;
  }

  @media (prefers-color-scheme: dark) {
    .badge-green {
      background: rgba(16,185,129,0.2) !important;
      color: #6ee7b7 !important;
      border-color: rgba(16,185,129,0.4) !important;
    }

    .badge-red {
      background: rgba(239,68,68,0.2) !important;
      color: #fca5a5 !important;
      border-color: rgba(239,68,68,0.4) !important;
    }
  }

</style>


<div class="container mx-auto px-4 py-8 space-y-10">


    {{-- SUCCESS MESSAGE --}}
    @if (session('success'))
        <div class="mb-6 rounded-lg card-light dark:card-dark
                    text-green-700 dark:text-green-300 px-4 py-3 text-sm transition">
            {{ session('success') }}
        </div>
    @endif



    <!-- ========================== -->
    <!-- FORM BUAT AKUN -->
    <!-- ========================== -->
    <div class="rounded-2xl p-6 card-light dark:card-dark transition">

        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-4 flex items-center gap-2">
            Tambah Akun
        </h2>

        <form action="{{ route('users.store') }}" method="POST" class="space-y-3">
            @csrf

            <input type="text" name="name" placeholder="Nama"
                class="p-3 rounded-lg w-full input-fix dark:input-fix" required>

            <input type="email" name="email" placeholder="Email"
                class="p-3 rounded-lg w-full input-fix dark:input-fix" required>

            <input type="password" name="password" placeholder="Password"
                class="p-3 rounded-lg w-full input-fix dark:input-fix" required>

            <input type="password" name="password_confirmation" placeholder="Konfirmasi Password"
                class="p-3 rounded-lg w-full input-fix dark:input-fix" required>


            {{-- PHONE --}}
            <div class="flex w-full">
                <span class="p-3 rounded-l-lg input-fix dark:input-fix font-semibold flex items-center border-r-0">
                    +62
                </span>

                <input type="text" name="phone" placeholder="81234567890"
                    class="p-3 rounded-r-lg w-full input-fix dark:input-fix border-l-0"
                    required pattern="[0-9]{6,15}">
            </div>

            {{-- ROLE --}}
            <select name="role"
                class="p-3 rounded-lg w-full input-fix dark:input-fix" required>
                <option value="">-- Pilih Role --</option>
                <option value="pelanggan">Pelanggan</option>
                <option value="trainer">Trainer</option>
            </select>

            <button type="submit"
                class="mt-3 bg-indigo-600 hover:bg-indigo-500 
                       px-5 py-2.5 rounded-lg font-semibold text-white shadow">
                + Buat Akun
            </button>
        </form>
    </div>

    <!-- ========================== -->
    <!-- TABLE USER -->
    <!-- ========================== -->
    <div class="rounded-2xl shadow-lg overflow-hidden card-light dark:card-dark">

        <div class="p-4 border-b border-fix flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">
                Daftar User & Member
            </h2>

            <span class="text-sm text-gray-600 dark:text-gray-400">
                {{ count($members) }} data ditemukan
            </span>
        </div>


        {{-- =========================== --}}
        {{-- FORM BUAT MEMBER --}}
        {{-- =========================== --}}
       

        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">

                <thead class="thead-light">
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
                    @forelse ($members as $index => $user)
                    <tr class="border-b border-fix hover:bg-gray-100 dark:hover:bg-gray-700/30 transition">

                        <td class="px-4 py-3">{{ $index + 1 }}</td>

                        <td class="px-4 py-3 font-semibold text-gray-800 dark:text-white">
                            {{ $user->name }}
                        </td>

                        <td class="px-4 py-3 text-gray-700 dark:text-gray-300">
                            {{ $user->email }}
                        </td>

                        <td class="px-4 py-3 capitalize text-gray-700 dark:text-gray-300">
                            {{ $user->role }}
                        </td>

                        <td class="px-4 py-3">
                            @if ($user->member)
                                <span class="px-2 py-1 rounded text-xs font-medium
                                    {{ $user->member->status === 'aktif' ? 'badge-green' : 'badge-red' }}">
                                    {{ ucfirst($user->member->status) }}
                                </span>
                            @else
                                <span class="text-gray-500 dark:text-gray-400 text-xs italic">
                                    Belum jadi member
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-center space-x-2">

                            {{-- Edit --}}
                            <button onclick="openEditModal({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}')"
                                class="bg-yellow-400 hover:bg-yellow-300 text-black font-semibold 
                                       px-3 py-1.5 rounded-lg transition">
                                Edit
                            </button>

                            {{-- Delete --}}
                            <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                class="inline-block"
                                onsubmit="return confirm('Yakin ingin menghapus pengguna ini?')">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    class="bg-red-500 hover:bg-red-400 text-white font-semibold 
                                           px-3 py-1.5 rounded-lg transition">
                                    Hapus
                                </button>
                            </form>

                        </td>

                    </tr>
                    @empty

                    <tr>
                        <td colspan="6" class="text-center text-gray-600 dark:text-gray-400 py-6">
                            Belum ada pelanggan terdaftar.
                        </td>
                    </tr>

                    @endforelse
                </tbody>

            </table>
        </div>
    </div>



    <!-- ========================== -->
    <!-- MODAL EDIT USER -->
    <!-- ========================== -->
    <div id="editModal" class="fixed inset-0 bg-black/60 z-50 hidden">
        <div class="flex items-center justify-center w-full h-full">
            <div class="modal-white p-6 rounded-xl w-full max-w-lg transition">

                <h2 class="text-xl font-semibold text-gray-800 dark:text-white mb-4">
                    Edit User
                </h2>

                <form id="editForm" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="text-gray-700 dark:text-gray-300">Nama</label>
                        <input id="editName" type="text" name="name"
                            class="w-full input-fix dark:input-fix px-3 py-2 rounded-lg" required />
                    </div>

                    <div class="mb-3">
                        <label class="text-gray-700 dark:text-gray-300">Email</label>
                        <input id="editEmail" type="email" name="email"
                            class="w-full input-fix dark:input-fix px-3 py-2 rounded-lg" required />
                    </div>

                    <div class="flex justify-end gap-2 mt-6">
                        <button type="button" onclick="closeModal()"
                            class="px-4 py-2 bg-gray-300 dark:bg-gray-700 
                                   dark:text-white rounded-lg">
                            Batal
                        </button>

                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-500 
                                   text-white rounded-lg">
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
