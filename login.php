<?php
session_start();
require 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Pastikan 'login_type' ada sebelum mengaksesnya
    if (isset($_POST['login_type'])) {
        $login_type = $_POST['login_type'];  // Mengambil tipe login (admin/user)

        if ($login_type == 'user') {
            // Query untuk user
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['is_admin'] = false;
                header('Location: dashboard.php');  // Redirect ke halaman dashboard user
            } else {
                echo "Invalid username or password for user!";
            }

        } else if ($login_type == 'admin') {
            // Query untuk admin
            $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? AND password = ?");
            $stmt->bind_param("ss", $username, $password);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $admin = $result->fetch_assoc();
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['username'] = $admin['username'];
                $_SESSION['is_admin'] = true;
                header('Location: admin_dashboard.php');  // Redirect ke halaman admin
            } else {
                echo "Invalid username or password for admin!";
            }
        }
    } else {
        echo "Please select a login type!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <form method="POST" action="">
        <h2>Login</h2>
        <label for="username">Username</label>
        <input type="text" name="username" placeholder="Username" required>
        
        <label for="password">Password</label>
        <input type="password" name="password" placeholder="Password" required>

        <label for="login_type">Login As:</label>
        <select name="login_type" id="login_type" required>
            <option value="user">User</option>
            <option value="admin">Admin</option>
        </select>

        <button type="submit">Login</button>
        <p>Don't have an account? <a href="daftar.php">Register as a User</a></p>

    </form>

</body>
</html>
