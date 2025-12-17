document.addEventListener('DOMContentLoaded', () => {
    VoucherApp.init();
});

const VoucherApp = {
    init() {
        this.cache();
        this.bind();
    },

    cache() {
        this.grid   = document.getElementById('voucherGrid');
        this.modal  = document.getElementById('modalVoucher');
        this.form   = document.getElementById('formVoucher');
        this.method = document.getElementById('voucherMethod');
        this.id     = document.getElementById('voucherId');
        this.title  = document.getElementById('modalTitle');
    },

    bind() {
        document.getElementById('btnOpenCreate')
            ?.addEventListener('click', () => this.openCreate());

        this.modal?.addEventListener('click', e => {
            if (e.target === this.modal) UI.closeModal(this.modal);
        });

        this.modal?.querySelector('.modal-close')
            ?.addEventListener('click', () => UI.closeModal(this.modal));

        this.form?.addEventListener('submit', e => this.submit(e));
        this.grid?.addEventListener('click', e => this.cardAction(e));
    },

    openCreate() {
        this.form.reset();
        this.method.value = 'POST';
        this.id.value = '';
        this.title.textContent = 'Tambah Voucher';
        UI.openModal(this.modal);
    },

    async submit(e) {
        e.preventDefault();

        const fd = new FormData(this.form);
        const isEdit = this.method.value === 'PUT';
        const url = isEdit ? `${voucherBaseUrl}/${this.id.value}` : voucherStoreUrl;

        if (!this.validate(fd)) return;

        UI.loading(this.form, true);

        try {
            const res = await API.send(url, fd, this.method.value);

            UI.toast(isEdit ? 'Voucher diperbarui' : 'Voucher ditambahkan');

            if (isEdit) UI.updateCard(res.voucher);
            else UI.prependCard(res.voucher);

            UI.closeModal(this.modal);
            this.form.reset();
        } catch (err) {
            UI.toast(err, 'error');
        } finally {
            UI.loading(this.form, false);
        }
    },

    validate(fd) {
        const diskon = Number(fd.get('diskon_persen'));
        const kuota  = Number(fd.get('kuota'));

        if (diskon < 1 || diskon > 100) {
            UI.toast('Diskon harus 1–100%', 'error');
            return false;
        }

        if (kuota < 1) {
            UI.toast('Kuota minimal 1', 'error');
            return false;
        }

        return true;
    },

    cardAction(e) {
        const card = e.target.closest('.voucher-card');
        if (!card) return;

        // Tap flip
        if (!e.target.classList.contains('btn-edit') && !e.target.classList.contains('btn-delete')) {
            card.classList.toggle('flipped');
        }

        const id = card.dataset.id;

        if (e.target.classList.contains('btn-edit')) this.openEdit(card, id);
        if (e.target.classList.contains('btn-delete')) this.delete(id, card);
    },

    openEdit(card, id) {
        this.method.value = 'PUT';
        this.id.value = id;
        this.title.textContent = 'Edit Voucher';

        this.form.kode.value = card.querySelector('.voucher-header').innerText;
        this.form.deskripsi.value = card.querySelector('.voucher-description').innerText;

        UI.openModal(this.modal);
    },

    async delete(id, card) {
        if (!confirm('Hapus voucher ini?')) return;

        try {
            await API.delete(`${voucherBaseUrl}/${id}`);
            card.remove();
            UI.toast('Voucher dihapus');
        } catch (err) {
            UI.toast(err, 'error');
        }
    }
};

const API = {
    async send(url, body, method = 'POST') {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': body.get('_token'),
                'X-HTTP-Method-Override': method,
                'Accept': 'application/json'
            },
            body
        });
        const data = await res.json();
        if (!res.ok) throw this.error(data);
        return data;
    },

    async delete(url) {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('[name=_token]').value,
                'X-HTTP-Method-Override': 'DELETE',
                'Accept': 'application/json'
            }
        });
        const data = await res.json();
        if (!res.ok) throw this.error(data);
        return data;
    },

    error(data) {
        if (data.errors) return Object.values(data.errors).flat().join('\n');
        return data.message || 'Terjadi kesalahan';
    }
};

const UI = {
    openModal(modal) { modal?.classList.add('show'); },
    closeModal(modal) { modal?.classList.remove('show'); },

    loading(form, state) {
        const btn = form.querySelector('button[type="submit"]');
        btn.disabled = state;
        btn.innerHTML = state ? 'Menyimpan…' : '💾 Simpan';
    },

    toast(msg, type = 'success') {
        const t = document.getElementById('toast');
        if (!t) return;
        t.textContent = msg;
        t.className = `toast show ${type}`;
        setTimeout(() => t.classList.remove('show'), 3000);
    },

    prependCard(v) {
        VoucherApp.grid?.insertAdjacentHTML('afterbegin', VoucherTemplate.render(v));
    },

    updateCard(v) {
        const card = document.querySelector(`.voucher-card[data-id="${v.id}"]`);
        if (!card) return;
        card.querySelector('.voucher-header').innerText = v.kode;
        card.querySelector('.voucher-description').innerText = v.deskripsi;
    }
};

const VoucherTemplate = {
    render(v) {
        return `
        <div class="voucher-card" data-id="${v.id}">
            <div class="voucher-front">
                <div>
                    <div class="voucher-header">${v.kode}</div>
                    <div class="voucher-description">${v.deskripsi}</div>
                    <div class="voucher-info">
                        <span>Diskon: ${v.diskon_persen}%</span>
                        <span>Status: ${v.status}</span>
                    </div>
                </div>
            </div>
            <div class="voucher-back">
                <div class="voucher-info">
                    <span>Target: ${v.role_target}</span>
                    <span>Kuota: ${v.kuota}</span>
                </div>
                <div class="card-actions">
                    <button class="btn-edit">✏️ Edit</button>
                    <button class="btn-delete">🗑️ Hapus</button>
                </div>
            </div>
        </div>`;
    }
};
