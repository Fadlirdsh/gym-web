
        document.addEventListener('DOMContentLoaded', function() {
            // Modal Tambah
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

            document.querySelectorAll('.btnOpenEdit').forEach(btn => {
                btn.addEventListener('click', function() {
                    let id = this.dataset.id;
                    let kelas_id = this.dataset.kelas_id;
                    let nama_diskon = this.dataset.nama_diskon;
                    let persentase = this.dataset.persentase;
                    let tanggal_mulai = this.dataset.tanggal_mulai;
                    let tanggal_berakhir = this.dataset.tanggal_berakhir;

                    // isi form modal
                    document.getElementById('editId').value = id;
                    document.getElementById('editKelasId').value = kelas_id;
                    document.getElementById('editNamaDiskon').value = nama_diskon;
                    document.getElementById('editPersentase').value = persentase;
                    document.getElementById('editTanggalMulai').value = tanggal_mulai;
                    document.getElementById('editTanggalBerakhir').value = tanggal_berakhir;

                    // set action form
                    document.getElementById('formEditDiskon').action = "/diskon/" + id;

                    // tampilkan modal
                    modalEdit.classList.remove("hidden");
                    modalEdit.classList.add("flex");
                });
            });

            btnCloseEdit.addEventListener("click", () => {
                modalEdit.classList.add("hidden");
                modalEdit.classList.remove("flex");
            });
        });