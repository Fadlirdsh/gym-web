@extends('layout.app')

@section('title', 'Manage Voucher')

@section('content')

<style>
/* ===========================================================
   LIGHT & DARK MODE FIX — AGAR TEKS TERLIHAT JELAS
   =========================================================== */

/* Background */
.page-bg {
    background: #f1f5f9;
}
.dark .page-bg {
    background: #0f172a;
}

/* === FIX UTAMA: Judul "Manage Voucher" harus terlihat === */
.page-bg h1.page-title {
    color: #0f172a !important; /* Sangat kontras saat light mode */
}
.dark .page-bg h1.page-title {
    color: #f1f5f9 !important; /* Tegas saat dark mode */
}

/* Table Box */
.table-box {
    background: #ffffff;
    border: 1px solid #d1d5db;
}
.dark .table-box {
    background: rgba(31,41,55,0.8);
    border-color: #374151;
}

/* Table Header */
.table-header {
    background: #e2e8f0;
    color: #1e293b;
}
.dark .table-header {
    background: rgba(55,65,81,0.7);
    color: #f3f4f6;
}

/* Table Rows */
.table-row {
    transition: 0.2s ease;
}
.table-row:hover {
    background: #f8fafc;
}
.dark .table-row:hover {
    background: rgba(55,65,81,0.4);
}

/* Inputs */
.form-input {
    background: #ffffff;
    color: #1e293b;
    border: 1px solid #cbd5e1;
}
.form-input::placeholder {
    color: #64748b;
}
.dark .form-input {
    background: #1e293b;
    color: #e2e8f0;
    border-color: #475569;
}
.dark .form-input::placeholder {
    color: #94a3b8;
}

/* Modal */
.modal-bg {
    background: #ffffff;
    border: 1px solid #cbd5e1;
}
.dark .modal-bg {
    background: #1e293b;
    border-color: #475569;
}

/* Buttons */
.btn-green {
    background: #16a34a;
}
.btn-green:hover {
    background: #15803d;
}

.btn-blue {
    background: #2563eb;
}
.btn-blue:hover {
    background: #1d4ed8;
}
</style>


<div class="relative page-bg min-h-screen p-6">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="page-title text-3xl font-bold tracking-wide drop-shadow-sm">
            Manage Voucher
        </h1>

        <button id="showFormBtn"
            class="px-4 py-2 btn-green text-white rounded font-semibold shadow">
            + Tambah Voucher
        </button>
    </div>

    <!-- MODAL -->
    <div id="addVoucherModal"
        class="fixed inset-0 bg-black/60 backdrop-blur-sm flex justify-center items-center z-50 hidden opacity-0 pointer-events-none transition-opacity">

        <div id="modalBox"
            class="modal-bg rounded-xl shadow-xl w-full max-w-lg p-6 transform scale-90 transition-all max-h-[90vh] overflow-y-auto">

            <div class="flex justify-between items-center mb-5">
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Tambah Voucher</h2>
                <button id="closeModalBtn" class="text-gray-800 dark:text-gray-300 text-3xl transition">&times;</button>
            </div>

            <!-- FORM -->
            <form method="POST" action="{{ route('voucher.store') }}" class="space-y-4">
                @csrf

                <select name="kelas_id" class="form-input w-full p-3 rounded-lg">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelas as $k)
                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>

                <input type="text" name="kode" placeholder="Kode Voucher"
                       class="form-input w-full p-3 rounded-lg">

                <textarea name="deskripsi" placeholder="Deskripsi"
                          class="form-input w-full p-3 rounded-lg h-24"></textarea>

                <div class="grid grid-cols-2 gap-4">
                    <input type="number" name="diskon_persen" placeholder="Diskon (%)"
                           class="form-input w-full p-3 rounded-lg">
                    <input type="number" name="kuota" placeholder="Kuota"
                           class="form-input w-full p-3 rounded-lg">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <input type="date" name="tanggal_mulai" class="form-input w-full p-3 rounded-lg">
                    <input type="date" name="tanggal_akhir" class="form-input w-full p-3 rounded-lg">
                </div>

                <select name="role_target" class="form-input w-full p-3 rounded-lg">
                    <option value="semua">Semua</option>
                    <option value="pelanggan">Pelanggan</option>
                    <option value="member">Member</option>
                </select>

                <select name="status" class="form-input w-full p-3 rounded-lg">
                    <option value="aktif">Aktif</option>
                    <option value="nonaktif">Nonaktif</option>
                </select>

                <button type="submit"
                        class="w-full py-3 btn-blue text-white rounded-lg font-semibold shadow">
                    Simpan
                </button>
            </form>

        </div>
    </div>


    <!-- TABLE -->
    <div class="table-box p-6 rounded-xl shadow-lg mt-6">

        <table class="min-w-full table-auto">
            <thead class="table-header">
                <tr class="text-center">
                    <th class="px-4 py-2">Kode</th>
                    <th class="px-4 py-2">Deskripsi</th>
                    <th class="px-4 py-2">Target</th>
                    <th class="px-4 py-2">Diskon</th>
                    <th class="px-4 py-2">Kelas</th>
                    <th class="px-4 py-2">Berakhir</th>
                    <th class="px-4 py-2">Status</th>
                    <th class="px-4 py-2">Kuota</th>
                    <th class="px-4 py-2">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($vouchers as $voucher)
                <tr class="table-row text-center border-b border-gray-200 dark:border-gray-700">
                    <td class="px-4 py-2">{{ $voucher->kode }}</td>
                    <td class="px-4 py-2">{{ $voucher->deskripsi }}</td>
                    <td class="px-4 py-2 capitalize">{{ $voucher->role_target }}</td>
                    <td class="px-4 py-2">{{ $voucher->diskon_persen }}%</td>
                    <td class="px-4 py-2">{{ $voucher->kelas->nama_kelas ?? 'Semua Kelas' }}</td>
                    <td class="px-4 py-2">{{ $voucher->tanggal_akhir }}</td>
                    <td class="px-4 py-2 capitalize">{{ $voucher->status }}</td>
                    <td class="px-4 py-2">{{ $voucher->kuota }}</td>

                    <td class="px-4 py-2 flex justify-center gap-2">
                        <form action="{{ route('voucher.destroy', $voucher->id) }}"
                              method="POST"
                              onsubmit="return confirm('Hapus voucher ini?');">
                            @csrf
                            @method('DELETE')
                            <button class="px-2 py-1 bg-red-500 hover:bg-red-600 rounded text-white text-sm">
                                Hapus
                            </button>
                        </form>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="9" class="text-center py-4 text-gray-500 dark:text-gray-300">
                        Belum ada voucher
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>
</div>


<!-- SCRIPT -->
<script>
const showFormBtn = document.getElementById("showFormBtn");
const modal = document.getElementById("addVoucherModal");
const modalBox = document.getElementById("modalBox");
const closeModalBtn = document.getElementById("closeModalBtn");

showFormBtn.addEventListener("click", () => {
    modal.classList.remove("hidden", "pointer-events-none");
    modal.classList.add("opacity-100");
    modalBox.classList.remove("scale-90");
    modalBox.classList.add("scale-100");
});

closeModalBtn.addEventListener("click", () => {
    modal.classList.add("hidden", "pointer-events-none");
    modal.classList.remove("opacity-100");
});

modal.addEventListener("click", (e) => {
    if (e.target === modal) {
        modal.classList.add("hidden", "pointer-events-none");
        modal.classList.remove("opacity-100");
    }
});
</script>

@endsection
