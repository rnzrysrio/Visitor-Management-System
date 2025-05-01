<?php
include('dbQueries/db.php');

date_default_timezone_set('Asia/Manila'); // Set your timezone if needed

$today = date('Y-m-d');
$currentTime = date('H:i:s'); // Current time in 24-hour format

// Update the status to 0 (checkout) for today if the checkout time has passed
$sql = "UPDATE appointments 
        SET visit_status = 0 
        WHERE visit_date = '$today'
          AND STR_TO_DATE(checkout_time, '%h:%i %p') <= STR_TO_DATE('$currentTime', '%H:%i:%s')
          AND  (visit_status != 0)";

mysqli_query($conn, $sql);

// Update the status to 2 (waiting) for future appointments (reservations)
$sql_reservation = "UPDATE appointments 
                    SET visit_status = 2 
                    WHERE visit_date > '$today'
                      AND (visit_status != 2)";

mysqli_query($conn, $sql_reservation);
?>