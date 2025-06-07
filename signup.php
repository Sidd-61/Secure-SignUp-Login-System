<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Signup Status</title>
    <link rel="stylesheet" href="sstyle.css">  <!-- CSS Link -->
</head>
<body>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "user_auth");

    if ($conn->connect_error) {
        die('<div class="message-box error-box">Connection failed: ' . $conn->connect_error . '</div>');
    }

    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        echo '<div class="message-box error-box">Error: Passwords do not match!</div>';
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Use prepared statement to check if username or email already exists
    $stmt_check = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt_check->bind_param("ss", $username, $email);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo '<div class="message-box error-box">This username or email already exists.</div>';
        exit();
    }

    $stmt_check->close();

    // Insert into database securely
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);

   if ($stmt->execute()) {
    echo '<div class="message-box success-box">
            Signup successful! <br>
            <a href="login.html" class="login-link">Click here to login</a>
          </div>';
} else {
    echo '<div class="message-box error-box">Error: Could not complete signup.</div>';
}

    $stmt->close();
    $conn->close();
}
?>