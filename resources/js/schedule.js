/* =====================================================
   GLOBAL SHIFT STATE
=====================================================*/
let ACTIVE_SHIFT = null;


/* =====================================================
   DOM READY
=====================================================*/
document.addEventListener("DOMContentLoaded", () => {

    /* ===============================
       SHIFT FORM SUBMIT
    =============================== */
    const shiftForm = document.getElementById("shiftForm");
    if (shiftForm) {
        const btnSubmit = shiftForm.querySelector("button[type='submit']");

        shiftForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            const shiftId = document.getElementById("shift_id").value;
            const start = document.getElementById("shift_start").value;
            const end = document.getElementById("shift_end").value;

            if (!start || !end) {
                showToast("Jam mulai dan jam selesai wajib diisi", "error");
                return;
            }

            const startMinutes = parseInt(start.split(":")[0]) * 60 + parseInt(start.split(":")[1]);
            const endMinutes = parseInt(end.split(":")[0]) * 60 + parseInt(end.split(":")[1]);
            const duration = endMinutes - startMinutes;

            if (duration < 120) {
                showToast("Durasi shift minimal 2 jam", "error");
                return;
            }

            if (duration > 600) {
                showToast("Shift terlalu panjang (maksimal 10 jam)", "error");
                return;
            }

            const payload = {
                trainer_id: document.getElementById("shift_trainer_id").value,
                day: document.getElementById("shift_day").value,
                shift_start: start,
                shift_end: end,
                is_active: document.getElementById("shift_is_active").value
            };

            const url = shiftId
                ? `/admin/trainer-shifts/${shiftId}`
                : `/admin/trainer-shifts`;

            const method = shiftId ? "PUT" : "POST";

            btnSubmit.disabled = true;
            btnSubmit.innerHTML = `<span class="loader mr-2"></span> Menyimpan...`;

            try {
                const res = await fetch(url, {
                    method,
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                if (!res.ok) throw new Error(data.message || "Gagal menyimpan shift");

                showToast(shiftId ? "Shift berhasil diperbarui" : "Shift berhasil ditambahkan");
                closeModal("modalShift");
                setTimeout(() => location.reload(), 400);

            } catch (err) {
                showToast(err.message, "error");
            } finally {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = "Simpan Shift";
            }
        });
    }


    /* ===============================
       SCHEDULE FORM SUBMIT (FIX FINAL)
    =============================== */
    const scheduleForm = document.getElementById("scheduleForm");
    if (scheduleForm) {
        scheduleForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            const id = document.getElementById("schedule_id").value;

            const payload = {
                trainer_shift_id: document.getElementById("trainer_shift_id").value,
                kelas_id: document.getElementById("kelas_id").value,
                trainer_id: document.getElementById("trainer_id").value,
                day: document.getElementById("day").value,
                start_time: document.getElementById("start_time").value,
                end_time: document.getElementById("end_time").value,
                class_focus: document.getElementById("class_focus").value,
                is_active: document.getElementById("is_active").value
            };

            const url = id
                ? `/admin/schedules/${id}`
                : `/admin/schedules`;

            const method = id ? "PUT" : "POST";

            try {
                const res = await fetch(url, {
                    method,
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                if (!res.ok) throw new Error(data.message);

                showToast(id ? "Jadwal diperbarui" : "Jadwal ditambahkan");
                closeModal("modalSchedule");
                setTimeout(() => location.reload(), 400);

            } catch (e) {
                showToast(e.message, "error");
            }
        });
    }
});


/* =====================================================
   SHIFT CRUD
=====================================================*/
async function editShift(id) {
    const res = await fetch(`/admin/trainer-shifts/${id}`, { headers: { Accept: "application/json" } });
    const data = await res.json();

    document.getElementById("shift_id").value = data.id;
    document.getElementById("shift_trainer_id").value = data.trainer_id;
    document.getElementById("shift_day").value = data.day;
    document.getElementById("shift_start").value = data.shift_start;
    document.getElementById("shift_end").value = data.shift_end;
    document.getElementById("shift_is_active").value = data.is_active ? 1 : 0;

    showModal("modalShift");
}

async function deleteShift(id) {
    if (!confirm("Yakin mau hapus shift ini?")) return;

    const res = await fetch(`/admin/trainer-shifts/${id}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
            "Accept": "application/json"
        }
    });

    const data = await res.json();
    if (!res.ok) throw new Error(data.message);

    showToast("Shift berhasil dihapus");
    setTimeout(() => location.reload(), 400);
}


/* =====================================================
   SHIFT SELECTOR
=====================================================*/
function selectShift(id, trainerName, day, start, end) {
    ACTIVE_SHIFT = { id, trainerName, day, start, end };

    document.getElementById("scheduleSection").classList.remove("hidden");
    document.getElementById("selectedShiftInfo").innerText =
        `${trainerName} • ${day} • ${start} - ${end}`;

    document.getElementById("trainer_shift_id").value = id;
    const daySelect = document.getElementById("day");
    if (daySelect) {
        daySelect.value = day;
        daySelect.disabled = true;
    }
}


/* =====================================================
   SCHEDULE CRUD
=====================================================*/
function openScheduleModal() {
    if (!ACTIVE_SHIFT) {
        showToast("Pilih shift terlebih dahulu", "error");
        return;
    }

    document.getElementById("scheduleForm").reset();
    document.getElementById("schedule_id").value = "";
    document.getElementById("trainer_shift_id").value = ACTIVE_SHIFT.id;
    document.getElementById("day").value = ACTIVE_SHIFT.day;

    showModal("modalSchedule");
}

async function editSchedule(id) {
    const res = await fetch(`/admin/schedules/${id}/edit`, { headers: { Accept: "application/json" } });
    const s = await res.json();

    document.getElementById("schedule_id").value = s.id;
    document.getElementById("trainer_shift_id").value = s.trainer_shift_id;
    document.getElementById("kelas_id").value = s.kelas_id;
    document.getElementById("trainer_id").value = s.trainer_id;
    document.getElementById("day").value = s.day;
    document.getElementById("start_time").value = s.start_time;
    document.getElementById("end_time").value = s.end_time;
    document.getElementById("class_focus").value = s.class_focus ?? "";
    document.getElementById("is_active").value = s.is_active ? 1 : 0;

    showModal("modalSchedule");
}

async function deleteSchedule(id) {
    if (!confirm("Yakin hapus jadwal ini?")) return;

    const res = await fetch(`/admin/schedules/${id}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
            "Accept": "application/json"
        }
    });

    const data = await res.json();
    if (!res.ok) throw new Error(data.message);

    showToast("Jadwal berhasil dihapus");
    setTimeout(() => location.reload(), 400);
}


/* =====================================================
   MODAL & TOAST
=====================================================*/
function showModal(id) {
    const modal = document.getElementById(id);
    modal.classList.remove("hidden");
    modal.classList.add("flex");
}

function closeModal(id) {
    const modal = document.getElementById(id);
    modal.classList.add("hidden");
    modal.classList.remove("flex");
}

function showToast(message, type = "success") {
    const toast = document.createElement("div");
    toast.style.position = "fixed";
    toast.style.bottom = "24px";
    toast.style.right = "24px";
    toast.style.zIndex = "999999";
    toast.style.padding = "12px 16px";
    toast.style.borderRadius = "12px";
    toast.style.fontWeight = "600";
    toast.style.color = "#fff";
    toast.style.backgroundColor = type === "error" ? "#dc2626" : "#16a34a";
    toast.innerText = message;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 2500);
}


/* =====================================================
   EXPORT
=====================================================*/
window.selectShift = selectShift;
window.editShift = editShift;
window.deleteShift = deleteShift;
window.openShiftModal = () => showModal("modalShift");
window.closeModal = closeModal;

window.openScheduleModal = openScheduleModal;
window.editSchedule = editSchedule;
window.deleteSchedule = deleteSchedule;
