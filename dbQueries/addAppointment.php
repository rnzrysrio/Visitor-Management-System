<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('db.php');  // Database connection

    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $visitDate = $_POST['visit-date'];
    $checkInTime = $_POST['checkin'];
    $checkOutTime = $_POST['checkout'];
    $purpose = $_POST['purpose'];
    $department = $_POST['department'];

   // Check if the same appointment is made
   $checkAppointmentDuplication = "SELECT * FROM appointments WHERE name='$name' AND visit_date='$visitDate' AND checkin_time='$checkInTime' AND checkout_time='$checkOutTime'";
   $result = mysqli_query($conn, $checkAppointmentDuplication);

   if (mysqli_num_rows($result) > 0) {
    echo "<script>alert('You already made an appointment with the same date, check-in time, and check-out time!');</script>";
   }
   else{
        // Insert appointment into the database
        $query = "INSERT INTO appointments (name, email, phone, visit_date, checkin_time, checkout_time, purpose, department) VALUES ('$name', '$email', '$phone', '$visitDate', '$checkInTime', '$checkOutTime', '$purpose', '$department')";

        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Appointment Added Successfully!');</script>";
            header('Location: ../userDashboard.php');
        } else {
            echo "<script>alert('Failed to Add Appointment!');</script>";
        }
    }
}
?>