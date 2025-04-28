<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('db.php');  // Database connection

    $username = $_POST['username'];
    $password = $_POST['password'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    //Username exists?
    $checkUsernameQuery = "SELECT * FROM user_accounts WHERE username='$username'";
    $result = mysqli_query($conn, $checkUsernameQuery);

    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('Username already taken. Please choose a different one.');</script>";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO user_accounts (username, password, name, email, phoneNumber) 
                  VALUES ('$username', '$hashedPassword', '$name', '$email', '$phone')";
        
        if (mysqli_query($conn, $query)) {
            echo "<script>
                    alert('Registration Successful!');
                    if (confirm('Would you like to log in now?')) {
                        window.location.href = '../loginPage.php';
                    } else {
                        window.location.href = '../registerPage.php';
                    }
                  </script>";
        } else {
            echo "<script>alert('Registration Failed!');</script>";
        }
    }
}
?>