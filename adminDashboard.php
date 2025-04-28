<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: loginPage.php");
    exit();
}
include('dbQueries/db.php');

// Total Count
$sql_total = "SELECT COUNT(*) FROM appointments";
$result_total = $conn->query($sql_total);
$row_total = $result_total->fetch_assoc();
$total_visitors = $row_total['COUNT(*)'];

// Today's Count
$today = date('Y-m-d');
$sql_new_today = "SELECT COUNT(*) FROM appointments WHERE visit_date = '$today'";
$result_new_today = $conn->query($sql_new_today);
$row_new_today = $result_new_today->fetch_assoc();
$new_visitors_today = $row_new_today['COUNT(*)'];

// Currently Checked-in Visitors Count
$sql_checked_in = "SELECT COUNT(*) FROM appointments WHERE checkin_time IS NOT NULL and visit_status = 1";
$result_checked_in = $conn->query($sql_checked_in);
$row_checked_in = $result_checked_in->fetch_assoc();
$checked_in_visitors = $row_checked_in['COUNT(*)'];

// Checked-out Visitors Count
$sql_checked_out = "SELECT COUNT(*) FROM appointments WHERE checkout_time IS NOT NULL and checkin_time IS NOT NULL and visit_status = 0";
$result_checked_out = $conn->query($sql_checked_out);
$row_checked_out = $result_checked_out->fetch_assoc();
$checked_out_visitors = $row_checked_out['COUNT(*)'];

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
    <title>Visitor Management System</title>
</head>
<body>
    <div class="userHeader">
        <h1 id="userGreeting">Admin: <?php echo $_SESSION['name']?></h1>
        <ul>
            <li>
                <a href="adminAddVisitorPage.php">Add Visitor</a>
                <a href="manageVisitorPage.php">Manage Visitors</a>
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
        </div>
    </div>
</body>
</html>