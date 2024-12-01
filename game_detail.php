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
    <link rel="stylesheet" href="game_detail.css">
</head>

<body>
    <div class="container">
        <h1><?php echo $game['title']; ?></h1>

        <div class="game-info">
            <h3>Detail Game</h3>
            <p><strong>Harga:</strong> Rp <?php echo number_format($game['price'], 0, ',', '.'); ?></p>
            <p><strong>Deskripsi:</strong> <?php echo $game['description']; ?></p>
            <p><strong>Stok:</strong> <?php echo $game['stock']; ?></p>
        </div>

        <div class="button-container">
            <a href="javascript:history.back()" class="back-button">Kembali</a>
            <a href="buy_game.php" class="back-button">Beli Sekarang</a>
        </div>
    </div>
</body>

</html>