document.addEventListener("DOMContentLoaded", () => {
    /* ===========================
     * FILTER FORM
     * ===========================*/
    const formFilter = document.querySelector("form[action*='admin/schedules']");
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

    /* ===========================
     * SUBMIT FORM (CREATE / UPDATE)
     * ===========================*/
    const scheduleForm = document.getElementById("scheduleForm");
    if (scheduleForm) {
        scheduleForm.addEventListener("submit", async function (e) {
            e.preventDefault();

            const kelasEl = document.getElementById("kelas_id");
            const trainerEl = document.getElementById("trainer_id");
            const dayEl = document.getElementById("day");
            const startEl = document.getElementById("start_time");
            const endEl = document.getElementById("end_time");
            const focusEl = document.getElementById("class_focus");
            const activeEl = document.getElementById("is_active");
            const scheduleIdEl = document.getElementById("schedule_id");

            if (!kelasEl || !trainerEl || !dayEl || !startEl || !endEl || !focusEl || !activeEl || !scheduleIdEl) {
                alert("Form belum lengkap, cek console!");
                console.error("Form element missing", { kelasEl, trainerEl, dayEl, startEl, endEl, focusEl, activeEl, scheduleIdEl });
                return;
            }

            if (!dayEl.value) {
                alert("Pilih hari dulu!");
                return;
            }

            const payload = {
                kelas_id: kelasEl.value,
                trainer_id: trainerEl.value,
                day: dayEl.value,
                start_time: startEl.value,
                end_time: endEl.value,
                class_focus: focusEl.value,
                is_active: activeEl.value === "1" ? 1 : 0
            };

            const id = scheduleIdEl.value;
            const url = id ? `/admin/schedules/${id}` : `/admin/schedules`;
            const method = id ? "PUT" : "POST";

            try {
                const response = await fetch(url, {
                    method: method,
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify(payload)
                });

                if (!response.ok) {
                    const text = await response.text();
                    console.error(text);
                    alert("Terjadi error server, cek console");
                    return;
                }

                const res = await response.json();

                if (res.success) {
                    closeModal();
                    // update table tanpa reload bisa ditambahkan di sini jika mau
                    location.reload();
                } else {
                    alert(res.message || "Gagal menyimpan jadwal");
                }

            } catch (err) {
                console.error(err);
                alert("Terjadi error: " + err.message);
            }
        });
    }
});

/* ===========================
 * MODAL
 * ===========================*/
function openModalTambah() {
    const scheduleForm = document.getElementById("scheduleForm");
    if (!scheduleForm) return;

    scheduleForm.reset();
    document.getElementById("schedule_id").value = "";
    document.getElementById("modalTitle").innerText = "Tambah Jadwal";
    const modal = document.getElementById("modalSchedule");
    modal.classList.remove("hidden");
    modal.classList.add("flex");
}

function closeModal() {
    const modal = document.getElementById("modalSchedule");
    modal.classList.add("hidden");
    modal.classList.remove("flex");
}

/* ===========================
 * EDIT
 * ===========================*/
async function editSchedule(id) {
    try {
        const response = await fetch(`/admin/schedules/${id}/edit`, {
            headers: { "Accept": "application/json" }
        });
        const data = await response.json();

        document.getElementById("schedule_id").value = data.id;
        document.getElementById("kelas_id").value = data.kelas_id;
        document.getElementById("trainer_id").value = data.trainer_id;
        document.getElementById("day").value = data.day;
        document.getElementById("start_time").value = data.start_time;
        document.getElementById("end_time").value = data.end_time;
        document.getElementById("class_focus").value = data.class_focus ?? "";
        document.getElementById("is_active").value = data.is_active;

        document.getElementById("modalTitle").innerText = "Edit Jadwal";
        const modal = document.getElementById("modalSchedule");
        modal.classList.remove("hidden");
        modal.classList.add("flex");
    } catch (err) {
        console.error(err);
        alert("Gagal load data jadwal");
    }
}

/* ===========================
 * DELETE
 * ===========================*/
async function deleteSchedule(id) {
    if (!confirm("Hapus jadwal ini?")) return;

    try {
        const response = await fetch(`/admin/schedules/${id}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
                "Accept": "application/json"
            }
        });

        if (!response.ok) {
            const text = await response.text();
            console.error(text);
            alert("Terjadi error server saat hapus");
            return;
        }

        const res = await response.json();

        if (res.success) {
            const btn = document.querySelector(`[data-id='${id}']`);
            if (btn) btn.closest("tr").remove();
            alert(res.message || "Jadwal berhasil dihapus");
        } else {
            alert(res.message || "Gagal menghapus jadwal");
        }
    } catch (err) {
        console.error(err);
        alert("Terjadi error: " + err.message);
    }
}

window.openModalTambah = openModalTambah;
window.closeModal = closeModal;
window.editSchedule = editSchedule;
window.deleteSchedule = deleteSchedule;
