<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('db.php');
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM user_accounts WHERE username='$username'";
    $result = mysqli_query($conn, $query);
    $user = mysqli_fetch_assoc($result);

    if ($username === $user['username'] && password_verify($password, $user['password'])) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $user['role'];
        $_SESSION['name'] = $user['name'];

        if ($user['role'] == 'admin') {
            header('Location: ../adminDashboard.php');
        } else {
            header('Location: ../userDashboard.php');
        }
    } else {
        $error_message = "Invalid username or password.";
        header('Location: ../loginPage.php');
        echo "<script>alert('Invalid username or password!');</script>";
    }
}
?>