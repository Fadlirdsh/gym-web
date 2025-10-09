// **BARIS TAMBAHAN UTAMA UNTUK FIX 404**
// Injeksi template URL dari PHP. 'CLASS_ID_PLACEHOLDER' akan diganti di JS.
const updateRouteTemplate = window.updateRouteTemplate;
// Modal Create
const btnOpenCreate = document.getElementById("btnOpenCreate");
const modalCreate = document.getElementById("modalCreate");
const btnCloseCreate = document.getElementById("btnCloseCreate");

btnOpenCreate.addEventListener("click", () => {
  modalCreate.classList.remove("hidden");
  modalCreate.classList.add("flex");
});

btnCloseCreate.addEventListener("click", () => {
  modalCreate.classList.add("hidden");
  modalCreate.classList.remove("flex");
});

// Modal Edit
const modalEdit = document.getElementById("modalEdit");
const btnCloseEdit = document.getElementById("btnCloseEdit");
const formEdit = document.getElementById("formEdit");
const editTipe = document.getElementById("editTipe");

document.querySelectorAll(".btnOpenEdit").forEach(btn => {
  btn.addEventListener("click", () => {
    modalEdit.classList.remove("hidden");
    modalEdit.classList.add('flex');

    // **PERBAIKAN 404:** Set form action menggunakan template route yang benar
    // formEdit.action = "/users/kelas/" + btn.dataset.id; // BARIS LAMA
    formEdit.action = updateRouteTemplate.replace(':id', btn.dataset.id);// BARIS BARU

    // Isi field edit
    document.getElementById("editNama").value = btn.dataset.nama;
    document.getElementById("editHarga").value = btn.dataset.harga;
    // document.getElementById("editDiskon").value = btn.dataset.diskon;
    document.getElementById("tipePaketEdit").value = btn.dataset.paket;
    document.getElementById("editDeskripsi").value = btn.dataset.deskripsi;
    // document.getElementById("editWaktu").value = btn.dataset.waktu;
    document.getElementById("editToken").value = btn.dataset.token;
    document.getElementById("editExpired").value = btn.dataset.expired;
    document.getElementById("editKapasitas").value = btn.dataset.kapasitas;

    // Pilih tipe kelas sesuai data
    editTipe.value = btn.dataset.tipe;

    // Update hide/show token & expired sesuai tipe paket
    toggleTokenFieldsEdit();
  });
});

btnCloseEdit.addEventListener("click", () => {
  modalEdit.classList.add("hidden");
  modalEdit.classList.remove("flex");
});

// ===== Tambahan: Hide/Show token & expired =====

// Modal Create
const tipeInputCreate = document.querySelector('#modalCreate [name="tipe_paket"]');
const tokenInputCreate = document.querySelector('#modalCreate [name="jumlah_token"]');
const expiredInputCreate = document.querySelector('#modalCreate [name="expired_at"]');

function toggleTokenFieldsCreate() {
  if (tipeInputCreate.value.toLowerCase() === 'classes') {
    tokenInputCreate.parentElement.style.display = 'block';
    expiredInputCreate.parentElement.style.display = 'block';
    tokenInputCreate.setAttribute('required', 'required'); // wajib isi
    expiredInputCreate.setAttribute('required', 'required'); // wajib isi
    tokenInputCreate.setAttribute('min', '1'); // minimal 1
  } else {
    tokenInputCreate.parentElement.style.display = 'none';
    expiredInputCreate.parentElement.style.display = 'none';
    tokenInputCreate.value = '';
    expiredInputCreate.value = '';
    tokenInputCreate.removeAttribute('required');
    expiredInputCreate.removeAttribute('required');
    tokenInputCreate.removeAttribute('min');
  }
}

tipeInputCreate.addEventListener('input', toggleTokenFieldsCreate);
toggleTokenFieldsCreate(); // inisialisasi saat modal terbuka

// Modal Edit
const tipeInputEdit = document.querySelector('#modalEdit [name="tipe_paket"]');
const tokenInputEdit = document.querySelector('#modalEdit [name="jumlah_token"]');
const expiredInputEdit = document.querySelector('#modalEdit [name="expired_at"]');

function toggleTokenFieldsEdit() {
  if (tipeInputEdit.value.toLowerCase() === 'classes') {
    tokenInputEdit.parentElement.style.display = 'block';
    expiredInputEdit.parentElement.style.display = 'block';
  } else {
    tokenInputEdit.parentElement.style.display = 'none';
    expiredInputEdit.parentElement.style.display = 'none';
    tokenInputEdit.value = '';
    expiredInputEdit.value = '';
  }
}
tipeInputEdit.addEventListener('input', toggleTokenFieldsEdit);