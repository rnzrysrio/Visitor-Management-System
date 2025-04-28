<?php
// Start session and check login status
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: loginPage.php"); // Redirect to login if not logged in
    exit();
}

// Include database connection file
include('dbQueries/db.php'); // Adjust the path as necessary

// Default query to fetch all appointments (if no search is made)
$appointments = [];
$searchName = "";

// Check if a search query is made
if (isset($_POST['searchName'])) {
    $searchName = mysqli_real_escape_string($conn, $_POST['searchName']);
    
    // Fetch the appointment history of the specific visitor by name
    $fetchAppointmentHistory = "SELECT * FROM appointments WHERE name LIKE '%$searchName%'";
    $result = mysqli_query($conn, $fetchAppointmentHistory);
    if ($result) {
        $appointments = mysqli_fetch_all($result, MYSQLI_ASSOC); // Fetch all appointments for the user
    } else {
        echo "<script>alert('Failed to fetch appointment history!');</script>";
    }
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
    <link rel="stylesheet" href="manageVisitorPageStyle.css">
    <script src="script.js"></script>
    <title>Manage Visitors</title>
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

    <div class="selectAndEditVisitor">
        <div class="searchVisitor">
            <form method="post">
                <label for="searchName">Search for a Visitor:</label>
                <input type="text" id="searchName" name="searchName" placeholder="Enter visitor name" value="<?php echo $searchName; ?>">
                <button type="submit">Search</button>
            </form>
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
                        <th>Action</th>
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
                            echo "<td id='actionCell'><button class='actionBtn' onclick='toggleEditVisitorInfoModal(" . htmlspecialchars(json_encode($appointment)) . ")'>Edit</button> <button class='actionBtn' onclick='confirmDelete(" . $appointment['id'] . ")'>Delete</button></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='9'>No appointments found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="editVisitorInfoModal" id="editVisitorInfoModal" onclick="toggleEditVisitorInfoModal()"> 
        <div class="modalContent" onclick="event.stopPropagation()">
            <span class="closeBtn" onclick="toggleEditVisitorInfoModal()">X</span>
            <h2>Edit Visitor Information</h2>
            <form id="editVisitorForm" action="dbQueries/updateVisitorInfo.php" method="post">
                <input type="hidden" id="appointment_id" name="appointment_id">

                <label for="editName">Name:</label>
                <input type="text" id="editName" name="editName" required>

                <label for="editEmail">Email:</label>
                <input type="email" id="editEmail" name="editEmail" required>

                <label for="editPhone">Phone:</label>
                <input type="text" id="editPhone" name="editPhone" required>

                <label for="editVisitDate">Visit Date:</label>
                <input type="date" id="editVisitDate" name="editVisitDate" required>

                <label for="editCheckIn">Check In Time:</label>
                <select id="editCheckIn" name="editCheckIn" required>
                    <option value="" disabled selected>Select Check In Time</option>
                    <option value="7:00 AM">7:00 AM</option>
                    <option value="1:00 PM">1:00 PM</option>
                    <option value="6:00 PM">6:00 PM</option>
                </select>

                <label for="editCheckOut">Check Out Time:</label>
                <select id="editCheckOut" name="editCheckOut" required onchange="toggleCustomCheckoutTime()">
                    <option value="" disabled selected>Select Check Out Time</option>
                    <option value="9:00 AM">9:00 AM</option>
                    <option value="3:00 PM">3:00 PM</option>
                    <option value="8:00 PM">8:00 PM</option>
                    <option value="other">Other (Specify Time)</option>
                </select>

                <!-- Input field for custom time (hidden by default) -->
                <input type="text" id="customCheckOutTime" name="customCheckOutTime" placeholder="Enter custom check out time" style="display:none;">


                <label for="editPurpose">Purpose of Visit:</label>
                <textarea id="editPurpose" name="editPurpose"></textarea>

                <label for="editDepartment">Department:</label>
                <select id="editDepartment" name="editDepartment" required>
                    <option value="" disabled selected>Select Department</option>
                    <option value="HR">HR</option>
                    <option value="IT">IT</option>
                    <option value="Finance">Finance</option>
                    <option value="Admin">Admin</option>
                    <option value="Marketing">Marketing</option>
                </select>

                <label for="editStatus">Visit Status:</label>
                <select id="editStatus" name="editStatus" required>
                    <option value="1">Checked-In</option>
                    <option value="0">Checked-Out</option>
                </select>

                <button type="submit">Save Changes</button>
            </form>
        </div>
    </div>
</body>
</html>