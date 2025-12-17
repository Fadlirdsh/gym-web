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


document.getElementById("btnOpenCreate")?.addEventListener("click", () => {
    openModal(modalCreate);
});

["btnCloseCreate", "btnCloseCreateBottom"].forEach(id => {
    document.getElementById(id)?.addEventListener("click", () => {
        closeModal(modalCreate);
    });
});

/* =========================================================
   MODAL EDIT
========================================================= */

const modalEdit = document.getElementById("modalEdit");
const formEdit = document.getElementById("formEdit");

const updateRouteTemplate = "/admin/kelas/:id";


document.querySelectorAll(".btnOpenEdit").forEach(btn => {
    btn.addEventListener("click", () => {

        openModal(modalEdit);

        // set action
        formEdit.action = updateRouteTemplate.replace(":id", btn.dataset.id);

        // fill form
        document.getElementById("editNama").value = btn.dataset.nama;
        document.getElementById("editHarga").value = btn.dataset.harga;
        document.getElementById("editDeskripsi").value = btn.dataset.deskripsi;
        document.getElementById("editKapasitas").value = btn.dataset.kapasitas;
        document.getElementById("editExpired").value = btn.dataset.expired ?? "";
        document.getElementById("editTipe").value = btn.dataset.tipe;
    });
});

["btnCloseEdit", "btnCloseEditBottom"].forEach(id => {
    document.getElementById(id)?.addEventListener("click", () => {
        closeModal(modalEdit);
    });
});


/* =========================================================
   MODAL QR — FIXED (NOW CAN OPEN MULTIPLE TIMES)
========================================================= */

window.openQrModal = async function (id, nama) {


    const modal = document.getElementById("qrModal");
    const qrTitle = document.getElementById("qrTitle");
    const qrContainer = document.getElementById("qrContainer");

    // RESET state setiap buka modal
    qrContainer.innerHTML = `
        <p class="text-sm text-gray-500 dark:text-gray-400">Memuat QR...</p>
    `;

    qrTitle.textContent = "QR Absen: " + nama;


    // OPEN modal
    openModal(modal);

    try {
        const response = await fetch(`/admin/kelas/${id}/qr`);

        const result = await response.json();

        if (result.qr_svg) {
            qrContainer.innerHTML = result.qr_svg;
        } else if (result.qr_url) {

            qrContainer.innerHTML = `
                <img src="${result.qr_url}" class="w-48 h-48 rounded-xl shadow-md mx-auto" />
            `;
        } else {
            qrContainer.innerHTML =
                `<p class="text-red-500 text-sm">QR tidak tersedia.</p>`;
        }

    } catch (error) {
        qrContainer.innerHTML =
            `<p class="text-red-500 text-sm">Gagal memuat QR...</p>`;

    }
};


window.closeQrModal = function () {
    const modal = document.getElementById("qrModal");
    closeModal(modal);
};


/* =========================================================
   CLOSE MODAL WHEN CLICK OUTSIDE
========================================================= */

document.addEventListener("click", (e) => {
    document.querySelectorAll(".modal-overlay").forEach((overlay) => {
        if (e.target === overlay) {
            const modal = overlay.closest(".fixed");
            closeModal(modal);
        }
    });
});

/* =========================================================
   ESC SUPPORT
========================================================= */

document.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
        ["modalCreate", "modalEdit", "qrModal"].forEach(id => {
            const modal = document.getElementById(id);
            if (modal && !modal.classList.contains("hidden")) {
                closeModal(modal);
            }
        });
    }
});

/* =========================================================
   Smooth Theme Transition
========================================================= */

document.documentElement.classList.add("transition", "duration-300");

