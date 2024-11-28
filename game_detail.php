<?php
session_start();
require 'koneksi.php';

if (isset($_GET['id'])) {
    $game_id = $_GET['id'];
    // Ambil data game dari database
    $query = "SELECT * FROM games WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $game_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $game = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Game</title>
</head>
<body>
    <h1><?php echo $game['title']; ?></h1>
    <p><strong>Harga:</strong> <?php echo $game['price']; ?></p>
    <p><strong>Deskripsi:</strong> <?php echo $game['description']; ?></p>
    <p><strong>Stok:</strong> <?php echo $game['stock']; ?></p>
</body>
</html>
