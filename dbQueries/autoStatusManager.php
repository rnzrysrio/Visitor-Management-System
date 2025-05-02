<?php
include('dbQueries/db.php');
date_default_timezone_set('Asia/Manila');

$today = date('Y-m-d');
$currentTime = date('H:i:s'); // 24-hour format

/*
1. Set visit_status to 3 = Cancelled/Did Not Attend for appointments created today 
   but have a visit_date for yesterday or earlier, 
   or for today but with check-in and check-out time less than the current time.
2. Mark future and later-today appointments as reserved (visit_status = 2)
3. Reject expired PENDING appointments from previous days
4. Reject today’s PENDING appointments if current time has passed their checkout_time
5. Mark as cancelled (visit_status = 3) if approved but still reserved past checkout time
*/


//REJECT AND CANCEL APPOINTMENTS

// 1. Reject expired PENDING appointments from previous days
$sql_reject_past = "UPDATE appointments 
                    SET appointment_status = 0, visit_status = 3, attendance = 0
                    WHERE visit_date < '$today' 
                      AND appointment_status = 2";
mysqli_query($conn, $sql_reject_past);

// 2. Reject today’s PENDING appointments if current time has passed their checkout_time
$sql_reject_today = "UPDATE appointments 
                     SET appointment_status = 0, visit_status = 3, attendance = 0
                     WHERE visit_date = '$today' 
                       AND STR_TO_DATE(checkout_time, '%h:%i %p') <= STR_TO_DATE('$currentTime', '%H:%i:%s')
                       AND appointment_status = 2";
mysqli_query($conn, $sql_reject_today);

// 3. Mark as Cancelled/Did Not Attend (visit_status = 3) for future appointments that are denied
$sql_cancel_denied = "UPDATE appointments 
                       SET visit_status = 3, attendance = 0
                       WHERE appointment_status = 0 
                         AND visit_status = 2";
mysqli_query($conn, $sql_cancel_denied);

// 4. Mark as Cancelled/Did Not Attend (visit_status = 3) for appointments created today but have a visit_date for yesterday or earlier or visit date for today but check out less than current time
$sql_cancel_past = "UPDATE appointments 
                     SET visit_status = 3, attendance = 0
                     WHERE appointment_registration_date = '$today' 
                       AND (
                           visit_date < '$today' 
                           OR (
                               visit_date = '$today' 
                               AND STR_TO_DATE(checkout_time, '%h:%i %p') <= STR_TO_DATE('$currentTime', '%H:%i:%s')
                           )
                       )
                       AND visit_status = 2";
mysqli_query($conn, $sql_cancel_past);

// 5. Mark as Cancelled/Did Not Attend (visit_status = 3) for APPROVED appointment created today but no-show visitor
$sql_cancel_did_not_attend = "UPDATE appointments 
                              SET visit_status = 3, attendance = 0
                              WHERE appointment_registration_date = '$today' AND appointment_status = 1
                                AND visit_date = '$today' 
                                AND STR_TO_DATE(checkout_time, '%h:%i %p') <= STR_TO_DATE('$currentTime', '%H:%i:%s')
                                AND visit_status = 2";
mysqli_query($conn, $sql_cancel_did_not_attend);

// 6. Set attendance to 1 for checked-in appointments that are not cancelled
$sql_attended = "UPDATE appointments 
                    SET attendance = 1 
                    WHERE visit_status = 1 
                      AND appointment_status = 1 
                      AND visit_date = '$today' 
                      AND STR_TO_DATE(checkin_time, '%h:%i %p') <= STR_TO_DATE('$currentTime', '%H:%i:%s')";
mysqli_query($conn, $sql_attended);

// 7. Set attendance to 2 for pending appointments
$sql_pendingAttendance = "UPDATE appointments 
                    SET attendance = 2
                    WHERE visit_status = 2
                      AND appointment_status = 2";
mysqli_query($conn, $sql_pendingAttendance);

// 8. Mark future and later-today appointments as reserved (visit_status = 2)
$sql_future = "UPDATE appointments 
               SET visit_status = 2 
               WHERE (
                   visit_date > '$today' 
                   OR (visit_date = '$today' AND STR_TO_DATE(checkin_time, '%h:%i %p') > STR_TO_DATE('$currentTime', '%H:%i:%s'))
               )
               AND visit_status NOT IN (1, 3, 4)"; 
mysqli_query($conn, $sql_future);

// 9. Mark as visit_status = 2 (pending) when appointment status is reserved and attendance is pending
$sql_reservedVisitStatus = "UPDATE appointments 
                    SET visit_status = 2
                    WHERE appointment_status = 2
                      AND attendance = 2";
mysqli_query($conn, $sql_reservedVisitStatus);

// 10. Set attendance to 1 for appointments that are checked-out and approved
$sql_attendedCheckout = "UPDATE appointments 
                    SET attendance = 1 
                    WHERE visit_status = 0 
                      AND appointment_status = 1";

mysqli_query($conn, $sql_attendedCheckout);
?>
