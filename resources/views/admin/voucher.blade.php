@extends('layout.app')

@section('title', 'Manage Voucher')

@section('content')

    <style>
        /* ==========================================
           PREMIUM FLIP CARD UI - Dark/Light Mode
        ========================================== */
        .page-bg {
            background: linear-gradient(180deg, #f0f4f8 0%, #e2e8f0 100%);
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            padding: 2rem;
            transition: all 0.3s;
        }

        .dark .page-bg {
            background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
            color: #e5e7eb;
        }

        /* === MODIFIED PAGE TITLE FOR AUTO DARK/LIGHT DEVICE MODE === */
        .page-title {
            font-size: 2.25rem;
            font-weight: 700;
            margin-bottom: 1rem;
            transition: color 0.3s;
            color: #1e293b; /* default light mode color */
        }

        @media (prefers-color-scheme: dark) {
            .page-title {
                color: #ffffff; /* dark mode color */
            }
        }

        /* Grid layout */
        .voucher-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
            perspective: 1200px;
        }

        /* Flip card */
        .voucher-card {
            width: 100%;
            height: 200px;
            position: relative;
            cursor: pointer;
            transform-style: preserve-3d;
            opacity: 1;
            transform: scale(1);
            transition: transform 0.6s, box-shadow 0.3s, opacity 0.5s, transform 0.5s;
        }

        .voucher-card.fade-in {
            opacity: 0;
            transform: scale(0.9);
        }

        .voucher-card.fade-out {
            opacity: 0;
            transform: scale(0.8);
        }

        .voucher-card:hover {
            transform: scale(1.03);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35);
        }

        .voucher-card.flipped {
            transform: rotateY(180deg);
        }

        /* Card front/back */
        .voucher-front,
        .voucher-back {
            position: absolute;
            inset: 0;
            border-radius: 20px;
            overflow: hidden;
            color: #fff;
            backface-visibility: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 1.8rem;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
            transition: all 0.3s;
        }

        .voucher-front {
            background: linear-gradient(135deg, #3b82f6, #60a5fa);
        }

        .voucher-back {
            background: linear-gradient(135deg, #2563eb, #3b82f6);
            transform: rotateY(180deg);
        }

        /* Dark mode adjustments */
        .dark .voucher-front {
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
        }

        .dark .voucher-back {
            background: linear-gradient(135deg, #1e40af, #2563eb);
        }

        /* Hologram stripes */
        .voucher-front::before,
        .voucher-back::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 10px;
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0.2));
            filter: blur(2px);
        }

        .voucher-front::after,
        .voucher-back::after {
            content: '';
            position: absolute;
            top: 0;
            left: -50%;
            width: 200%;
            height: 100%;
            background: repeating-linear-gradient(45deg, rgba(255, 255, 255, 0.15) 0, rgba(255, 255, 255, 0.05) 2px, rgba(255, 255, 255, 0.15) 4px);
            pointer-events: none;
            animation: holoMove 3s infinite linear;
        }

        @keyframes holoMove {
            0% { transform: translateX(-100%) rotate(25deg); }
            100% { transform: translateX(100%) rotate(25deg); }
        }

        /* Text & badges */
        .voucher-code {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: 3px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            font-variation-settings: 'wght' 700;
        }

        .voucher-desc {
            font-size: 0.95rem;
            opacity: 0.9;
            margin-bottom: 0.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .voucher-info {
            display: flex;
            justify-content: space-between;
            font-size: 0.85rem;
        }

        .voucher-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.75rem;
            color: #fff;
            transition: all 0.3s;
        }

        .badge-green {
            background: rgba(16, 163, 52, 0.85);
            animation: pulse 2s infinite;
        }

        .badge-red {
            background: rgba(220, 38, 38, 0.85);
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        /* Card actions */
        .card-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 0.75rem;
        }

        .card-actions button {
            flex: 1;
            margin: 0 0.25rem;
            padding: 0.5rem 0;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.2s;
            cursor: pointer;
        }

        .card-actions .btn-edit,
        .card-actions .btn-delete {
            background: rgba(255, 255, 255, 0.2);
            color: #fff;
        }

        .card-actions .btn-edit:hover,
        .card-actions .btn-delete:hover {
            background: rgba(255, 255, 255, 0.35);
        }

        /* Modal & toast */
        .modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 50;
            transition: opacity 0.3s;
        }

        .modal-backdrop.show {
            display: flex;
        }

        .modal-bg {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 2rem;
            max-width: 480px;
            width: 95%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.2);
            backdrop-filter: blur(12px);
            transform: translateY(-40px);
            opacity: 0;
            transition: all 0.3s ease;
            position: relative;
        }

        .modal-backdrop.show .modal-bg {
            transform: translateY(0);
            opacity: 1;
        }

        .modal-close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 1.25rem;
            color: #374151;
            background: none;
            border: none;
        }

        .modal-close:hover {
            color: #ef4444;
            cursor: pointer;
        }

        .toast {
            position: fixed;
            top: 1rem;
            right: 1rem;
            background: rgba(51, 65, 85, 0.9);
            color: #fff;
            padding: 0.75rem 1.25rem;
            border-radius: 16px;
            font-weight: 600;
            z-index: 9999;
            opacity: 0;
            transform: translateY(-20px);
            transition: all 0.3s;
        }

        .toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* === BUTTON TAMBAH VOUCHER MODIFIED === */
        .btn-primary {
            background: linear-gradient(135deg, #4f46e5, #3b82f6);
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 16px;
            padding: 0.75rem 1.25rem;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 6px 15px rgba(59, 130, 246, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.6);
        }
    </style>

    <div class="flex justify-between items-center mb-6">
        <h1 class="page-title">🎫 Manage Voucher</h1>
        <button id="btnOpenCreate" class="btn-primary">+ Tambah Voucher</button>
    </div>

    <div class="voucher-grid" id="voucherGrid">
        @forelse ($vouchers as $voucher)
            <div class="voucher-card" data-id="{{ $voucher->id }}">
                <div class="voucher-front">
                    <div>
                        <div class="voucher-code">{{ $voucher->kode }}</div>
                        <div class="voucher-desc">{{ $voucher->deskripsi }}</div>
                        <div class="voucher-info">
                            <span>Diskon: {{ $voucher->diskon_persen }}%</span>
                            <span>Kelas: {{ $voucher->kelas->nama_kelas ?? 'Semua' }}</span>
                        </div>
                    </div>
                </div>
                <div class="voucher-back">
                    <div class="voucher-info">
                        <span>Berakhir: {{ $voucher->tanggal_akhir }}</span>
                        <span>Status: <span
                                class="voucher-badge {{ $voucher->status === 'aktif' ? 'badge-green' : 'badge-red' }}">{{ ucfirst($voucher->status) }}</span></span>
                    </div>
                    <div class="voucher-info">
                        <span>Target: {{ ucfirst($voucher->role_target) }}</span>
                        <span>Kuota: {{ $voucher->kuota }}</span>
                    </div>
                    <div class="voucher-info">
                        <span>Tanggal Mulai: {{ $voucher->tanggal_mulai }}</span>
                    </div>
                    <div class="card-actions">
                        <button class="btn-edit">✏️ Edit</button>
                        <button class="btn-delete">🗑️ Hapus</button>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-gray-500 dark:text-gray-300 col-span-full text-center">Belum ada voucher</p>
        @endforelse
    </div>

    <div id="modalVoucher" class="modal-backdrop">
        <div class="modal-bg">
            <button class="modal-close">&times;</button>
            <h2 class="text-2xl font-bold mb-4" id="modalTitle">Tambah Voucher</h2>
            <form id="formVoucher" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" id="voucherMethod" value="POST">
                <input type="hidden" name="voucher_id" id="voucherId">
                <select name="kelas_id" class="form-input w-full p-3 rounded-lg">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelas as $k)
                        <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                    @endforeach
                </select>
                <input type="text" name="kode" placeholder="Kode Voucher" class="form-input w-full p-3 rounded-lg" required>
                <textarea name="deskripsi" placeholder="Deskripsi" class="form-input w-full p-3 rounded-lg h-24"></textarea>
                <div class="grid grid-cols-2 gap-4">
                    <input type="number" name="diskon_persen" placeholder="Diskon (%)" class="form-input w-full p-3 rounded-lg" min="1" max="100" required>
                    <input type="number" name="kuota" placeholder="Kuota" class="form-input w-full p-3 rounded-lg" min="1" required>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <input type="date" name="tanggal_mulai" class="form-input w-full p-3 rounded-lg" required>
                    <input type="date" name="tanggal_akhir" class="form-input w-full p-3 rounded-lg" required>
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
                <div class="flex justify-between mt-5">
                    <button type="submit" class="btn-primary w-full py-3 font-semibold" id="btnSaveVoucher">💾 Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <div id="toast" class="toast"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('modalVoucher');
            const btnOpen = document.getElementById('btnOpenCreate');
            const btnClose = modal.querySelector('.modal-close');
            const form = document.getElementById('formVoucher');
            const grid = document.getElementById('voucherGrid');
            const toast = document.getElementById('toast');
            const modalTitle = document.getElementById('modalTitle');
            const voucherMethod = document.getElementById('voucherMethod');
            const voucherIdInput = document.getElementById('voucherId');

            const kelasOptions = {};
            @foreach ($kelas as $k)
                kelasOptions["{{ $k->id }}"] = "{{ $k->nama_kelas }}";
            @endforeach

            grid.querySelectorAll('.voucher-card').forEach(card => {
                card.addEventListener('click', () => card.classList.toggle('flipped'));
            });

            function openModal() { modal.classList.add('show'); }
            function closeModal() { modal.classList.remove('show'); }
            function showToast(msg) { toast.textContent = msg; toast.classList.add('show'); setTimeout(() => toast.classList.remove('show'), 3000); }

            btnOpen?.addEventListener('click', () => {
                modalTitle.textContent = "Tambah Voucher";
                voucherMethod.value = "POST";
                form.reset();
                openModal();
            });
            btnClose?.addEventListener('click', closeModal);
            modal?.addEventListener('click', e => { if (e.target === modal) closeModal(); });

            grid.addEventListener('click', e => {
                const card = e.target.closest('.voucher-card');
                if (!card) return;
                const id = card.dataset.id;

                if (e.target.classList.contains('btn-edit')) {
                    voucherIdInput.value = id;
                    voucherMethod.value = "PUT";
                    modalTitle.textContent = "Edit Voucher";

                    form.kode.value = card.querySelector('.voucher-code').innerText;
                    form.deskripsi.value = card.querySelector('.voucher-desc').innerText;
                    form.diskon_persen.value = parseInt(card.querySelector('.voucher-info span:first-child').innerText.match(/\d+/));
                    form.kuota.value = parseInt(card.querySelector('.voucher-info span:last-child').innerText.match(/\d+/));

                    const kelasText = card.querySelector('.voucher-info span:nth-child(2)').innerText.replace('Kelas: ', '');
                    const kelasId = Object.keys(kelasOptions).find(key => kelasOptions[key] === kelasText);
                    form.kelas_id.value = kelasId || '';

                    form.role_target.value = card.querySelector('.voucher-info span:nth-child(3)')?.innerText.replace('Target: ', '') || 'semua';
                    form.status.value = card.querySelector('.voucher-badge')?.innerText.toLowerCase() || 'aktif';

                    const tanggalMulai = card.querySelector('.voucher-info span:nth-child(4)')?.innerText.replace('Tanggal Mulai: ', '') || '';
                    const tanggalAkhir = card.querySelector('.voucher-info span:first-child')?.innerText.replace('Berakhir: ', '') || '';
                    form.tanggal_mulai.value = tanggalMulai;
                    form.tanggal_akhir.value = tanggalAkhir;

                    openModal();
                }

                if (e.target.classList.contains('btn-delete')) {
                    if (!confirm('Hapus voucher ini?')) return;
                    fetch(`/admin/voucher/${id}`, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                        body: JSON.stringify({ _method: 'DELETE' })
                    }).then(res => {
                        if (res.ok) { card.remove(); showToast('Voucher berhasil dihapus'); }
                        else showToast('Gagal menghapus voucher');
                    });
                }
            });

            form.addEventListener('submit', e => {
                e.preventDefault();
                const formData = new FormData(form);
                const id = voucherIdInput.value;
                const method = voucherMethod.value;

                fetch(method === 'POST' ? '/admin/voucher' : `/admin/voucher/${id}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: formData
                }).then(res => res.json()).then(data => {
                    if (data.success) {
                        closeModal();
                        showToast(data.message || 'Voucher Berhasil Disimpan');
                        setTimeout(() => location.reload(), 1000);
                    } else showToast(data.message || 'Gagal menyimpan voucher');
                }).catch(() => showToast('Terjadi kesalahan'));
            });
        });
    </script>

@endsection
