@extends('layout.app')

@section('title', 'Manajemen Diskon')

@section('content')

<style>
/* ==========================================
   MODERN UI - Glassmorphism + Dark/Light Mode
========================================== */
.page-bg {
    background: linear-gradient(180deg,#f0f4f8 0%,#e2e8f0 100%);
    color:#1e293b;
    min-height:100vh;
    font-family:'Inter',sans-serif;
    transition:all 0.3s;
}
.dark .page-bg {
    background: linear-gradient(180deg,#0f172a 0%,#1e293b 100%);
    color:#e5e7eb;
}
.page-title { font-size:2.25rem; font-weight:700; letter-spacing:-0.5px; }
.card-box {
    background: rgba(255,255,255,0.85);
    border-radius:20px;
    padding:2rem;
    box-shadow:0 10px 30px rgba(0,0,0,0.08);
    backdrop-filter: blur(12px);
    transition:all 0.3s;
}
.dark .card-box { background: rgba(31,41,55,0.85); box-shadow:0 10px 30px rgba(0,0,0,0.5); }

/* ===== MODERN SEARCH BAR ===== */
#searchInputWrapper { position:relative; width:100%; max-width:400px; }
#searchInput {
    width:100%;
    padding:0.75rem 1.5rem 0.75rem 0.75rem;
    border-radius:9999px;
    border:1px solid rgba(0,0,0,0.15);
    background: rgba(255,255,255,0.95);
    font-size:0.95rem;
    box-shadow:0 4px 12px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}
#searchInput:focus { 
    outline:none;
    border-color:#3b82f6;
    box-shadow:0 4px 15px rgba(59,130,246,0.3);
    background-color: rgba(255,255,255,1);
}
#searchInputWrapper label {
    position:absolute;
    left:0.75rem;
    top:50%;
    transform:translateY(-50%);
    color:#64748b;
    pointer-events:none;
    transition: all 0.2s ease;
}
#searchInput:focus + label,
#searchInput:not(:placeholder-shown) + label {
    top:-0.5rem;
    font-size:0.75rem;
    color:#3b82f6;
    background:#fff;
    padding:0 0.25rem;
}
#btnClearSearch {
    position:absolute;
    right:0.75rem;
    top:50%;
    transform:translateY(-50%);
    background:none;
    border:none;
    font-size:1rem;
    color:#64748b;
    cursor:pointer;
    display:none;
}
#btnClearSearch:hover { color:#1e3a8a; }

button { border-radius:12px; font-weight:600; padding:0.5rem 1.5rem; transition:all 0.2s; }
button:hover { transform:translateY(-1px); box-shadow:0 8px 25px rgba(0,0,0,0.1); }
.btn-primary { background:#3b82f6; color:#fff; } 
.btn-primary:hover { background:#2563eb; }

/* ===== TABLE ===== */
.table-wrapper { overflow-x:auto; }
table { width:100%; border-collapse:separate; border-spacing:0; font-size:0.95rem; }
table th, table td { padding:0.75rem 1rem; text-align:center; transition: all 0.2s; }
table tr:hover { background: rgba(59,130,246,0.05); border-radius:12px; }
.table-header { background: rgba(248,250,252,0.9); color:#1e293b; text-transform:uppercase; letter-spacing:0.05em; font-weight:600; }
.badge-blue { background: rgba(59,130,246,0.2); color:#1e3a8a; border-radius:12px; padding:0.25rem 0.5rem; font-weight:600; font-size:0.75rem; }

/* ===== MODALS ===== */
.modal-backdrop { position:fixed; inset:0; background:rgba(0,0,0,0.6); backdrop-filter:blur(5px); display:none; align-items:center; justify-content:center; z-index:50; transition: opacity 0.3s; }
.modal-backdrop.show { display:flex; }
.modal-bg { background: rgba(255,255,255,0.95); border-radius: 20px; padding: 2rem; max-width:480px; width:95%; max-height:90vh; overflow-y:auto; box-shadow: 0 12px 40px rgba(0,0,0,0.2); backdrop-filter: blur(12px); transform: translateY(-30px); opacity:0; transition: all 0.3s ease; position: relative; }
.modal-backdrop.show .modal-bg { transform:translateY(0); opacity:1; }
.modal-close { position:absolute; top:1rem; right:1rem; font-size:1.25rem; color:#374151; background:none; border:none; }
.modal-close:hover { color:#ef4444; cursor:pointer; }

/* ===== TOAST ===== */
.toast { position:fixed; top:1rem; right:1rem; background:rgba(51,65,85,0.9); color:#fff; padding:0.75rem 1.25rem; border-radius:16px; font-weight:600; z-index:9999; opacity:0; transform:translateY(-20px); transition:all 0.3s; }
.toast.show{ opacity:1; transform:translateY(0); }

/* ===== SMOOTH ROW HIGHLIGHT ===== */
@keyframes highlightRow {
    0% { background-color: rgba(59,130,246,0.4); }
    50% { background-color: rgba(59,130,246,0.2); }
    100% { background-color: transparent; }
}
.highlight { animation: highlightRow 1s ease forwards; }

@media(max-width:640px){
    table thead{display:none;} 
    table tbody tr{display:block; padding:1rem; margin-bottom:1rem; box-shadow:0 4px 20px rgba(0,0,0,0.05); border-radius:12px;} 
    table tbody td{display:flex; justify-content:space-between; padding:0.5rem 0; font-size:0.9rem;} 
    table tbody td::before{content:attr(data-label); font-weight:600; color:#64748b; flex-basis:45%; text-align:left;} 
}

@media(prefers-color-scheme:dark){
    .page-bg { background: linear-gradient(180deg,#0f172a 0%,#1e293b 100%); color:#e5e7eb; }
    .card-box { background: rgba(31,41,55,0.85); box-shadow:0 10px 30px rgba(0,0,0,0.5); }
    #searchInput { background: rgba(255,255,255,0.08); color:#f1f5f9; border-color: rgba(255,255,255,0.25); }
    #searchInput:focus { background: rgba(255,255,255,0.12); border-color:#60a5fa; box-shadow:0 0 0 4px rgba(99,102,241,0.3); }
    table tr:hover { background: rgba(255,255,255,0.05); }
    .table-header { background: rgba(51,65,85,0.6); color:#e2e8f0; }
    .badge-blue { background: rgba(59,130,246,0.3); color:#bfdbfe; }
    .modal-bg { background: rgba(31,41,55,0.95); }
}
</style>

<div class="page-bg px-6 py-8">
    <div class="container mx-auto space-y-8">
        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row justify-between items-center mb-8">
            <h1 class="page-title">💰 Manage Diskon</h1>
            <div class="flex flex-col sm:flex-row gap-3 mt-3 sm:mt-0">
                <div id="searchInputWrapper">
                    <input type="text" id="searchInput" placeholder=" " class="input-box">
                    <label for="searchInput">Cari Diskon / Kelas</label>
                    <button id="btnClearSearch">&times;</button>
                </div>
                <button id="btnOpenCreate" class="btn-primary flex items-center gap-2">➕ Tambah Diskon</button>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="card-box table-wrapper rounded-3xl">
            <table id="diskonTable">
                <thead class="table-header">
                    <tr>
                        <th>ID</th><th>Kelas</th><th>Nama Diskon</th><th>Persentase</th><th>Mulai</th><th>Berakhir</th><th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($diskons as $diskon)
                    <tr>
                        <td data-label="ID">{{ $diskon->id }}</td>
                        <td data-label="Kelas">{{ $diskon->kelas->nama_kelas ?? '-' }}</td>
                        <td data-label="Nama Diskon">{{ $diskon->nama_diskon }}</td>
                        <td data-label="Persentase"><span class="badge-blue">{{ $diskon->persentase }}%</span></td>
                        <td data-label="Mulai">{{ \Carbon\Carbon::parse($diskon->tanggal_mulai)->format('d M Y') }}</td>
                        <td data-label="Berakhir">{{ \Carbon\Carbon::parse($diskon->tanggal_berakhir)->format('d M Y') }}</td>
                        <td data-label="Aksi" class="flex justify-center gap-2">
                            <button class="btnOpenEdit bg-yellow-500 hover:bg-yellow-600 text-gray-900 px-3 py-1 rounded-lg shadow transition"
                                data-id="{{ $diskon->id }}"
                                data-kelas_id="{{ $diskon->kelas_id }}"
                                data-nama_diskon="{{ $diskon->nama_diskon }}"
                                data-persentase="{{ $diskon->persentase }}"
                                data-tanggal_mulai="{{ $diskon->tanggal_mulai }}"
                                data-tanggal_berakhir="{{ $diskon->tanggal_berakhir }}">
                                ✏️ Edit
                            </button>
                            <form action="{{ route('diskon.destroy', $diskon->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg shadow transition" onclick="return confirm('Yakin ingin menghapus diskon ini?')">🗑️ Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-6 italic text-gray-500 dark:text-gray-400">🚫 Belum ada data diskon</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL CREATE --}}
<div id="modalCreate" class="modal-backdrop">
    <div class="modal-bg">
        <button class="modal-close">&times;</button>
        <h2 class="text-2xl font-bold mb-4">Tambah Diskon</h2>
        <form id="formCreateDiskon" action="{{ route('diskon.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label>Pilih Kelas</label>
                <select name="kelas_id" class="w-full input-box rounded-lg px-3 py-2">
                    @foreach ($kelas as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Nama Diskon</label>
                <input type="text" name="nama_diskon" class="w-full input-box rounded-lg px-3 py-2" required>
            </div>
            <div>
                <label>Persentase (%)</label>
                <input type="number" name="persentase" class="w-full input-box rounded-lg px-3 py-2" min="1" max="100" required>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label>Mulai</label>
                    <input type="date" name="tanggal_mulai" class="w-full input-box rounded-lg px-3 py-2" required>
                </div>
                <div>
                    <label>Berakhir</label>
                    <input type="date" name="tanggal_berakhir" class="w-full input-box rounded-lg px-3 py-2" required>
                </div>
            </div>
            <div class="flex justify-between mt-5">
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-semibold">💾 Simpan</button>
                <button type="button" id="btnCloseCreate" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-semibold">❌ Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div id="modalEdit" class="modal-backdrop">
    <div class="modal-bg">
        <button class="modal-close">&times;</button>
        <h2 class="text-2xl font-bold mb-4">Edit Diskon</h2>
        <form id="formEditDiskon" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="editId">
            <div>
                <label>Pilih Kelas</label>
                <select name="kelas_id" id="editKelasId" class="w-full input-box rounded-lg px-3 py-2">
                    @foreach ($kelas as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label>Nama Diskon</label>
                <input type="text" id="editNamaDiskon" name="nama_diskon" class="w-full input-box rounded-lg px-3 py-2" required>
            </div>
            <div>
                <label>Persentase (%)</label>
                <input type="number" id="editPersentase" name="persentase" class="w-full input-box rounded-lg px-3 py-2" min="1" max="100" required>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label>Mulai</label>
                    <input type="date" id="editTanggalMulai" name="tanggal_mulai" class="w-full input-box rounded-lg px-3 py-2" required>
                </div>
                <div>
                    <label>Berakhir</label>
                    <input type="date" id="editTanggalBerakhir" name="tanggal_berakhir" class="w-full input-box rounded-lg px-3 py-2" required>
                </div>
            </div>
            <div class="flex justify-between mt-5">
                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-gray-900 px-4 py-2 rounded-lg font-semibold">🔄 Update</button>
                <button type="button" id="btnCloseEdit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-semibold">❌ Batal</button>
            </div>
        </form>
    </div>
</div>

{{-- TOAST --}}
<div id="toast" class="toast">✅ Aksi berhasil</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const modalCreate = document.getElementById('modalCreate');
    const btnOpenCreate = document.getElementById('btnOpenCreate');
    const btnCloseCreate = document.getElementById('btnCloseCreate');
    const modalEdit = document.getElementById('modalEdit');
    const btnCloseEdit = document.getElementById('btnCloseEdit');
    const editButtons = document.querySelectorAll('.btnOpenEdit');
    const searchInput = document.getElementById('searchInput');
    const btnClearSearch = document.getElementById('btnClearSearch');
    const tableRows = document.querySelectorAll('#diskonTable tbody tr');
    const toast = document.getElementById('toast');

    function openModal(modal){ if(!modal) return; modal.classList.add('show'); }
    function closeModal(modal){ if(!modal) return; modal.classList.remove('show'); }

    // MODAL CREATE
    btnOpenCreate?.addEventListener('click',()=>openModal(modalCreate));
    btnCloseCreate?.addEventListener('click',()=>closeModal(modalCreate));
    modalCreate?.querySelector('.modal-close')?.addEventListener('click',()=>closeModal(modalCreate));
    modalCreate?.addEventListener('click',(e)=>{ if(e.target===modalCreate) closeModal(modalCreate); });

    // MODAL EDIT
    editButtons.forEach(btn=>{
        btn.addEventListener('click',()=>{
            document.getElementById('editId').value=btn.dataset.id;
            document.getElementById('editKelasId').value=btn.dataset.kelas_id;
            document.getElementById('editNamaDiskon').value=btn.dataset.nama_diskon;
            document.getElementById('editPersentase').value=btn.dataset.persentase;
            document.getElementById('editTanggalMulai').value=btn.dataset.tanggal_mulai;
            document.getElementById('editTanggalBerakhir').value=btn.dataset.tanggal_berakhir;
            document.getElementById('formEditDiskon').action=`/admin/diskon/${btn.dataset.id}`;
            openModal(modalEdit);
        });
    });
    btnCloseEdit?.addEventListener('click',()=>closeModal(modalEdit));
    modalEdit?.querySelector('.modal-close')?.addEventListener('click',()=>closeModal(modalEdit));
    modalEdit?.addEventListener('click',(e)=>{ if(e.target===modalEdit) closeModal(modalEdit); });

    // SEARCH BAR
    searchInput?.addEventListener('input',()=>{
        const val = searchInput.value.toLowerCase();
        btnClearSearch.style.display = val ? 'block' : 'none';
        tableRows.forEach(row=>{ row.style.display = row.textContent.toLowerCase().includes(val)?'':'none'; });
    });
    btnClearSearch?.addEventListener('click',()=>{
        searchInput.value=''; btnClearSearch.style.display='none'; tableRows.forEach(row=>row.style.display='');
    });

    // TOAST & ROW HIGHLIGHT
    window.showToast = (msg='✅ Aksi berhasil', row=null)=>{
        if(!toast) return;
        toast.textContent = msg;
        toast.classList.add('show');
        setTimeout(()=>toast.classList.remove('show'),3000);

        if(row){
            row.classList.remove('highlight');
            void row.offsetWidth; // trigger reflow
            row.classList.add('highlight');
        }
    };

    // FRONT-END VALIDASI PERSENTASE
    const formCreate = document.getElementById('formCreateDiskon');
    const inputPersentase = formCreate?.querySelector('input[name="persentase"]');
    formCreate?.addEventListener('submit', (e)=>{
        const val = parseInt(inputPersentase.value);
        if(val<1||val>100){ e.preventDefault(); alert('Persentase harus antara 1–100'); }
    });
});
</script>

@endsection
