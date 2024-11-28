<?php
require 'koneksi.php';

$stmt = $conn->prepare("SELECT * FROM games");
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game List</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Available Games</h2>
    <ul>
        <?php while ($game = $result->fetch_assoc()): ?>
            <li>
                <a href="game_detail.php?id=<?php echo $game['id']; ?>">
                    <?php echo $game['title']; ?> - $<?php echo $game['price']; ?>
                </a>
            </li>
        <?php endwhile; ?>
    </ul>
</body>
</html>
