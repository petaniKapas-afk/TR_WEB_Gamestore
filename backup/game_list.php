<?php
require 'koneksi.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_param = '%' . $search . '%';

// Prepare and execute the SQL statement
$stmt = $conn->prepare("
    SELECT games.*,
        discount_events.discount_type,
        discount_events.discount_value,
        discount_events.start_date,
        discount_events.end_date
    FROM games
    LEFT JOIN discount_event_games ON games.id = discount_event_games.game_id
    LEFT JOIN discount_events ON discount_event_games.event_id = discount_events.id
    WHERE games.title LIKE ?
    ORDER BY games.title ASC
");
$stmt->bind_param('s', $search_param);
$stmt->execute();
$result = $stmt->get_result();

// Fetch all games into an array to handle multiple discounts per game if necessary
$games = [];
while ($row = $result->fetch_assoc()) {
    $game_id = $row['id'];
    if (!isset($games[$game_id])) {
        $games[$game_id] = $row;
        // Initialize with original price
        $games[$game_id]['discount_price'] = (float) $row['price'];
    }

    // Check if discount data exists and if the discount is currently active
    $current_time = date('Y-m-d H:i:s');
    if (
        !empty($row['discount_type']) &&
        !empty($row['start_date']) &&
        !empty($row['end_date']) &&
        $row['start_date'] <= $current_time &&
        $row['end_date'] >= $current_time
    ) {

        $original_price = (float) $row['price'];
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
        if ($discount_price < $games[$game_id]['discount_price']) {
            $games[$game_id]['discount_price'] = $discount_price;
            $games[$game_id]['has_discount'] = true;
            $games[$game_id]['discount_type'] = $row['discount_type'];
            $games[$game_id]['discount_value'] = $row['discount_value'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Game Store</title>
    <link rel="stylesheet" href="game_list.css">
</head>

<body>
    <div id="game-list" class="game-list">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search games..."
                value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>

        <div class="game-grid">
            <?php foreach ($games as $game): ?>
                <div class="game-item">
                    <a href="game_detail.php?id=<?php echo $game['id']; ?>">
                        <!-- Gambar game -->
                        <img src="uploads/<?php echo htmlspecialchars($game['image']); ?>"
                            alt="<?php echo htmlspecialchars($game['title']); ?>">

                        <!-- Informasi game -->
                        <div class="game-info">
                            <div class="game-title"><?php echo htmlspecialchars($game['title']); ?></div>
                            <?php
                            $original_price = (float) $game['price'];
                            $discount_price = isset($game['discount_price']) ? $game['discount_price'] : $original_price;
                            ?>
                            <?php if (isset($game['has_discount']) && $game['has_discount'] && $discount_price < $original_price): ?>
                                <div class="game-price">
                                    <span class="original-price">Rp
                                        <?php echo number_format($original_price, 2, ',', '.'); ?></span>
                                    <span class="discount-price"><br>Rp
                                        <?php echo number_format($discount_price, 2, ',', '.'); ?></span>
                                </div>
                            <?php else: ?>
                                <div class="game-price">Rp <?php echo number_format($original_price, 2, ',', '.'); ?></div>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>