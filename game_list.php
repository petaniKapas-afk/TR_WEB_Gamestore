<!-- game_list.php -->
<?php
require 'koneksi.php';

// Handle search input
$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_param = '%' . $search . '%';

// Prepare and execute statement
$stmt = $conn->prepare("SELECT * FROM games WHERE title LIKE ? ORDER BY title ASC");
$stmt->bind_param('s', $search_param);
$stmt->execute();
$result = $stmt->get_result();
?>

<div id="game-list" class="game-list">
    <!-- Search Form -->
    <form method="GET" action="">
        <input type="text" name="search" placeholder="Search games..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">Search</button>
    </form>

    <!-- Game List -->
    <ul>
        <?php while ($game = $result->fetch_assoc()): ?>
            <li>
                <a href="game_detail.php?id=<?php echo $game['id']; ?>">
                    <?php echo htmlspecialchars($game['title']); ?> - $<?php echo number_format($game['price'], 2); ?>
                </a>
            </li>
        <?php endwhile; ?>
    </ul>
</div>
