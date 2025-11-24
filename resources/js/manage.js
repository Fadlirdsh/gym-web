console.log("manage.js loaded!");

window.openEditModal = function (id, name, email, role) {
    const modal = document.getElementById("editModal");
    modal.classList.remove("hidden");

    document.getElementById("editName").value = name;
    document.getElementById("editEmail").value = email;
    document.getElementById("editRole").value = role;
    document.getElementById("editForm").action = "/users/" + id;
};

window.closeModal = function () {
    document.getElementById("editModal").classList.add("hidden");
};
