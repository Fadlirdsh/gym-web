document.addEventListener("DOMContentLoaded", () => {

    /* =====================================================
     *  FILTER: jika ada (opsional)
     * =====================================================*/
    const formFilter = document.querySelector("form[action*='schedules']");
    const inputClient = document.getElementById("client");
    const inputDate = document.getElementById("date");
    const inputTime = document.getElementById("time");

    if (formFilter) {
        if (inputClient) {
            inputClient.addEventListener("keypress", (e) => {
                if (e.key === "Enter") {
                    e.preventDefault();
                    formFilter.submit();
                }
            });
        }
        if (inputDate) inputDate.addEventListener("change", () => formFilter.submit());
        if (inputTime) inputTime.addEventListener("change", () => formFilter.submit());

        const btnReset = document.querySelector("a.bg-gray-400");
        if (btnReset) {
            btnReset.addEventListener("click", (e) => {
                e.preventDefault();
                window.location.href = formFilter.getAttribute("action");
            });
        }
    }

    /* =====================================================
     *  CREATE MODAL
     * =====================================================*/
    const createForm = document.getElementById("createForm");
    const createModal = document.getElementById("modalCreate");

    // OPEN CREATE MODAL (tambahkan class flex)
    document.querySelectorAll("[data-open-create]").forEach(btn => {
        btn.addEventListener("click", () => {
            createModal.classList.remove("hidden");
            createModal.classList.add("flex");
        });
    });

    // CLOSE CREATE MODAL (hapus flex)
    if (createModal) {
        createModal.addEventListener("click", (e) => {
            if (e.target === createModal) {
                createModal.classList.add("hidden");
                createModal.classList.remove("flex");
            }
        });
    }

    const closeCreate = document.getElementById("closeCreateModal");
    if (closeCreate) {
        closeCreate.onclick = () => {
            createModal.classList.add("hidden");
            createModal.classList.remove("flex");
        };
    }

    if (createForm) {
        createForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            const formData = new FormData(createForm);

            try {
                const res = await fetch(createForm.action, {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    credentials: 'same-origin'
                });

                const data = await res.json();

                if (res.ok) {
                    createModal.classList.add("hidden");
                    createModal.classList.remove("flex");
                    createForm.reset();
                    alert("Jadwal berhasil ditambahkan!");
                    window.location.reload();
                } else {
                    alert(data.message || "Terjadi kesalahan!");
                }

            } catch (err) {
                console.error(err);
                alert("Terjadi kesalahan saat mengirim data.");
            }
        });
    }

    /* =====================================================
     *  EDIT MODAL
     * =====================================================*/
    const editModal = document.getElementById("modalEdit");
    const editForm = document.getElementById("editForm");
    const closeEdit = document.getElementById("closeEditModal");

    const normalizeTime = (time) => time ? time.slice(0, 5) : "";

    document.querySelectorAll(".btnEdit").forEach(btn => {
        btn.addEventListener("click", () => {

            // OPEN EDIT MODAL
            editModal.classList.remove("hidden");
            editModal.classList.add("flex");

            // action form dari data-url
            editForm.action = btn.dataset.url;

            document.getElementById("editTrainer").value = btn.dataset.trainer;
            document.getElementById("editKelas").value = btn.dataset.kelas;
            document.getElementById("editDay").value = btn.dataset.day;
            document.getElementById("editStart").value = normalizeTime(btn.dataset.start);
            document.getElementById("editEnd").value = normalizeTime(btn.dataset.end);
            document.getElementById("editFocus").value = btn.dataset.focus ?? "";
            document.getElementById("editStatus").value = btn.dataset.status;
        });
    });

    if (editModal) {
        editModal.addEventListener("click", (e) => {
            if (e.target === editModal) {
                editModal.classList.add("hidden");
                editModal.classList.remove("flex");
            }
        });
    }

    if (closeEdit) {
        closeEdit.onclick = () => {
            editModal.classList.add("hidden");
            editModal.classList.remove("flex");
        };
    }

    if (editForm) {
        editForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            const formData = new FormData(editForm);
            formData.append('_method', 'PUT');

            try {
                const res = await fetch(editForm.action, {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
                    },
                    credentials: 'same-origin'
                });

                if (res.ok) {
                    editModal.classList.add("hidden");
                    editModal.classList.remove("flex");
                    alert("Jadwal berhasil diupdate!");
                    window.location.reload();
                } else {
                    const text = await res.text();
                    try {
                        const data = JSON.parse(text);
                        alert(data.message || "Terjadi kesalahan saat update!");
                    } catch {
                        console.error("Response bukan JSON:", text);
                        alert("Terjadi kesalahan saat update jadwal. Periksa console.");
                    }
                }

            } catch (err) {
                console.error(err);
                alert("Terjadi kesalahan saat mengirim data.");
            }
        });
    }
});
