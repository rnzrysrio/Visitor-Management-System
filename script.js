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

function updateCheckoutTime() {
    const checkInTime = document.getElementById('editCheckIn').value;
    const checkOutSelect = document.getElementById('editCheckOut');
    const customCheckOutTimeInput = document.getElementById('customCheckOutTime');

    // Remove all existing options
    while (checkOutSelect.options.length > 0) {
        checkOutSelect.remove(0);
    }

    // Define the partner checkout times based on check-in time
    const validCheckoutTimes = {
        '7:00 AM': ['9:00 AM'],
        '1:00 PM': ['3:00 PM'],
        '6:00 PM': ['8:00 PM']
    };

    // Add the valid partner checkout time options for the selected check-in time
    if (validCheckoutTimes[checkInTime]) {
        validCheckoutTimes[checkInTime].forEach(validTime => {
            const option = document.createElement('option');
            option.value = validTime;
            option.text = validTime;
            checkOutSelect.appendChild(option);
        });
    }

    // Add the "Other" option last, if it doesn't already exist
    const otherOption = document.createElement('option');
    otherOption.value = 'other';
    otherOption.text = 'Other';
    checkOutSelect.appendChild(otherOption);

    // Set the value of checkOutSelect based on the existing appointment value (if it exists)
    const currentCheckOutValue = checkOutSelect.dataset.initialValue;
    if (currentCheckOutValue) {
        checkOutSelect.value = currentCheckOutValue;
    }

    // Toggle the visibility of the custom input field and set min/max time range
    toggleCustomCheckoutTime();
}

function toggleCustomCheckoutTime() {
    const checkOutTimeSelect = document.getElementById('editCheckOut');
    const customCheckOutTimeInput = document.getElementById('customCheckOutTime');
    const checkInSelect = document.getElementById('editCheckIn');
    const checkInTime = checkInSelect.value;

    // Check if "Other" option is selected
    if (checkOutTimeSelect.value === 'other') {
        // Show the custom time input field
        customCheckOutTimeInput.style.display = 'block';

        // Partner checkout mapping
        const partnerTimes = {
            '7:00 AM': '9:00 AM',
            '1:00 PM': '3:00 PM',
            '6:00 PM': '8:00 PM'
        };

        // Function to convert 12-hour to 24-hour format
        const to24Hour = timeStr => {
            const [time, modifier] = timeStr.split(' ');
            let [hours, minutes] = time.split(':');
            hours = parseInt(hours, 10);
            if (modifier === 'PM' && hours !== 12) hours += 12;
            if (modifier === 'AM' && hours === 12) hours = 0;
            return `${hours.toString().padStart(2, '0')}:${minutes}`;
        };

        // Set valid range based on selected check-in time
        const minTime = to24Hour(checkInTime);
        const maxTime = to24Hour(partnerTimes[checkInTime]);

        // Set a placeholder for the user to know what time to input
        customCheckOutTimeInput.placeholder = `Enter time between ${minTime} and ${maxTime}`;

        // Add validation for the entered time (optional)
        customCheckOutTimeInput.addEventListener('input', function () {
            const inputValue = customCheckOutTimeInput.value;
            // Simple regex to check if the input is in valid time format (HH:MM AM/PM)
            const timeRegex = /^(0?[1-9]|1[0-2]):([0-5][0-9]) (AM|PM)$/;
            const isValidTime = timeRegex.test(inputValue);

            if (isValidTime) {
                // Convert input time to 24-hour format for comparison
                const input24Hour = to24Hour(inputValue);

                // Check if the input time is within the valid range
                if (input24Hour >= minTime && input24Hour <= maxTime) {
                    customCheckOutTimeInput.setCustomValidity(''); // Reset validity
                } else {
                    customCheckOutTimeInput.setCustomValidity(`Please enter a time between ${minTime} and ${maxTime}.`);
                }
            } else {
                customCheckOutTimeInput.setCustomValidity('Invalid time format. Please enter a valid time (HH:MM AM/PM).');
            }
        });

        customCheckOutTimeInput.required = true;
    } else {
        // Hide the custom time input field
        customCheckOutTimeInput.style.display = 'none';
        customCheckOutTimeInput.required = false;
        customCheckOutTimeInput.setCustomValidity(''); // Reset validity
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
        document.getElementById("editCheckOut").value = appointment.checkout_time; // Ensure this value is set correctly
        document.getElementById("editPurpose").value = appointment.purpose;
        document.getElementById("editDepartment").value = appointment.department;
        document.getElementById("editStatus").value = appointment.visit_status;

        // Store the initial value of the checkout time (to preserve during function calls)
        document.getElementById('editCheckOut').dataset.initialValue = appointment.checkout_time;

        modal.style.display = "block";

        // Update the checkout time options and logic after modal is shown
        updateCheckoutTime();
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