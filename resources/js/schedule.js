document.addEventListener("DOMContentLoaded", () => {

    /* =====================================================
     *  FILTER: client, tanggal, waktu
     * =====================================================*/
    const formFilter = document.querySelector("form[action*='schedules']");
    const inputClient = document.getElementById("client");
    const inputDate = document.getElementById("date");
    const inputTime = document.getElementById("time");

    if (formFilter) {

        // Tekan ENTER di input client → submit
        if (inputClient) {
            inputClient.addEventListener("keypress", (e) => {
                if (e.key === "Enter") {
                    e.preventDefault();
                    formFilter.submit();
                }
            });
        }

        // Ganti tanggal → submit otomatis
        if (inputDate) {
            inputDate.addEventListener("change", () => {
                formFilter.submit();
            });
        }

        // Ganti jam → submit otomatis
        if (inputTime) {
            inputTime.addEventListener("change", () => {
                formFilter.submit();
            });
        }

        // Tombol reset → kembali ke route schedules.index
        const btnReset = document.querySelector("a.bg-gray-400");
        if (btnReset) {
            btnReset.addEventListener("click", (e) => {
                e.preventDefault();
                window.location.href = formFilter.getAttribute("action");
            });
        }
    }



    /* =====================================================
     *  ADD MODAL
     * =====================================================*/
    const addBtn = document.getElementById("btnAddSchedule");
    const addModal = document.getElementById("addScheduleModal");
    const closeAdd = document.getElementById("closeAddModal");

    if (addBtn && addModal && closeAdd) {
        addBtn.onclick = () => addModal.classList.remove("hidden");
        closeAdd.onclick = () => addModal.classList.add("hidden");

        // Klik luar modal untuk close
        addModal.onclick = (e) => {
            if (e.target === addModal) addModal.classList.add("hidden");
        };
    }



    /* =====================================================
     *  EDIT MODAL
     * =====================================================*/
    const editModal = document.getElementById("editScheduleModal");
    const closeEdit = document.getElementById("closeEditModal");

    document.querySelectorAll(".btnEdit").forEach(btn => {
        btn.onclick = function () {

            document.getElementById("editDay").value = this.dataset.day;
            document.getElementById("editTime").value = this.dataset.time;
            document.getElementById("editKelas").value = this.dataset.kelas;
            document.getElementById("editTrainer").value = this.dataset.trainer;
            document.getElementById("editStatus").value = this.dataset.status;

            document.getElementById("editForm").action = "/schedules/" + this.dataset.id;

            editModal.classList.remove("hidden");
        };
    });

    if (closeEdit && editModal) {
        closeEdit.onclick = () => editModal.classList.add("hidden");
    }



    /* =====================================================
     *  DELETE MODAL
     * =====================================================*/
   document.addEventListener("DOMContentLoaded", () => {

    document.querySelectorAll(".deleteForm").forEach(form => {
        form.addEventListener("submit", function (e) {

            const confirmDelete = confirm("Yakin ingin menghapus jadwal ini?");
            if (!confirmDelete) {
                e.preventDefault(); // batal submit
            }

        });
    });

});
    // console.log("✅ schedule.js loaded tanpa duplikasi variabel.");
});
