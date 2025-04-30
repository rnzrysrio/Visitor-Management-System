<?php

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: loginPage.php"); // Redirect to login page if not logged in
    exit();
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
    <link rel="stylesheet" href="adminAddVisitorPage.css">
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

    <div class="addAppointment" id="addAppointment">
        <h1>Add Appointment</h1>
        <form action="dbQueries/addAppointment.php" method="post">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" require>

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

            <input type="hidden" id="encoder" name="encoder" value="<?php echo "Admin " . $_SESSION['name']; ?>">

            <button type="submit" value="submit">Submit Appointment</button>
        </form>
    </div>
</body>
</html>