<?php
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password =$_POST['password'];

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, membership_level) VALUES (?, ?, ?, 'Regular')");
    $stmt->bind_param("sss", $username, $email, $password);
    
    if ($stmt->execute()) {
        echo "Registration successful!";
        header('Location: login.php');
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>
    <form method="POST" action="">
        <h2>Register</h2>
        <input type="text" name="username" placeholder="Username" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" style="display: block; margin: 0.5rem auto; width:80px;">Register</button>

        <p style="text-align:center;">Have an Account ? <a href="login.php">Sign in</a></p>
    </form>
</body>
</html>
