<?php
session_start();

include ('db.php');

// Check if the necessary form fields are set, and sanitize inputs.
$name = isset($_POST['editName']) ? mysqli_real_escape_string($conn, $_POST['editName']) : null;
$email = isset($_POST['editEmail']) ? mysqli_real_escape_string($conn, $_POST['editEmail']) : null;
$phone = isset($_POST['editPhone']) ? mysqli_real_escape_string($conn, $_POST['editPhone']) : null;
$visit_date = isset($_POST['editVisitDate']) ? mysqli_real_escape_string($conn, $_POST['editVisitDate']) : null;
$checkin_time = isset($_POST['editCheckIn']) ? mysqli_real_escape_string($conn, $_POST['editCheckIn']) : null;
$checkout_time = isset($_POST['editCheckOut']) ? mysqli_real_escape_string($conn, $_POST['editCheckOut']) : null;
$customCheckOutTime = mysqli_real_escape_string($conn, $_POST['customCheckOutTime']);
$purpose = isset($_POST['editPurpose']) ? mysqli_real_escape_string($conn, $_POST['editPurpose']) : null;
$department = isset($_POST['editDepartment']) ? mysqli_real_escape_string($conn, $_POST['editDepartment']) : null;
$visit_status = isset($_POST['editStatus']) ? intval($_POST['editStatus']) : 0;
$appointment_status = isset($_POST['editApprovalStatus']) ? intval($_POST['editApprovalStatus']) : 2;
$encoder = isset($_POST['encoder']) ? mysqli_real_escape_string($conn, $_POST['encoder']) : null;

if ($checkout_time === "other" && !empty($customCheckOutTime)) {
    $checkout_time = $customCheckOutTime;
}

// Sanitize appointment ID
$appointment_id = isset($_POST['appointment_id']) ? intval($_POST['appointment_id']) : null;

if ($name && $email && $phone && $visit_date && $checkin_time && $appointment_id !== null) {
    $updateQuery = "UPDATE appointments 
                    SET name = '$name', 
                        email = '$email', 
                        phone = '$phone', 
                        visit_date = '$visit_date', 
                        checkin_time = '$checkin_time', 
                        checkout_time = " . ($checkout_time ? "'$checkout_time'" : "NULL") . ",
                        purpose = '$purpose', 
                        department = '$department', 
                        visit_status = $visit_status,
                        encoder = '$encoder',
                        appointment_status = $appointment_status
                    WHERE id = $appointment_id";

    if (mysqli_query($conn, $updateQuery)) {
        echo "<script>
            alert('Record updated successfully!');
            window.location.href = '../adminDashboard.php';
          </script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "All required fields must be filled.";
}

?>
