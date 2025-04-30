<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: loginPage.php");
    exit();
}

include('dbQueries/db.php');

$appointments = [];
$from = isset($_GET['from']) ? $_GET['from'] : '';
$to = isset($_GET['to']) ? $_GET['to'] : '';

// Validate and prepare the query
if (!empty($from) && !empty($to)) {
    // Validate date range
    $from_date = DateTime::createFromFormat('Y-m-d', $from);
    $to_date = DateTime::createFromFormat('Y-m-d', $to);
    if ($from_date && $to_date && $from_date <= $to_date) {
        // Prepare the query using prepared statements
        $query = "SELECT * FROM appointments WHERE visit_date BETWEEN ? AND ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $from, $to);  // "ss" indicates two string parameters
    } else {
        echo "<script>alert('Invalid date range!');</script>";
        $stmt = null;
    }
} else {
    $query = "SELECT * FROM appointments";
    $stmt = $conn->prepare($query);
}

// Execute query and fetch results
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result) {
        $appointments = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        echo "<script>alert('Failed to fetch appointment history! Error: " . mysqli_error($conn) . "');</script>";
    }
} else {
    echo "<script>alert('Failed to prepare the query!');</script>";
}
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
    <link rel="stylesheet" href="visitorReportPageStyle.css">
    <script src="script.js"></script>
    <title>VMS</title>
</head>
<body>
    <div class="userHeader">
        <button class="backBtn"><a href="adminDashboard.php">&lt Back</a></button>
        <h1 id="userGreeting">Admin: <?php echo $_SESSION['name']?></h1>
        <div class="profile-dropdown">
            <button id="dropbtn" class="dropbtn" onclick="toggleProfile()">&#9662;</button>
            <div id="dropdown-content" class="dropdown-content">
                <a href="dbQueries/logout.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="appointments">
        <div class="filter-form">
            <form method="GET" action="">
                <label for="from">From:</label>
                <input type="date" id="from" name="from" required>
                <label for="to">To:</label>
                <input type="date" id="to" name="to" required>
                <button type="submit">Filter</button>
                <a href="?" style="margin-left: 10px;">Reset</a>
            </form>
            <form method="POST" action="dbQueries/printVisitorReport.php" target="_blank" style="margin-top: 10px;">
                <input type="hidden" name="from" value="<?php echo isset($_GET['from']) ? $_GET['from'] : ''; ?>">
                <input type="hidden" name="to" value="<?php echo isset($_GET['to']) ? $_GET['to'] : ''; ?>">
                <button id="printer" type="submit">Print PDF</button>
            </form>
        </div>
        <table>
            <caption>Appointment History</caption>
            <thead>
                <tr>
                    <th>Visitor Name</th>
                    <th>Email</th>
                    <th>Contact Number</th>
                    <th>Visit Date</th>
                    <th>Check In Time</th>
                    <th>Check Out Time</th>
                    <th>Purpose of Visit</th>
                    <th>Department</th>
                    <th>Visit Status</th>
                    <th>Encoder</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($appointments)) {
                    foreach ($appointments as $appointment) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($appointment['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($appointment['email']) . "</td>";
                        echo "<td>" . htmlspecialchars($appointment['phone']) . "</td>";
                        echo "<td>" . htmlspecialchars($appointment['visit_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($appointment['checkin_time']) . "</td>";
                        echo "<td>" . htmlspecialchars($appointment['checkout_time']) . "</td>";
                        echo "<td>" . htmlspecialchars($appointment['purpose']) . "</td>";
                        echo "<td>" . htmlspecialchars($appointment['department']) . "</td>";
                        echo "<td>" . ($appointment['visit_status'] == '1' ? "<span class='checkInStatus' style='color: green;'>Checked-In</span>" : "<span class='checkInStatus' style='color: red;'>Checked-Out</span>") . "</td>";
                        echo "<td>" . htmlspecialchars($appointment['encoder']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr style='text-align: center;'><td colspan='9'>No appointments found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>