// ========================
// Modal Create
// ========================
const btnOpenCreate = document.getElementById("btnOpenCreate");
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

  setTimeout(() => {
    modalCreate.style.opacity = "1";
  }, 10);
});

// Tutup modal Create (atas & bawah)
[btnCloseCreate, btnCloseCreateBottom].forEach((btn) => {
  btn?.addEventListener("click", () => {
    if (!modalCreate) return;
    modalCreate.style.opacity = "0";
    setTimeout(() => {
      modalCreate.style.display = "none";
    }, 300);
  });
});

// ========================
// Modal Edit
// ========================
const modalEdit = document.getElementById("modalEdit");
const btnCloseEdit = document.getElementById("btnCloseEdit");
const btnCloseEditBottom = document.getElementById("btnCloseEditBottom");
const formEdit = document.getElementById("formEdit");
const editTipe = document.getElementById("editTipe");

document.querySelectorAll(".btnOpenEdit").forEach((btn) => {
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

    setTimeout(() => {
      modalEdit.style.opacity = "1";
    }, 10);

    // Atur route action form edit
    formEdit.action = updateRouteTemplate.replace(":id", btn.dataset.id);

    // Isi data
    document.getElementById("editNama").value = btn.dataset.nama;
    document.getElementById("editHarga").value = btn.dataset.harga;
    document.getElementById("tipePaketEdit").value = btn.dataset.paket;
    document.getElementById("editDeskripsi").value = btn.dataset.deskripsi;
    document.getElementById("editKapasitas").value = btn.dataset.kapasitas;
    document.getElementById('editExpired').value = btn.dataset.expired;
    editTipe.value = btn.dataset.tipe;
  });
});

// Tutup modal Edit (atas & bawah)
[btnCloseEdit, btnCloseEditBottom].forEach((btn) => {
  btn?.addEventListener("click", () => {
    if (!modalEdit) return;
    modalEdit.style.opacity = "0";
    setTimeout(() => {
      modalEdit.style.display = "none";
    }, 300);
  });
});

// ========================
// Tutup modal kalau klik di luar konten
// ========================
window.addEventListener("click", (e) => {
  if (e.target === modalCreate) {
    modalCreate.style.opacity = "0";
    setTimeout(() => (modalCreate.style.display = "none"), 300);
  }
  if (e.target === modalEdit) {
    modalEdit.style.opacity = "0";
    setTimeout(() => (modalEdit.style.display = "none"), 300);
  }
});

// ========================
// Token/Expired REMOVED (karena tidak digunakan di halaman Kelas)
// ========================
