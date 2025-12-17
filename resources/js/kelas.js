/* =========================================================
   UNIVERSAL MODAL HANDLER — DARK/LIGHT READY & BUG FREE
========================================================= */

// OPEN modal (with animation)
function openModal(modal) {
    if (!modal) return;

    modal.classList.remove("hidden");

    const box = modal.querySelector(".animate-showModal");
    if (box) {
        box.classList.remove("modal-show");
        // Delay to retrigger animation
        setTimeout(() => box.classList.add("modal-show"), 10);
    }
}

// CLOSE modal (with animation)
function closeModal(modal) {
    if (!modal) return;

    const box = modal.querySelector(".animate-showModal");
    if (box) box.classList.remove("modal-show");

    setTimeout(() => modal.classList.add("hidden"), 180);
}

/* =========================================================
   MODAL CREATE
========================================================= */

const modalCreate = document.getElementById("modalCreate");
const btnCloseCreate = document.getElementById("btnCloseCreate");
const btnCloseCreateBottom = document.getElementById("btnCloseCreateBottom");

btnOpenCreate?.addEventListener("click", () => {
    if (!modalCreate) return;
    modalCreate.style.display = "flex";
    modalCreate.style.alignItems = "center";
    modalCreate.style.justifyContent = "center";
    modalCreate.style.opacity = "0";
    modalCreate.style.transition = "opacity 0.3s ease";

    modalCreate.style.position = "fixed";
    modalCreate.style.top = "0";
    modalCreate.style.left = "0";
    modalCreate.style.width = "100%";
    modalCreate.style.height = "100%";
    modalCreate.style.backgroundColor = "rgba(0,0,0,0.5)";
    modalCreate.style.zIndex = "9999";

    setTimeout(() => { modalCreate.style.opacity = "1"; }, 10);
});

[btnCloseCreate, btnCloseCreateBottom].forEach((btn) => {
    btn?.addEventListener("click", () => {
        if (!modalCreate) return;
        modalCreate.style.opacity = "0";
        setTimeout(() => { modalCreate.style.display = "none"; }, 300);
    });
});

/* =========================================================
   MODAL EDIT
========================================================= */

const modalEdit = document.getElementById("modalEdit");
const formEdit = document.getElementById("formEdit");
const editTipe = document.getElementById("editTipe");

document.querySelectorAll(".btnOpenEdit").forEach(btn => {
    btn.addEventListener("click", () => {
        if (!modalEdit) return;

        modalEdit.style.display = "flex";
        modalEdit.style.alignItems = "center";
        modalEdit.style.justifyContent = "center";
        modalEdit.style.opacity = "0";
        modalEdit.style.transition = "opacity 0.3s ease";

        modalEdit.style.position = "fixed";
        modalEdit.style.top = "0";
        modalEdit.style.left = "0";
        modalEdit.style.width = "100%";
        modalEdit.style.height = "100%";
        modalEdit.style.backgroundColor = "rgba(0,0,0,0.5)";
        modalEdit.style.zIndex = "9999";

        setTimeout(() => { modalEdit.style.opacity = "1"; }, 10);

        // Atur route action form edit
        formEdit.action = updateRouteTemplate.replace(":id", btn.dataset.id);

        // Isi data form edit
        document.getElementById("editNama").value = btn.dataset.nama;
        document.getElementById("editHarga").value = btn.dataset.harga;
        document.getElementById("tipePaketEdit").value = btn.dataset.paket;
        document.getElementById("editDeskripsi").value = btn.dataset.deskripsi;
        document.getElementById("editKapasitas").value = btn.dataset.kapasitas;
        document.getElementById("editExpired").value = btn.dataset.expired;
        editTipe.value = btn.dataset.tipe;
    });
});

[btnCloseEdit, btnCloseEditBottom].forEach((btn) => {
    btn?.addEventListener("click", () => {
        if (!modalEdit) return;
        modalEdit.style.opacity = "0";
        setTimeout(() => { modalEdit.style.display = "none"; }, 300);
    });
});

// ===============================
// 📘 Tutup modal kalau klik di luar konten
// ===============================
window.addEventListener("click", (e) => {
    if (e.target === modalCreate) {
        modalCreate.style.opacity = "0";
        setTimeout(() => { modalCreate.style.display = "none"; }, 300);
    }
    if (e.target === modalEdit) {
        modalEdit.style.opacity = "0";
        setTimeout(() => { modalEdit.style.display = "none"; }, 300);
    }
});

// ===============================
// 📘 Modal QR CODE Absen
// ===============================
async function openQrModal(id, nama) {
    const modal = document.getElementById("qrModal");
    const qrTitle = document.getElementById("qrTitle");
    const qrContainer = document.getElementById("qrContainer");

    // RESET state setiap buka modal
    qrContainer.innerHTML = `
        <p class="text-sm text-gray-500 dark:text-gray-400">Memuat QR...</p>
    `;

    qrTitle.textContent = "QR Absen: " + nama;
    qrContainer.innerHTML = `<p class="text-gray-600 text-sm">Loading...</p>`;

    modal.style.display = "flex";
    modal.style.alignItems = "center";
    modal.style.justifyContent = "center";
    modal.style.opacity = "0";
    modal.style.transition = "opacity 0.3s ease";
    modal.style.position = "fixed";
    modal.style.top = "0";
    modal.style.left = "0";
    modal.style.width = "100%";
    modal.style.height = "100%";
    modal.style.backgroundColor = "rgba(0,0,0,0.5)";
    modal.style.zIndex = "9999";

    setTimeout(() => { modal.style.opacity = "1"; }, 10);

    try {
        const response = await fetch(`/admin/kelas/${id}/qr`);
        if (!response.ok) throw new Error("Network response was not ok");

        const result = await response.json();

        if (result.qr_svg) {
            qrContainer.innerHTML = result.qr_svg;
        } else if (result.qr_url) {
            qrContainer.innerHTML = `<img src="${result.qr_url}" alt="QR Code" class="mx-auto">`;
        }

    } catch (error) {
        console.error("QR Fetch Error:", error);
        qrContainer.innerHTML = `<p class="text-red-600 text-sm">Gagal memuat QR!</p>`;
    }
};

function closeQrModal() {
    const modal = document.getElementById("qrModal");
    modal.style.opacity = "0";
    setTimeout(() => { modal.style.display = "none"; }, 300);
}

window.addEventListener("click", (e) => {
    const modal = document.getElementById("qrModal");
    if (e.target === modal) closeQrModal();
});

window.openQrModal = openQrModal;
window.closeQrModal = closeQrModal;
