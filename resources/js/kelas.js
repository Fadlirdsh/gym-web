// ===============================
// 📘 Modal Create
// ===============================
const btnOpenCreate = document.getElementById("btnOpenCreate");
const modalCreate = document.getElementById("modalCreate");
const btnCloseCreate = document.getElementById("btnCloseCreate");
const btnCloseCreateBottom = document.getElementById("btnCloseCreateBottom");

btnOpenCreate?.addEventListener("click", () => {
    modalCreate?.classList.remove("hidden");
});

[btnCloseCreate, btnCloseCreateBottom].forEach((btn) => {
    btn?.addEventListener("click", () => {
        modalCreate?.classList.add("hidden");
    });
});

// ===============================
// 📘 Modal Edit
// ===============================
const modalEdit = document.getElementById("modalEdit");
const btnCloseEdit = document.getElementById("btnCloseEdit");
const btnCloseEditBottom = document.getElementById("btnCloseEditBottom");
const formEdit = document.getElementById("formEdit");

document.querySelectorAll(".btnOpenEdit").forEach((btn) => {
    btn.addEventListener("click", () => {
        if (!modalEdit || !formEdit) return;

        // buka modal
        modalEdit.classList.remove("hidden");

        // set action form
        formEdit.action = updateRouteTemplate.replace(":id", btn.dataset.id);

        // isi data (INI YANG PENTING)
        document.getElementById("editNama").value = btn.dataset.nama ?? "";
        document.getElementById("editHarga").value = btn.dataset.harga ?? "";
        document.getElementById("editDeskripsi").value = btn.dataset.deskripsi ?? "";
        document.getElementById("editKapasitas").value = btn.dataset.kapasitas ?? "";
        document.getElementById("editExpired").value = btn.dataset.expired ?? "";
        document.getElementById("editTipe").value = btn.dataset.tipe ?? "";
    });
});

[btnCloseEdit, btnCloseEditBottom].forEach((btn) => {
    btn?.addEventListener("click", () => {
        modalEdit?.classList.add("hidden");
    });
});

// ===============================
// 📘 Tutup modal jika klik backdrop
// ===============================
window.addEventListener("click", (e) => {
    if (e.target === modalCreate) modalCreate.classList.add("hidden");
    if (e.target === modalEdit) modalEdit.classList.add("hidden");
});

// ===============================
// 📘 Modal QR CODE
// ===============================
async function openQrModal(id, nama) {
    const modal = document.getElementById("qrModal");
    const qrTitle = document.getElementById("qrTitle");
    const qrContainer = document.getElementById("qrContainer");

    qrTitle.textContent = "QR Absen: " + nama;
    qrContainer.innerHTML = `<p class="text-gray-600 text-sm">Loading...</p>`;

    modal.classList.remove("hidden");

    try {
        const response = await fetch(`/admin/kelas/${id}/qr`);
        if (!response.ok) throw new Error("Fetch error");

        const result = await response.json();

        if (result.qr_svg) {
            qrContainer.innerHTML = result.qr_svg;
        } else if (result.qr_url) {
            qrContainer.innerHTML = `<img src="${result.qr_url}" class="mx-auto" />`;
        } else {
            qrContainer.innerHTML = `<p class="text-red-600 text-sm">QR tidak tersedia</p>`;
        }
    } catch (err) {
        console.error(err);
        qrContainer.innerHTML = `<p class="text-red-600 text-sm">Gagal memuat QR</p>`;
    }
}

function closeQrModal() {
    document.getElementById("qrModal")?.classList.add("hidden");
}

window.openQrModal = openQrModal;
window.closeQrModal = closeQrModal;
