document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirm_password");

    form.addEventListener("submit", function (e) {
        if (password.value !== confirmPassword.value) {
            e.preventDefault();
            alert("Passwords do not match.");
            confirmPassword.focus();
        }
    });
});

function toggleAppointmentModal() {
    const modal = document.getElementById("appointmentModal");
    const modalOverlay = document.getElementById("modalOverlay");
    modal.style.display = modal.style.display === "block" ? "none" : "block";
    modalOverlay.style.display = modalOverlay.style.display === "block" ? "none" : "block";
}
