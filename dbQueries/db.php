<?php
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "visitor_management_system_db";

//Connect
$conn = new mysqli($servername, $username, $password, $dbname);

// Error Checking for Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>