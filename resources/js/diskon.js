document.addEventListener('DOMContentLoaded', function () {

    /* ======================================
       SEARCH BAR + CLEAR BUTTON
    ====================================== */
    const table = document.getElementById('diskonTable');
    const searchInput = document.getElementById('searchInput');
    const btnClearSearch = document.getElementById('btnClearSearch');

    if (table && searchInput && btnClearSearch) {
        let debounceTimer;

        function filterTable(keyword) {
            const filter = keyword.toLowerCase();
            Array.from(table.tBodies[0].rows).forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(filter)
                    ? ''
                    : 'none';
            });
        }

        searchInput.addEventListener('input', function () {
            const value = this.value.trim();
            btnClearSearch.style.display = value ? 'block' : 'none';

            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => filterTable(value), 150);
        });

        btnClearSearch.addEventListener('click', () => {
            searchInput.value = '';
            btnClearSearch.style.display = 'none';
            filterTable('');
            searchInput.focus();
        });

        searchInput.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                searchInput.value = '';
                btnClearSearch.style.display = 'none';
                filterTable('');
            }
        });
    }

    /* ======================================
       TOAST
    ====================================== */
    const toast = document.getElementById('toast');

    function showToast(msg = '✅ Aksi berhasil') {
        if (!toast) return;
        toast.textContent = msg;
        toast.classList.add('show');
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-20px)';
        toast.style.transition = 'opacity 0.4s ease, transform 0.4s ease';

        requestAnimationFrame(() => {
            toast.style.opacity = '1';
            toast.style.transform = 'translateY(0)';
        });

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-20px)';
            setTimeout(() => toast.classList.remove('show'), 400);
        }, 3000);
    }

    /* ======================================
       MODAL HELPER
    ====================================== */
    function openModal(modal) {
        if (!modal) return;
        modal.style.display = 'flex';
        modal.style.opacity = '0';
        modal.style.transform = 'translateY(-30px)';
        modal.style.transition = 'opacity 0.3s ease, transform 0.3s ease';

        requestAnimationFrame(() => {
            modal.style.opacity = '1';
            modal.style.transform = 'translateY(0)';
        });

        // auto-focus input pertama
        const firstInput = modal.querySelector('input, select, textarea, button');
        firstInput?.focus();
    }

    function closeModal(modal) {
        if (!modal) return;
        modal.style.opacity = '0';
        modal.style.transform = 'translateY(-30px)';
        setTimeout(() => modal.style.display = 'none', 300);
    }

    /* ======================================
       MODAL CREATE
    ====================================== */
    const btnOpenCreate = document.getElementById('btnOpenCreate');
    const modalCreate = document.getElementById('modalCreate');
    const btnCloseCreate = document.getElementById('btnCloseCreate');

    if (btnOpenCreate && modalCreate) {
        btnOpenCreate.addEventListener('click', () => openModal(modalCreate));
        btnCloseCreate?.addEventListener('click', () => closeModal(modalCreate));
        modalCreate.querySelector('.modal-close')?.addEventListener('click', () => closeModal(modalCreate));
    }

    /* ======================================
       MODAL EDIT
    ====================================== */
    const modalEdit = document.getElementById('modalEdit');
    const btnCloseEdit = document.getElementById('btnCloseEdit');
    const formEditDiskon = document.getElementById('formEditDiskon');

    document.querySelectorAll('.btnOpenEdit').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            document.getElementById('editId').value = id;
            document.getElementById('editKelasId').value = this.dataset.kelas_id;
            document.getElementById('editNamaDiskon').value = this.dataset.nama_diskon;
            document.getElementById('editPersentase').value = this.dataset.persentase;
            document.getElementById('editTanggalMulai').value = this.dataset.tanggal_mulai;
            document.getElementById('editTanggalBerakhir').value = this.dataset.tanggal_berakhir;

            if (formEditDiskon) formEditDiskon.action = `/diskon/${id}`;

            openModal(modalEdit);
        });
    });

    btnCloseEdit?.addEventListener('click', () => closeModal(modalEdit));
    modalEdit?.querySelector('.modal-close')?.addEventListener('click', () => closeModal(modalEdit));

});
