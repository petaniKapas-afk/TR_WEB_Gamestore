<?php
session_start();
require 'koneksi.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

// Cek jika ID game tersedia di URL
if (isset($_GET['id'])) {
    $game_id = $_GET['id'];
} else {
    echo "Game ID is required!";
    exit();
}

// Jika sudah terkonfirmasi dan ada ID, lakukan penghapusan game
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($game_id)) {
    // Query untuk menghapus game
    $query = "DELETE FROM games WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $game_id);

    // Eksekusi query
    if ($stmt->execute()) {
        echo "Game deleted successfully!";
        header('Location: admin_dashboard.php'); // Kembali ke halaman dashboard admin
    } else {
        echo "Error deleting game: " . $conn->error;
    }
}

?>

