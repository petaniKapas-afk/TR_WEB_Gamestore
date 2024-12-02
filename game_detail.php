<?php
session_start();
require 'koneksi.php';

if (isset($_GET['id'])) {
    $game_id = $_GET['id'];

    $current_time = date('Y-m-d H:i:s');

    $query = "
        SELECT games.*,
            discount_events.discount_type,
            discount_events.discount_value,
            discount_events.start_date,
            discount_events.end_date
        FROM games
        LEFT JOIN discount_event_games ON games.id = discount_event_games.game_id
        LEFT JOIN discount_events ON discount_event_games.event_id = discount_events.id
        WHERE games.id = ?
    ";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $game_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Initialize variables
    $game = null;
    $lowest_discount_price = null;

    while ($row = $result->fetch_assoc()) {
        if (!$game) {
            // First time, store the game data
            $game = $row;
            $original_price = (float)$game['price'];
            $game['discount_price'] = $original_price;
        }

        // Check if discount data exists and if the discount is currently active
        if (!empty($row['discount_type']) &&
            !empty($row['start_date']) &&
            !empty($row['end_date']) &&
            $row['start_date'] <= $current_time &&
            $row['end_date'] >= $current_time) {

            $original_price = (float)$game['price'];
            $discount_price = $original_price;

            if ($row['discount_type'] == 'percentage') {
                $discount_price = $original_price * (1 - $row['discount_value'] / 100);
            } elseif ($row['discount_type'] == 'fixed') {
                $discount_price = $original_price - $row['discount_value'];
            }

            if ($discount_price < 0) {
                $discount_price = 0;
            }

            // If this discount provides a lower price, use it
            if (!isset($lowest_discount_price) || $discount_price < $lowest_discount_price) {
                $lowest_discount_price = $discount_price;
                $game['discount_price'] = $discount_price;
                $game['has_discount'] = true;
                $game['discount_type'] = $row['discount_type'];
                $game['discount_value'] = $row['discount_value'];
            }
        }
    }

    // If no discount was applied, set discount price to original price
    if (!isset($game['has_discount'])) {
        $game['discount_price'] = $game['price'];
    }

    // Handle case where game is not found
    if (!$game) {
        die('Game not found.');
    }

} else {
    die('Game ID not specified.');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Game</title>
    <link rel="stylesheet" href="game_detail.css">
</head>

<body>
    <div class="container">
        <h1><?php echo htmlspecialchars($game['title']); ?></h1>

        <!-- Gambar Game -->
        <div class="game-image">
            <div class="image-wrapper">
                <img src="uploads/<?php echo htmlspecialchars($game['image']); ?>" alt="<?php echo htmlspecialchars($game['title']); ?>">
            </div>
        </div>

        <!-- Informasi Game -->
        <div class="game-info">
            <h3>Detail Game</h3>
            <?php
                $original_price = (float)$game['price'];
                $discount_price = (float)$game['discount_price'];
            ?>
            <?php if (isset($game['has_discount']) && $discount_price < $original_price): ?>
                <p><strong>Harga:</strong> <span class="original-price">Rp <?php echo number_format($original_price, 2, ',', '.'); ?></span> <span class="discount-price">Rp <?php echo number_format($discount_price, 2, ',', '.'); ?></span></p>
            <?php else: ?>
                <p><strong>Harga:</strong> Rp <?php echo number_format($original_price, 2, ',', '.'); ?></p>
            <?php endif; ?>
            <p><strong>Deskripsi:</strong> <?php echo nl2br(htmlspecialchars($game['description'])); ?></p>
            <p><strong>Stok:</strong> <?php echo htmlspecialchars($game['stock']); ?></p>
        </div>

        <!-- Tombol -->
        <div class="button-container">
            <a href="javascript:history.back()" class="back-button">Kembali</a>
            <a href="buy_game.php?id=<?php echo $game['id']; ?>" class="buy-button">Beli Sekarang</a>
        </div>
    </div>
</body>

</html>


