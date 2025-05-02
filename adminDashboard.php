<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: loginPage.php");
    exit();
}
include('dbQueries/db.php');
include('dbQueries/autoStatusManager.php');

// Total Visitors Count (excluding rejected and pending appointments)
$sql_total = "SELECT COUNT(*) FROM appointments WHERE appointment_status = 1";
$result_total = $conn->query($sql_total);
$row_total = $result_total->fetch_assoc();
$total_visitors = $row_total['COUNT(*)'];

// New Visitors Today Count (excluding rejected and pending appointments)
$sql_new_today = "SELECT COUNT(*) FROM appointments WHERE visit_date = '$today' AND appointment_status = 1";
$result_new_today = $conn->query($sql_new_today);
$row_new_today = $result_new_today->fetch_assoc();
$new_visitors_today = $row_new_today['COUNT(*)'];

// Checked In Visitors Count (excluding rejected and pending appointments)
$sql_checked_in = "SELECT COUNT(*) FROM appointments WHERE checkin_time IS NOT NULL AND visit_status = 1 AND appointment_status = 1";
$result_checked_in = $conn->query($sql_checked_in);
$row_checked_in = $result_checked_in->fetch_assoc();
$checked_in_visitors = $row_checked_in['COUNT(*)'];

// Checked Out Visitors Count (excluding rejected and pending appointments)
$sql_checked_out = "SELECT COUNT(*) FROM appointments WHERE checkout_time IS NOT NULL AND checkin_time IS NOT NULL AND visit_status = 0 AND appointment_status = 1";
$result_checked_out = $conn->query($sql_checked_out);
$row_checked_out = $result_checked_out->fetch_assoc();
$checked_out_visitors = $row_checked_out['COUNT(*)'];

// Accepted Appointments Count (not rejected, not expired)
$sql_accepted = "SELECT COUNT(*) FROM appointments 
                 WHERE appointment_status = 1";
$result_accepted = $conn->query($sql_accepted);
$accepted_appointments = $result_accepted->fetch_assoc()['COUNT(*)'];

// Rejected Appointments Count
$sql_rejected = "SELECT COUNT(*) FROM appointments 
                 WHERE appointment_status = 0";
$result_rejected = $conn->query($sql_rejected);
$rejected_appointments = $result_rejected->fetch_assoc()['COUNT(*)'];

// Pending Appointments Count (still waiting, exclude expired ones)
$sql_pending = "SELECT COUNT(*) FROM appointments 
                WHERE appointment_status = 2 
                AND (
                    visit_date > '$today' OR 
                    (visit_date = '$today' AND STR_TO_DATE(checkin_time, '%h:%i %p') > STR_TO_DATE('$currentTime', '%H:%i:%s'))
                )";
$result_pending = $conn->query($sql_pending);
$pending_appointments = $result_pending->fetch_assoc()['COUNT(*)'];

// Reserved Visitors Count
$sql_reserved = "SELECT COUNT(*) FROM appointments 
                 WHERE (visit_date > '$today' OR (visit_date = '$today' AND STR_TO_DATE(checkin_time, '%h:%i %p') > '$currentTime')) 
                   AND visit_status = 2";
$result_reserved = $conn->query($sql_reserved);
$row_reserved = $result_reserved->fetch_assoc();
$reserved_visitors = $row_reserved['COUNT(*)'];


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Visitor Management System">
    <meta name="keywords" content="Visitor, Management, System, VMS">
    <meta name="charset" content="UTF-8">
    <link rel="stylesheet" href="adminDashboardStyle.css">
    <script src="script.js"></script>
    <title>VMS</title>
</head>
<body>
    <div class="userHeader">
        <h1 id="userGreeting">Admin: <?php echo $_SESSION['name']?></h1>
        <ul>
            <li>
                <a href="adminAddVisitorPage.php">Add Visitor</a>
                <a href="manageVisitorPage.php">Manage Visitors</a>
                <a href="visitorReportPage.php">Visitor Report</a>
            </li>
        </ul>
        <div class="profile-dropdown">
            <button id="dropbtn" class="dropbtn" onclick="toggleProfile()">&#9662;</button>
            <div id="dropdown-content" class="dropdown-content">
                <a href="dbQueries/logout.php">Logout</a>
            </div>
        </div>
    </div>
    <div class="dashboard">
        <h1>Dashboard</h1>
        <hr>
        <div class="card-container">
            <div class="card">
                <h2>Total Visitors</h2>
                <p><?php echo $total_visitors; ?></p>
            </div>
            <div class="card">
                <h2>New Visitors Today</h2>
                <p><?php echo $new_visitors_today; ?></p>
            </div>
            <div class="card">
                <h2>Visitors Checked In</h2>
                <p><?php echo $checked_in_visitors; ?></p>
            </div>
            <div class="card">
                <h2>Visitors Checked Out</h2>
                <p><?php echo $checked_out_visitors; ?></p>
            </div>
            <div class="card">
                <h2>Reserved Visitors</h2>
                <p><?php echo $reserved_visitors; ?></p>
            </div>
            <div class="card">
                <h2>Accepted Appointments</h2>
                <p><?php echo $accepted_appointments; ?></p>
            </div>
            <div class="card">
                <h2>Rejected Appointments</h2>
                <p><?php echo $rejected_appointments; ?></p>
            </div>
            <div class="card">
                <h2>Pending Appointments</h2>
                <p><?php echo $pending_appointments; ?></p>
            </div>
        </div>
    </div>
</body>
</html>