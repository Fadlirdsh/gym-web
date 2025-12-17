@extends('layout.app')

@section('title', 'Edit User')

@section('content')

<style>
/* =========================================================
   UNIVERSAL UI MODERN - Light & Dark - Glassmorphism Style
   ========================================================= */

/* Card */
.card-premium {
    background-color: #f8fafc;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    backdrop-filter: blur(8px);
    transition: background 0.3s, box-shadow 0.3s, transform 0.2s;
    padding: 3rem;
}
.card-premium:hover {
    transform: translateY(-2px);
}
@media (prefers-color-scheme: dark) {
    .card-premium {
        background-color: rgba(31,41,55,0.8);
        box-shadow: 0 8px 30px rgba(0,0,0,0.5);
        backdrop-filter: blur(12px);
    }
}

/* Input & Select */
.floating-input, select.floating-input {
    background-color: rgba(255,255,255,0.85);
    border: 1px solid rgba(0,0,0,0.15);
    border-radius: 12px;
    padding: 0.75rem 1rem;
    color: #1e293b;
    font-size: 1rem;
    width: 100%;
    transition: all 0.3s;
}
.floating-input:focus, select.floating-input:focus {
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79,70,229,0.25);
    outline: none;
}
@media (prefers-color-scheme: dark) {
    .floating-input, select.floating-input {
        background-color: rgba(255,255,255,0.08);
        color: #f1f5f9;
        border: 1px solid rgba(255,255,255,0.25);
    }
    .floating-input:focus, select.floating-input:focus {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99,102,241,0.3);
    }
}

/* Floating Label */
.floating-group {
    position: relative;
    margin-bottom: 1.5rem;
}
.floating-label {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #64748b;
    font-size: 1rem;
    pointer-events: none;
    transition: 0.25s ease;
}
.floating-input:focus + .floating-label,
.floating-input:not(:placeholder-shown) + .floating-label,
select.floating-input:focus + .floating-label,
select.floating-input:not([value=""]) + .floating-label {
    top: 0.25rem;
    font-size: 0.75rem;
    color: #4f46e5;
}

/* Eye Icon */
.eye-btn {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.2rem;
    color: #9ca3af;
    cursor: pointer;
    transition: 0.2s;
}
.eye-btn:hover { color: #1e293b; }

/* Buttons */
.btn-primary {
    background: #4f46e5;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.25s ease, transform 0.2s ease;
}
.btn-primary:hover { background: #4338ca; transform: scale(1.02); }

.btn-secondary {
    background: transparent;
    color: #1e293b;
    border: 1px solid rgba(0,0,0,0.1);
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.25s ease, transform 0.2s ease;
}
.btn-secondary:hover { background: rgba(248,250,252,0.8); transform: scale(1.02); }

@media (prefers-color-scheme: dark) {
    .btn-secondary {
        color: #f1f5f9;
        border: 1px solid rgba(255,255,255,0.25);
    }
    .btn-secondary:hover { background: rgba(255,255,255,0.1); }
}

/* Divider */
.divider {
    height: 1px;
    background: rgba(0,0,0,0.1);
    margin: 2rem 0 1.5rem;
}
@media (prefers-color-scheme: dark) {
    .divider { background: rgba(255,255,255,0.15); }
}

/* Header Text Theme-aware */
.page-wrapper h1 {
    color: #111827; /* Light mode */
    transition: color 0.3s ease;
}
.page-wrapper p {
    color: #6b7280; /* Light mode */
    transition: color 0.3s ease;
}
@media (prefers-color-scheme: dark) {
    .page-wrapper h1 {
        color: #f3f4f6; /* Dark mode */
    }
    .page-wrapper p {
        color: #9ca3af; /* Dark mode */
    }
}

/* Responsive Layout */
@media (max-width: 1024px) {
    .card-premium { padding: 2.5rem; }
}
@media (max-width: 768px) {
    .card-premium { padding: 2rem; }
    .grid.sm\:grid-cols-2 { grid-template-columns: 1fr; gap: 1rem; }
}
@media (max-width: 480px) {
    .card-premium { padding: 1.5rem; }
    .btn-primary, .btn-secondary { width: 100%; }
    .floating-input, select.floating-input { padding: 0.65rem 0.9rem; font-size: 0.95rem; }
    .floating-label { font-size: 0.9rem; }
}
</style>

<div class="page-wrapper px-4 md:px-8 py-16">
    <div class="max-w-4xl mx-auto">

        {{-- HEADER --}}
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold">
                 Edit User
            </h1>
            <p class="text-lg mt-2">
                Update informasi user dengan tampilan mewah & modern.
            </p>
        </div>

        {{-- ERRORS --}}
        @if ($errors->any())
        <div class="mb-8 p-5 rounded-xl
            bg-red-100 dark:bg-red-900/25
            border border-red-300 dark:border-red-700
            text-red-700 dark:text-red-300 shadow">
            <ul class="list-disc pl-5 text-sm space-y-1">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- CARD --}}
        <div class="card-premium">

            <form action="{{ route('users.update', $user->id) }}" method="POST" class="space-y-7">
                @csrf
                @method('PUT')

                {{-- GRID --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Role --}}
                    <div class="floating-group">
                        <input type="hidden" name="role" value="{{ $user->role }}">
                        <input class="floating-input" value="{{ ucfirst($user->role) }}" disabled placeholder=" ">
                        <label class="floating-label">Role</label>
                    </div>

                    {{-- Status Member --}}
                    @if($user->member)
                    <div class="floating-group">
                        <input type="hidden" name="status_member" value="{{ $user->member->status }}">
                        <input class="floating-input" value="{{ ucfirst($user->member->status) }}" disabled placeholder=" ">
                        <label class="floating-label">Status Member</label>
                    </div>
                    @endif
                </div>

                {{-- NAME --}}
                <div class="floating-group">
                    <input type="text" name="name" class="floating-input"
                           value="{{ $user->name }}" placeholder=" " required>
                    <label class="floating-label">Nama Lengkap</label>
                </div>

                {{-- EMAIL --}}
                <div class="floating-group">
                    <input type="email" name="email" class="floating-input"
                           value="{{ $user->email }}" placeholder=" " required>
                    <label class="floating-label">Email</label>
                </div>

                {{-- KELAS --}}
                @if($user->member)
                <div class="floating-group">
                    <select name="kelas_id" class="floating-input" required>
                        <option value="" disabled selected></option>
                        @foreach ($kelas as $k)
                        <option value="{{ $k->id }}" @if($user->member->kelas_id == $k->id) selected @endif>
                            {{ $k->nama_kelas }}
                        </option>
                        @endforeach
                    </select>
                    <label class="floating-label">Kelas</label>
                </div>
                @endif

                {{-- PASSWORD --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="floating-group">
                        <input id="password" type="password" name="password"
                               class="floating-input pr-12" placeholder=" ">
                        <label class="floating-label">Password Baru (Opsional)</label>
                        <span class="eye-btn" onclick="togglePassword('password')">👁</span>
                    </div>

                    <div class="floating-group">
                        <input id="password_confirmation" type="password" name="password_confirmation"
                               class="floating-input pr-12" placeholder=" ">
                        <label class="floating-label">Konfirmasi Password</label>
                        <span class="eye-btn" onclick="togglePassword('password_confirmation')">👁</span>
                    </div>
                </div>

                <div class="divider"></div>

                {{-- BUTTONS --}}
                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit" class="btn-primary w-full sm:w-auto">
                        Simpan Perubahan
                    </button>

                    <a href="{{ route('users.manage') }}"
                       class="btn-secondary text-center w-full sm:w-auto">
                        Batal
                    </a>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
function togglePassword(id) {
    const input = document.getElementById(id);
    input.type = input.type === "password" ? "text" : "password";
}
</script>

@endsection
