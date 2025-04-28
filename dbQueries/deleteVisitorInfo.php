<?php
include ('db.php');

if (isset($_POST['id'])) {
    $appointmentId = $_POST['id'];
    
    // Sanitize input
    $appointmentId = mysqli_real_escape_string($conn, $appointmentId);

    // Delete query
    $deleteQuery = "DELETE FROM appointments WHERE id = $appointmentId";

    // Execute the query
    if (mysqli_query($conn, $deleteQuery)) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    echo "Invalid request.";
}
?>
