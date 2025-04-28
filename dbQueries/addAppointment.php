<?php

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('db.php');  // Database connection

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $visitDate = mysqli_real_escape_string($conn, $_POST['visit-date']);
    $checkInTime = mysqli_real_escape_string($conn, $_POST['checkin']);
    $checkOutTime = mysqli_real_escape_string($conn, $_POST['checkout']);

    $purpose = mysqli_real_escape_string($conn, $_POST['purpose']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);

    // Check if the same appointment is made
    $checkAppointmentDuplication = "SELECT * FROM appointments WHERE name=? AND visit_date=? AND checkin_time=? AND checkout_time=?";
    $stmt = $conn->prepare($checkAppointmentDuplication);
    $stmt->bind_param("ssss", $name, $visitDate, $checkInTime, $checkOutTime);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('You already made an appointment with the same date, check-in time, and check-out time!');</script>";
        header('Location: ../userDashboard.php');
    } else {
        // Insert appointment into the database
        $query = "INSERT INTO appointments (name, email, phone, visit_date, checkin_time, checkout_time, purpose, department) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssss", $name, $email, $phone, $visitDate, $checkInTime, $checkOutTime, $purpose, $department);

        if ($stmt->execute()) {
            echo "<script>alert('Appointment Added Successfully!');</script>";
            if(isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
                header('Location: ../adminDashboard.php');
            } else {
                header('Location: ../userDashboard.php');
            }
        } else {
            echo "<script>alert('Failed to Add Appointment!');</script>";
        }
    }

    $stmt->close();
    $conn->close();
}

?>
