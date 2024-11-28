<?php
session_start();
require 'koneksi.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php'); // Jika belum login, redirect ke login
    exit();
}

// Cek apakah payment_id dikirimkan melalui POST
if (isset($_POST['payment_id'])) {
    $payment_id = $_POST['payment_id'];

    // Update status pembayaran menjadi terverifikasi (verified = 1)
    $stmt = $conn->prepare("UPDATE payments SET verified = 1 WHERE id = ?");
    $stmt->bind_param("i", $payment_id);

    if ($stmt->execute()) {
        echo "Payment verified successfully!";
    } else {
        echo "Error verifying payment: " . $conn->error;
    }
} else {
    echo "Invalid request!";
}
?>
