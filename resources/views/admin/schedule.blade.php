@extends('layout.app')

@section('title', 'Manajemen Jadwal Trainer')

@section('content')

<style>
/* =============================
   UNIVERSAL PREMIUM UI STYLE
   Light & Dark Theme Compatible
============================= */

/* ===== BODY ===== */
body {
    transition: background 0.3s, color 0.3s;
}

/* ===== CARD ===== */
.card {
    background-color: #f8fafc;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    backdrop-filter: blur(8px);
    padding: 1.5rem;
    transition: all 0.3s;
}
.card:hover { transform: translateY(-2px) scale(1.01); }
@media (prefers-color-scheme: dark) {
    .card {
        background-color: rgba(31,41,55,0.85);
        box-shadow: 0 8px 30px rgba(0,0,0,0.5);
        backdrop-filter: blur(12px);
    }
}

/* ===== INPUT & SELECT ===== */
.input-field {
    background-color: rgba(255,255,255,0.85);
    border: 1px solid rgba(0,0,0,0.15);
    border-radius: 12px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    width: 100%;
    transition: all 0.3s;
    color: #111827;
}
.input-field:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79,70,229,0.25);
    outline: none;
}
select.input-field {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
}
@media (prefers-color-scheme: dark) {
    .input-field {
        background-color: rgba(255,255,255,0.15);
        color: #f1f5f9;
        border: 1px solid rgba(255,255,255,0.3);
    }
    .input-field:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.4);
    }
    select.input-field option {
        background-color: rgba(31,41,55,0.95);
        color: #f1f5f9;
    }
}

/* ===== BUTTON ===== */
.btn {
    border-radius: 10px;
    font-weight: 600;
    padding: 0.75rem 1.5rem;
    transition: all 0.25s ease;
    position: relative;
    overflow: hidden;
    white-space: nowrap;
}
.btn::after {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(120deg, rgba(255,255,255,0.15), rgba(255,255,255,0));
    transform: translateX(-100%);
    transition: transform 0.4s ease;
}
.btn:hover::after { transform: translateX(0); }
.btn:hover { transform: translateY(-1px) scale(1.02); }

.btn-primary { background-color: #4f46e5; color: #fff; }
.btn-primary:hover { background-color: #6366f1; }
.btn-red { background-color: #ef4444; color: #fff; }
.btn-red:hover { background-color: #f87171; }
.btn-gray { background-color: #9ca3af; color: #fff; }
.btn-gray:hover { background-color: #6b7280; }

/* ===== TABLE ===== */
.table-container {
    overflow-x: auto;
    border-radius: 16px;
    border: 1px solid rgba(0,0,0,0.1);
}
table {
    border-collapse: separate;
    border-spacing: 0;
    width: 100%;
    transition: all 0.3s;
}
thead { background: #f8fafc; }
th, td { padding: 0.75rem 1rem; text-align: left; }
tr { transition: all 0.25s ease; border-radius: 12px; }
tr:hover { background-color: rgba(0,0,0,0.05); transform: scale(1.01); }
@media (prefers-color-scheme: dark) {
    thead { background: rgba(51,65,85,0.6); color: #e2e8f0; }
    tr:hover { background-color: rgba(255,255,255,0.08); }
}

/* ===== BADGES ===== */
.badge-green { background-color: rgba(16,185,129,0.2); color: #10b981; border-radius: 8px; padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight: 600; }
.badge-red { background-color: rgba(239,68,68,0.2); color: #ef4444; border-radius: 8px; padding: 0.25rem 0.5rem; font-size: 0.75rem; font-weight: 600; }

/* ===== MODAL ===== */
.modal-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.45);
    backdrop-filter: blur(6px);
    justify-content: center;
    align-items: center;
    z-index: 50;
    opacity: 0;
    transition: opacity 0.3s ease;
}
.modal-overlay.flex { 
    display: flex; 
    opacity: 1; 
}

.modal-box {
    background: rgba(255,255,255,0.9);
    backdrop-filter: blur(12px);
    border-radius: 20px;
    padding: 24px 26px;
    border: 1px solid rgba(255,255,255,0.3);
    box-shadow: 0 12px 40px rgba(0,0,0,0.25);
    transform: scale(0.9);
    opacity: 0;
    transition: all 0.35s ease;
    max-height: 90vh;
    overflow-y: auto;
    width: 450px;
    max-width: 95%;
}
.modal-overlay.flex .modal-box { 
    transform: scale(1); 
    opacity: 1; 
}

@media (prefers-color-scheme: dark) {
    .modal-box {
        background: rgba(31,41,55,0.95);
        border: 1px solid rgba(255,255,255,0.25);
        color: #f1f5f9;
    }
}

/* Close button */
.close-btn {
    position: absolute;
    top: 16px;
    right: 16px;
    font-size: 1.3rem;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    padding: 6px 10px;
    transition: 0.25s ease;
    cursor: pointer;
}
.close-btn:hover {
    color: #ef4444;
    transform: scale(1.2);
    background: rgba(255,255,255,0.35);
}

/* Form inside modal */
.modal-box form {
    display: flex;
    flex-direction: column;
}
.modal-box form label {
    margin-top: 12px;
    margin-bottom: 6px;
    font-weight: 600;
}
.modal-box form .input-field {
    margin-bottom: 12px;
}
.modal-box form button[type="submit"] {
    margin-top: 16px;
}

/* TOOLTIP */
.tooltip { position: relative; display: inline-block; cursor: pointer; }
.tooltip .tooltip-text {
    visibility: hidden;
    width: max-content;
    max-width: 220px;
    background-color: rgba(0,0,0,0.85);
    color: #fff;
    text-align: center;
    border-radius: 8px;
    padding: 6px 10px;
    position: absolute;
    z-index: 100;
    bottom: 125%;
    left: 50%;
    transform: translateX(-50%) translateY(5px);
    opacity: 0;
    transition: opacity 0.25s ease, transform 0.25s ease;
    font-size: 0.75rem;
    pointer-events: none;
}
.tooltip:hover .tooltip-text { visibility: visible; opacity: 1; transform: translateX(-50%) translateY(0); }

/* MOBILE TABLE RESPONSIVE */
@media (max-width: 640px) {
    table thead { display: none; }
    table tbody tr {
        display: block;
        margin-bottom: 16px;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        padding: 14px;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    table tbody td {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        font-size: 14px;
    }
    table tbody td::before {
        content: attr(data-label);
        font-weight: 600;
        color: #64748b;
        flex-basis: 45%;
    }
}

/* ===== FILTER GRID ===== */
.filter-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    align-items: flex-end;
}
.filter-grid > div {
    flex: 1 1 150px;
}
.filter-grid .btn {
    flex: 1 1 120px;
}
@media (max-width: 640px) {
    .filter-grid {
        flex-direction: column;
        align-items: stretch;
    }
    .filter-grid .btn {
        width: 100%;
    }
}
</style>

<div class="min-h-screen px-6 py-8 bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-gray-100">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
        <h1 class="text-3xl font-bold">Manajemen Jadwal Trainer</h1>
        <div class="flex gap-3 flex-wrap">
            <a href="{{ route('schedules.exportPDF', request()->all()) }}" class="btn btn-red tooltip w-full sm:w-auto">
                Export PDF
                <span class="tooltip-text">Unduh jadwal sebagai PDF</span>
            </a>
            <button onclick="openModalTambah()" class="btn btn-primary tooltip w-full sm:w-auto">
                + Tambah Jadwal
                <span class="tooltip-text">Tambah jadwal baru</span>
            </button>
        </div>
    </div>

    {{-- FILTER --}}
    <form method="GET" class="card mb-6 p-4">
        <div class="filter-grid">
            <div class="flex flex-col">
                <label class="font-semibold mb-1">Hari</label>
                <select name="day" class="input-field">
                    <option value="">Semua</option>
                    @foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $d)
                        <option value="{{ $d }}" {{ request('day') == $d ? 'selected' : '' }}>{{ ucfirst($d) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col">
                <label class="font-semibold mb-1">Trainer</label>
                <select name="trainer_id" class="input-field">
                    <option value="">Semua</option>
                    @foreach ($trainers as $t)
                        <option value="{{ $t->id }}" {{ request('trainer_id') == $t->id ? 'selected' : '' }}>{{ $t->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex flex-col">
                <label class="font-semibold mb-1">Jam Mulai</label>
                <select name="start_time" class="input-field">
                    <option value="">Pilih jam mulai</option>
                    @foreach (range(7,19) as $h)
                        <option value="{{ sprintf('%02d:00:00', $h) }}" {{ request('start_time') == sprintf('%02d:00:00', $h) ? 'selected' : '' }}>{{ $h }}:00</option>
                        <option value="{{ sprintf('%02d:30:00', $h) }}" {{ request('start_time') == sprintf('%02d:30:00', $h) ? 'selected' : '' }}>{{ $h }}:30</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary h-[42px]">Filter</button>
            <a href="{{ route('schedules.index') }}" class="btn btn-gray h-[42px]">Reset</a>
        </div>
    </form>

    {{-- TABLE --}}
    <div class="table-container p-4">
        <table id="scheduleTable">
            <thead>
                <tr>
                    <th>Kelas</th>
                    <th>Trainer</th>
                    <th>Hari</th>
                    <th>Jam</th>
                    <th>Fokus</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($schedules as $s)
                <tr>
                    <td data-label="Kelas">{{ $s->kelas->nama_kelas }}</td>
                    <td data-label="Trainer">{{ $s->trainer->name }}</td>
                    <td data-label="Hari">{{ $s->day }}</td>
                    <td data-label="Jam">{{ $s->start_time }} - {{ $s->end_time }}</td>
                    <td data-label="Fokus">{{ $s->class_focus ?? '-' }}</td>
                    <td data-label="Status">
                        @if ($s->is_active)
                            <span class="badge-green">Aktif</span>
                        @else
                            <span class="badge-red">Nonaktif</span>
                        @endif
                    </td>
                    <td data-label="Aksi" class="flex gap-2 pt-3 flex-wrap">
                        <button onclick="editSchedule({{ $s->id }})" class="btn btn-primary !py-1 !px-3 tooltip">
                            Edit
                            <span class="tooltip-text">Edit jadwal</span>
                        </button>
                        <button onclick="deleteSchedule({{ $s->id }})" class="btn btn-red !py-1 !px-3 tooltip">
                            Hapus
                            <span class="tooltip-text">Hapus jadwal</span>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL --}}
<div id="modalSchedule" class="modal-overlay">
    <div class="modal-box relative">
        <button onclick="closeModal()" class="close-btn">✕</button>
        <h2 id="modalTitle" class="text-xl font-bold mb-4">Tambah Jadwal</h2>
        <form id="scheduleForm">
            @csrf
            <input type="hidden" id="schedule_id">

            <label>Kelas</label>
            <select id="kelas_id" class="input-field">
                @foreach ($kelas as $k)
                    <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                @endforeach
            </select>

            <label>Trainer</label>
            <select id="trainer_id" class="input-field">
                @foreach ($trainers as $t)
                    <option value="{{ $t->id }}">{{ $t->name }}</option>
                @endforeach
            </select>

            <label>Hari</label>
            <select id="day" class="input-field">
                <option value="">Semua</option>
                @foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $d)
                    <option value="{{ $d }}">{{ ucfirst($d) }}</option>
                @endforeach
            </select>

            <label>Jam Mulai</label>
            <select id="start_time" class="input-field">
                @foreach (range(7,19) as $h)
                    <option value="{{ sprintf('%02d:00:00', $h) }}">{{ $h }}:00</option>
                    <option value="{{ sprintf('%02d:30:00', $h) }}">{{ $h }}:30</option>
                @endforeach
            </select>

            <label>Jam Selesai</label>
            <select id="end_time" class="input-field">
                @foreach (range(7,19) as $h)
                    <option value="{{ sprintf('%02d:00:00', $h) }}">{{ $h }}:00</option>
                    <option value="{{ sprintf('%02d:30:00', $h) }}">{{ $h }}:30</option>
                @endforeach
            </select>

            <label>Fokus Kelas</label>
            <input type="text" id="class_focus" class="input-field">

            <label>Status</label>
            <select id="is_active" class="input-field">
                <option value="1">Aktif</option>
                <option value="0">Nonaktif</option>
            </select>

            <button type="submit" class="btn btn-primary w-full mt-2">Simpan</button>
        </form>
    </div>
</div>

@vite('resources/js/schedule.js')

@endsection
