document.addEventListener("DOMContentLoaded", () => {
    // Ambil elemen form & input filter
    const formFilter = document.querySelector("form[action*='schedules']");
    const inputClient = document.getElementById("client");
    const inputDate = document.getElementById("date");
    const inputTime = document.getElementById("time");

    if (!formFilter) return;

    // Saat user menekan Enter di input nama client → langsung cari
    if (inputClient) {
        inputClient.addEventListener("keypress", (e) => {
            if (e.key === "Enter") {
                e.preventDefault();
                formFilter.submit();
            }
        });
    }

    // Saat user ganti tanggal → langsung submit
    if (inputDate) {
        inputDate.addEventListener("change", () => {
            formFilter.submit();
        });
    }

    // Saat user ganti jam → langsung submit
    if (inputTime) {
        inputTime.addEventListener("change", () => {
            formFilter.submit();
        });
    }

    // Tambahan: tombol Reset kembali ke halaman tanpa query
    const btnReset = document.querySelector("a.bg-gray-400");
    if (btnReset) {
        btnReset.addEventListener("click", (e) => {
            e.preventDefault();
            window.location.href = formFilter.getAttribute("action");
        });
    }

    console.log("✅ Filter jadwal aktif: pencarian client, tanggal, dan jam siap digunakan.");
});
