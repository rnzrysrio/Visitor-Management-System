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

function toggleCustomCheckoutTime() {
    const checkOutTimeSelect = document.getElementById('editCheckOut');
    const customCheckOutTimeInput = document.getElementById('customCheckOutTime');
    
    if (checkOutTimeSelect.value === 'other') {
        customCheckOutTimeInput.style.display = 'block';
    } else {
        customCheckOutTimeInput.style.display = 'none';
    }
}


function toggleEditVisitorInfoModal(appointment) {
    var modal = document.getElementById("editVisitorInfoModal");

    if (modal.style.display === "block") {
        modal.style.display = "none";
    } else {
        // Set values
        document.getElementById("appointment_id").value = appointment.id;
        document.getElementById("editName").value = appointment.name;
        document.getElementById("editEmail").value = appointment.email;
        document.getElementById("editPhone").value = appointment.phone;
        document.getElementById("editVisitDate").value = appointment.visit_date;
        document.getElementById("editCheckIn").value = appointment.checkin_time;
        document.getElementById("editCheckOut").value = appointment.checkout_time;
        document.getElementById("editPurpose").value = appointment.purpose;
        document.getElementById("editDepartment").value = appointment.department;
        document.getElementById("editStatus").value = appointment.visit_status;

        modal.style.display = "block";
    }
}

function confirmDelete(appointmentId) {
    // Confirm the deletion
    if (confirm("Are you sure you want to delete this record?")) {
        // Make an AJAX request to delete the record
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "dbQueries/deleteVisitorInfo.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onload = function () {
            if (xhr.status == 200) {
                alert("Record deleted successfully.");
                location.reload(); // Refresh the page to update the list
            } else {
                alert("Error deleting record.");
            }
        };

        xhr.send("id=" + appointmentId);
    }
}


document.querySelector(".closeBtn").addEventListener("click", function() {
    document.getElementById("editVisitorInfoModal").style.display = "none";
});

function toggleProfile() {
    var dropdown = document.getElementById("dropdown-content");
    var dropdownBtn = document.getElementById("dropbtn");

    if (dropdown.style.display === "block") {
        dropdown.style.display = "none";
        dropdownBtn.innerHTML = "&#9662;"; // ▼
    } else {
        dropdown.style.display = "block";
        dropdownBtn.innerHTML = "&#9652;"; // ▲
    }
}