<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Status</title>
    <link rel="stylesheet" href="sstyle.css">  <!-- CSS Link -->
</head>
<body>
<?php
session_start();  // Start session to manage login

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connect to the database
    $conn = new mysqli("localhost", "root", "", "user_auth");

    if ($conn->connect_error) {
        die('<div class="message-box error-box">Connection failed: ' . $conn->connect_error . '</div>');
    }

    // Get user input
    $login_identifier = $_POST['login_identifier'];  // Username or email
    $password = $_POST['password'];

    // Validate empty fields
    if (empty($login_identifier) || empty($password)) {
        echo '<div class="message-box error-box">All fields are required!</div>';
        exit();
    }

    // Check if username/email exists in the database
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $login_identifier, $login_identifier);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 1) {
        // Fetch user details
        $stmt->bind_result($user_id, $username, $hashed_password);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            // Set session variables
            $_SESSION["user_id"] = $user_id;
            $_SESSION["username"] = $username;

            // Redirect to a homepage
             header("Location: dashboard.html?login_success");
            exit();
        } else {
            echo '<div class="message-box error-box">Incorrect password!</div>';
        }
    } else {
        echo '<div class="message-box error-box">User does not exist!</div>';
    }

    $stmt->close();
    $conn->close();
}
?>