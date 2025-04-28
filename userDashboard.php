<?php
session_start(); // Start or resume the session
if (!isset($_SESSION['username'])) {
    header("Location: loginPage.php"); // Redirect to login page if not logged in
    exit();
}

// Include database connection file
include('dbQueries/db.php'); // Adjust the path as necessary

// Check if the user is logged in and retrieve user information
if (isset($_SESSION['username'])) {
    $userSessionName = mysqli_real_escape_string($conn, $_SESSION['name']);

    $fetchAppointmentHistory = "SELECT * FROM appointments WHERE name='$userSessionName'";
    $result = mysqli_query($conn, $fetchAppointmentHistory);
    if ($result) {
        $appointments = mysqli_fetch_all($result, MYSQLI_ASSOC); // Fetch all appointments for the user
    } else {
        echo "<script>alert('Failed to fetch appointment history!');</script>";
    }
} else {
    header("Location: loginPage.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="Visitor">
    <meta name="keywords" content="Visitor">
    <meta name="charset" content="UTF-8">
    <link rel="stylesheet" href="userDashboardStyle.css">
    <title>User Home</title>
    <script src="script.js"></script>
</head>
<body>

    <div class="userHeader">
        <h1 id="userGreeting">Hello <?php echo $_SESSION['name']?></h1>
        <div class="profile-dropdown">
            <button id="dropbtn" class="dropbtn" onclick="toggleProfile()">&#9662;</button>
            <div id="dropdown-content" class="dropdown-content">
                <a href="dbQueries/logout.php">Logout</a>
            </div>
        </div>
    </div>

    <div class="appointments">
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
                </tr>
            </thead>
            <tbody>
                <?php
                // Loop through the appointments and display them in the table
                if (isset($appointments) && !empty($appointments)) {
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
                        if ($appointment['visit_status'] == '1') {
                            echo "<td class='checkInStatus' style='color: green;'>Checked-In</td>";
                        } else if ($appointment['visit_status'] == '0') {
                            echo "<td class='checkInStatus' style='color: red;'>Checked-Out</td>";
                        } 
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No appointments found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <button onclick="toggleAppointmentModal()">Add New Appointment</button>
    </div>

    <div id="modalOverlay" onclick="toggleAppointmentModal()"></div>

    <div class="appointmentModal" id="appointmentModal">
        <button class="exitModal" onclick="toggleAppointmentModal()">&times</button>
        <h1>Add Appointment</h1>
        <form action="dbQueries/addAppointment.php" method="post">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo $userSessionName ?>" require readonly>

            <label for="email">Email:</label>
            <input type="text" id="email" name="email" required>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" required>

            <label for="visit-date">Visit Date:</label>
            <input type="date" id="visit-date" name="visit-date" required>

            <label for="checkin">Check In Time:</label>
            <select id="checkin" name="checkin" required>
                    <option value="" disabled selected>Select Check In Time</option>
                    <option value="7:00 AM">7:00 AM</option>
                    <option value="1:00 PM">1:00 PM</option>
                    <option value="6:00 PM">6:00 PM</option>
            </select>

            <label for="checkout">Check Out Time:</label>
            <select id="checkout" name="checkout">
                    <option value="" disabled selected>Select Check In Time</option>
                    <option value="9:00 AM">9:00 AM</option>
                    <option value="3:00 PM">3:00 PM</option>
                    <option value="8:00 PM">8:00 PM</option>
            </select>

            <label for="purpose">Purpose of Visit:</label>
            <textarea id="purpose" name="purpose"></textarea>

            <label for="department">Department:</label>
            <select name="department" id="department" required>
                <option value="" disabled selected>Select Department</option>
                <option value="HR">HR</option>
                <option value="IT">IT</option>
                <option value="Finance">Finance</option>
                <option value="Admin">Admin</option>
                <option value="Marketing">Marketing</option>
            </select>

            <button type="submit" value="submit">Submit Appointment</button>
        </form>
    </div>
</body>
</html>