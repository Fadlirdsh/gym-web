/* =====================================================
   GLOBAL STATE
===================================================== */
let ACTIVE_SHIFT = null;

/* =====================================================
   HELPERS
===================================================== */
const $ = (id) => document.getElementById(id);

const csrf = () =>
    document.querySelector("meta[name='csrf-token']")?.content;

/* =====================================================
   DOM READY
===================================================== */
document.addEventListener("DOMContentLoaded", () => {

    /* ===============================
       SHIFT FORM SUBMIT
    =============================== */
    const shiftForm = $("shiftForm");
    if (shiftForm) {
        const btnSubmit = shiftForm.querySelector("button[type='submit']");

        shiftForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            const shiftId = $("shift_id").value;
            const start = $("shift_start").value;
            const end = $("shift_end").value;

            if (!start || !end) {
                showToast("Jam mulai dan jam selesai wajib diisi", "error");
                return;
            }

            const toMin = (t) => {
                const [h, m] = t.split(":").map(Number);
                return h * 60 + m;
            };

            const duration = toMin(end) - toMin(start);

            if (duration < 120) {
                showToast("Durasi shift minimal 2 jam", "error");
                return;
            }

            if (duration > 600) {
                showToast("Shift terlalu panjang (maksimal 10 jam)", "error");
                return;
            }

            const payload = {
                trainer_id: $("shift_trainer_id").value,
                day: $("shift_day").value,
                shift_start: start,
                shift_end: end,
                is_active: $("shift_is_active").value
            };

            const url = shiftId ? `/admin/trainer-shifts/${shiftId}` : `/admin/trainer-shifts`;
            const method = shiftId ? "PUT" : "POST";

            btnSubmit.disabled = true;
            btnSubmit.innerText = "Menyimpan...";

            try {
                const res = await fetch(url, {
                    method,
                    headers: {
                        "X-CSRF-TOKEN": csrf(),
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                if (!res.ok) throw new Error(data.message || "Gagal menyimpan shift");

                showToast(shiftId ? "Shift diperbarui" : "Shift ditambahkan");
                closeModal("modalShift");
                setTimeout(() => location.reload(), 400);

            } catch (err) {
                showToast(err.message, "error");
            } finally {
                btnSubmit.disabled = false;
                btnSubmit.innerText = "Simpan Shift";
            }
        });
    }

    /* ===============================
       SCHEDULE FORM SUBMIT
    =============================== */
    const scheduleForm = $("scheduleForm");
    if (scheduleForm) {
        scheduleForm.addEventListener("submit", async (e) => {
            e.preventDefault();

            if (!ACTIVE_SHIFT) {
                showToast("Pilih shift terlebih dahulu", "error");
                return;
            }

            const id = $("schedule_id").value;

            const payload = {
                trainer_shift_id: ACTIVE_SHIFT.id,
                kelas_id: $("kelas_id").value,
                start_time: $("start_time").value,
                end_time: $("end_time").value,
                class_focus: $("class_focus").value,
                is_active: $("is_active").value,
                capacity: $("capacity")?.value ?? null
            };

            const url = id ? `/admin/schedules/${id}` : `/admin/schedules`;
            const method = id ? "PUT" : "POST";

            try {
                const res = await fetch(url, {
                    method,
                    headers: {
                        "X-CSRF-TOKEN": csrf(),
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                if (!res.ok) throw new Error(data.message || "Gagal menyimpan jadwal");

                showToast(id ? "Jadwal diperbarui" : "Jadwal ditambahkan");
                closeModal("modalSchedule");
                setTimeout(() => location.reload(), 400);

            } catch (err) {
                showToast(err.message, "error");
            }
        });
    }
});

/* =====================================================
   SHIFT ACTIONS
===================================================== */
function selectShift(id, trainerName, day, start, end) {
    ACTIVE_SHIFT = { id, trainerName, day, start, end };

    $("scheduleSection")?.classList.remove("hidden");
    $("selectedShiftInfo").innerText =
        `${trainerName} • ${day} • ${start} - ${end}`;

    $("trainer_shift_id").value = id;

    const dayInput = $("day");
    if (dayInput) {
        dayInput.value = day;
        dayInput.disabled = true;
    }
}

function openShiftModal() {
    $("shiftForm")?.reset();
    $("shift_id").value = "";
    showModal("modalShift");
}

async function editShift(id) {
    const res = await fetch(`/admin/trainer-shifts/${id}`, {
        headers: { Accept: "application/json" }
    });

    const s = await res.json();

    document.getElementById("shift_id").value = s.id;
    document.getElementById("shift_trainer_id").value = s.trainer_id;
    document.getElementById("shift_day").value = s.day;
    document.getElementById("shift_start").value = s.shift_start;
    document.getElementById("shift_end").value = s.shift_end;
    document.getElementById("shift_is_active").value = s.is_active ? 1 : 0;

    showModal("modalShift");
}


async function deleteShift(id) {
    if (!confirm("Yakin hapus shift ini?")) return;

    const res = await fetch(`/admin/trainer-shifts/${id}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": csrf(),
            "Accept": "application/json"
        }
    });

    const data = await res.json();
    if (!res.ok) throw new Error(data.message || "Gagal menghapus shift");

    showToast("Shift berhasil dihapus");
    setTimeout(() => location.reload(), 400);
}

/* =====================================================
   SCHEDULE ACTIONS
===================================================== */
function openScheduleModal() {
    if (!ACTIVE_SHIFT) {
        showToast("Pilih shift terlebih dahulu", "error");
        return;
    }

    $("scheduleForm")?.reset();
    $("schedule_id").value = "";
    $("trainer_shift_id").value = ACTIVE_SHIFT.id;

    const dayInput = $("day");
    if (dayInput) dayInput.value = ACTIVE_SHIFT.day;

    showModal("modalSchedule");
}

async function editSchedule(id) {
    const res = await fetch(`/admin/schedules/${id}/edit`, {
        headers: { Accept: "application/json" }
    });

    const s = await res.json();

    $("schedule_id").value = s.id;
    $("trainer_shift_id").value = s.trainer_shift_id;
    $("kelas_id").value = s.kelas_id;
    $("start_time").value = s.start_time;
    $("end_time").value = s.end_time;
    $("class_focus").value = s.class_focus ?? "";
    $("is_active").value = s.is_active ? 1 : 0;
    $("capacity") && ($("capacity").value = s.capacity);

    showModal("modalSchedule");
}

async function deleteSchedule(id) {
    if (!confirm("Yakin hapus jadwal ini?")) return;

    const res = await fetch(`/admin/schedules/${id}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": csrf(),
            "Accept": "application/json"
        }
    });

    const data = await res.json();
    if (!res.ok) throw new Error(data.message || "Gagal menghapus jadwal");

    showToast("Jadwal berhasil dihapus");
    setTimeout(() => location.reload(), 400);
}

/* =====================================================
   MODAL & TOAST
===================================================== */
function showModal(id) {
    const modal = $(id);
    if (!modal) return;
    modal.classList.remove("hidden");
    modal.classList.add("flex");
}

function closeModal(id) {
    const modal = $(id);
    if (!modal) return;
    modal.classList.add("hidden");
    modal.classList.remove("flex");
}

function showToast(message, type = "success") {
    const toast = document.createElement("div");
    toast.style.position = "fixed";
    toast.style.bottom = "24px";
    toast.style.right = "24px";
    toast.style.Index = "999999";
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
   EXPORT (INLINE ONCLICK)
===================================================== */
window.openShiftModal = openShiftModal;
window.editShift = editShift;
window.deleteShift = deleteShift;
window.selectShift = selectShift;

window.openScheduleModal = openScheduleModal;
window.editSchedule = editSchedule;
window.deleteSchedule = deleteSchedule;
window.closeModal = closeModal;
