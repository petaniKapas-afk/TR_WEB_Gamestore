<?php
session_start();
require 'koneksi.php';

// Pastikan admin sudah login
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Periksa apakah data form sudah ada
    if (isset($_POST['title'], $_POST['price'], $_POST['description'], $_POST['stock'])) {
        $title = $_POST['title'];
        $price = $_POST['price'];
        $description = $_POST['description'];
        $stock = $_POST['stock'];

        // Query untuk menambah game baru
        $insert_query = "INSERT INTO games (title, description, price, stock) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insert_query);

        // Periksa apakah prepare berhasil
        if ($stmt === false) {
            die('MySQL prepare error: ' . $conn->error);
        }

        // Bind parameter
        $stmt->bind_param("ssdi", $title, $description, $price, $stock);  // "ssdi" untuk string, string, decimal, integer

        if ($stmt->execute()) {
            echo "Game added successfully!";
        } else {
            echo "Error adding game: " . $stmt->error;
        }
    } else {
        echo "Missing game information!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Game</title>
</head>
<body>
    <h2>Add New Game</h2>
    <form method="POST" action="add_game.php">
        <label for="title">Game Title:</label>
        <input type="text" id="title" name="title" required><br>

        <label for="price">Price:</label>
        <input type="number" id="price" name="price" required><br>

        <label for="description">Description:</label>
        <textarea id="description" name="description" required></textarea><br>

        <label for="stock">Stock:</label>
        <input type="number" id="stock" name="stock" required><br>

        <button type="submit">Add Game</button>
    </form>
    <a href="admin_dashboard.php">Kembali</a>
</body>
</html>
