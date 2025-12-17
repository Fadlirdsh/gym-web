document.addEventListener("DOMContentLoaded", () => {

    /* =====================================================
       FILTER FORM — Auto-submit + Reset
    =====================================================*/
    const formFilter = document.querySelector("form[action*='admin/schedules']");
    if (formFilter) {
        formFilter.querySelectorAll("select, input").forEach(el => {
            el.addEventListener("change", () => formFilter.submit());
        });

        const btnReset = formFilter.querySelector("a.btn-gray");
        if (btnReset) {
            btnReset.addEventListener("click", (e) => {
                e.preventDefault();
                btnReset.classList.add("opacity-50");
                setTimeout(() => window.location.href = formFilter.getAttribute("action"), 200);
            });
        }
    }

    /* =====================================================
       SUBMIT FORM — CREATE / UPDATE
    =====================================================*/
    const scheduleForm = document.getElementById("scheduleForm");
    if (scheduleForm) {
        const btnSubmit = scheduleForm.querySelector("button[type='submit']");
        scheduleForm.addEventListener("submit", async (e) => {
            e.preventDefault();
            btnSubmit.disabled = true;

            const payload = collectFormData();
            if (!payload.day) { showToast("Pilih hari terlebih dahulu", "error"); btnSubmit.disabled=false; return; }

            const id = document.getElementById("schedule_id").value;
            const url = id ? `/admin/schedules/${id}` : `/admin/schedules`;
            const method = id ? "PUT" : "POST";

            try {
                btnSubmit.innerHTML = `<span class="loader mr-2"></span> Menyimpan...`;

                const response = await fetch(url, {
                    method, 
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
                        "Content-Type": "application/json",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify(payload)
                });

                const res = await response.json();
                if (!response.ok) throw new Error(res.message || "Server error");

                if (res.success) {
                    showToast("Jadwal berhasil disimpan!", "success");
                    closeModal();
                    localStorage.setItem("highlightSchedule", "1");
                    setTimeout(() => location.reload(), 400);
                } else {
                    showToast(res.message || "Gagal menyimpan jadwal", "error");
                }
            } catch (err) {
                console.error(err);
                showToast(err.message, "error");
            } finally {
                btnSubmit.disabled = false;
                btnSubmit.innerHTML = "Simpan";
            }
        });
    }
});

/* =====================================================
   COLLECT FORM DATA
=====================================================*/
function collectFormData() {
    return {
        kelas_id: document.getElementById("kelas_id").value,
        trainer_id: document.getElementById("trainer_id").value,
        day: document.getElementById("day").value,
        start_time: document.getElementById("start_time").value,
        end_time: document.getElementById("end_time").value,
        class_focus: document.getElementById("class_focus").value,
        is_active: document.getElementById("is_active").value==="1"?1:0
    };
}

/* =====================================================
   MODAL SYSTEM — Open / Close Smooth
=====================================================*/
function openModalTambah() {
    const form = document.getElementById("scheduleForm");
    form.reset();
    document.getElementById("schedule_id").value = "";
    document.getElementById("modalTitle").innerText = "Tambah Jadwal";
    showModal();
}

function showModal() {
    const modal = document.getElementById("modalSchedule");
    const box = modal.querySelector(".modal-box");

    modal.classList.add("flex");
    modal.classList.remove("hidden");

    box.classList.add("opacity-0","scale-90");
    setTimeout(() => {
        box.classList.remove("opacity-0","scale-90");
        box.classList.add("opacity-100","scale-100");
    }, 20);
}

function closeModal() {
    const modal = document.getElementById("modalSchedule");
    const box = modal.querySelector(".modal-box");

    box.classList.add("opacity-0","scale-95");
    setTimeout(() => {
        modal.classList.add("hidden");
        modal.classList.remove("flex");
        box.classList.remove("opacity-0","scale-95");
    }, 180);
}

/* =====================================================
   EDIT — Prefill + Show Modal
=====================================================*/
async function editSchedule(id) {
    try {
        const res = await fetch(`/admin/schedules/${id}/edit`, { headers: { "Accept": "application/json" } });
        const data = await res.json();

        document.getElementById("schedule_id").value = data.id;
        document.getElementById("kelas_id").value = data.kelas_id;
        document.getElementById("trainer_id").value = data.trainer_id;
        document.getElementById("day").value = data.day;
        document.getElementById("start_time").value = data.start_time;
        document.getElementById("end_time").value = data.end_time;
        document.getElementById("class_focus").value = data.class_focus ?? "";
        document.getElementById("is_active").value = data.is_active;

        document.getElementById("modalTitle").innerText = "Edit Jadwal";
        showModal();
    } catch (err) {
        console.error(err);
        showToast("Gagal memuat data jadwal", "error");
    }
}

/* =====================================================
   DELETE
=====================================================*/
async function deleteSchedule(id) {
    if (!confirm("Yakin ingin menghapus jadwal ini?")) return;

    try {
        const res = await fetch(`/admin/schedules/${id}`, {
            method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": document.querySelector("meta[name='csrf-token']").content,
                "Accept": "application/json"
            }
        }).then(r => r.json());

        if (res.success) {
            showToast("Jadwal berhasil dihapus","success");
            setTimeout(()=>location.reload(),500);
        } else showToast(res.message,"error");

    } catch (err) { console.error(err); showToast("Gagal menghapus jadwal","error"); }
}

/* =====================================================
   TOAST SYSTEM
=====================================================*/
function showToast(message,type="success"){
    const toast=document.createElement("div");
    toast.className=`fixed bottom-6 right-6 px-4 py-3 rounded-xl shadow-lg text-white text-sm font-semibold z-[9999] animate-slide-up ${type==="error"?"bg-red-600":"bg-green-600"}`;
    toast.innerText=message;
    document.body.appendChild(toast);
    setTimeout(()=>toast.remove(),2500);
}

/* =====================================================
   GLOBAL ANIMATIONS
=====================================================*/
const style=document.createElement("style");
style.innerHTML=`
@keyframes slide-up { from {transform:translateY(20px);opacity:0;} to {transform:translateY(0);opacity:1;} }
.animate-slide-up { animation: slide-up .25s ease-out; }
.loader { width:16px; height:16px; border:2px solid white; border-top-color:transparent; border-radius:50%; display:inline-block; animation: spin .6s linear infinite; }
@keyframes spin { to { transform: rotate(360deg); } }
.modal-box { transition:0.18s ease; }
`;
document.head.appendChild(style);

/* =====================================================
   EXPORT
=====================================================*/
window.openModalTambah = openModalTambah;
window.closeModal = closeModal;
window.editSchedule = editSchedule;
window.deleteSchedule = deleteSchedule;
