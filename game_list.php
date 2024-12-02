<?php
require 'koneksi.php';

// Handle AJAX search request
if (isset($_GET['ajax_search'])) {
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    $search_param = '%' . $search . '%';

    $stmt = $conn->prepare("
        SELECT games.id, games.title, games.price, games.image,
               discount_events.discount_type, discount_events.discount_value,
               discount_events.start_date, discount_events.end_date
        FROM games
        LEFT JOIN discount_event_games ON games.id = discount_event_games.game_id
        LEFT JOIN discount_events ON discount_event_games.event_id = discount_events.id
        WHERE games.title LIKE ?
        ORDER BY games.title ASC
    ");
    $stmt->bind_param('s', $search_param);
    $stmt->execute();
    $result = $stmt->get_result();

    $games = [];
    while ($row = $result->fetch_assoc()) {
        $game = [
            'id' => $row['id'],
            'title' => $row['title'],
            'price' => (float)$row['price'],
            'image' => $row['image'],
            'discount_price' => $row['price'],
            'has_discount' => false,
        ];

        $current_time = date('Y-m-d H:i:s');
        if (!empty($row['discount_type']) &&
            $row['start_date'] <= $current_time &&
            $row['end_date'] >= $current_time
        ) {
            if ($row['discount_type'] === 'percentage') {
                $game['discount_price'] = $game['price'] * (1 - $row['discount_value'] / 100);
            } elseif ($row['discount_type'] === 'fixed') {
                $game['discount_price'] = max(0, $game['price'] - $row['discount_value']);
            }
            $game['has_discount'] = true;
        }

        $games[] = $game;
    }

    header('Content-Type: application/json');
    echo json_encode($games);
    exit();
}

// Default query for all games when the page loads
$stmt = $conn->prepare("
    SELECT games.id, games.title, games.price, games.image,
           discount_events.discount_type, discount_events.discount_value,
           discount_events.start_date, discount_events.end_date
    FROM games
    LEFT JOIN discount_event_games ON games.id = discount_event_games.game_id
    LEFT JOIN discount_events ON discount_event_games.event_id = discount_events.id
    ORDER BY games.title ASC
");
$stmt->execute();
$result = $stmt->get_result();

$games = [];
while ($row = $result->fetch_assoc()) {
    $game = [
        'id' => $row['id'],
        'title' => $row['title'],
        'price' => (float)$row['price'],
        'image' => $row['image'],
        'discount_price' => $row['price'],
        'has_discount' => false,
    ];

    $current_time = date('Y-m-d H:i:s');
    if (!empty($row['discount_type']) &&
        $row['start_date'] <= $current_time &&
        $row['end_date'] >= $current_time
    ) {
        if ($row['discount_type'] === 'percentage') {
            $game['discount_price'] = $game['price'] * (1 - $row['discount_value'] / 100);
        } elseif ($row['discount_type'] === 'fixed') {
            $game['discount_price'] = max(0, $game['price'] - $row['discount_value']);
        }
        $game['has_discount'] = true;
    }

    $games[] = $game;
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
        <form id="search-form">
            <input type="text" id="search-input" name="search" placeholder="Search games...">
            <button type="submit">Search</button>
        </form>

        <div id="game-results" class="game-grid">
            <!-- Default list of games -->
            <?php foreach ($games as $game): ?>
                <div class="game-item">
                    <a href="game_detail.php?id=<?php echo $game['id']; ?>">
                        <img src="uploads/<?php echo htmlspecialchars($game['image']); ?>" alt="<?php echo htmlspecialchars($game['title']); ?>">
                        <div class="game-info">
                            <div class="game-title"><?php echo htmlspecialchars($game['title']); ?></div>
                            <div class="game-price">
                                <?php if ($game['has_discount']): ?>
                                    <span class="original-price">Rp <?php echo number_format($game['price'], 0, ',', '.'); ?></span>
                                    <span class="discount-price">Rp <?php echo number_format($game['discount_price'], 0, ',', '.'); ?></span>
                                <?php else: ?>
                                    Rp <?php echo number_format($game['price'], 0, ',', '.'); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script>
        document.getElementById('search-form').addEventListener('submit', function (event) {
            event.preventDefault();

            const searchInput = document.getElementById('search-input').value;

            fetch(`game_list.php?ajax_search=1&search=${encodeURIComponent(searchInput)}`)
                .then((response) => response.json())
                .then((games) => {
                    const resultsContainer = document.getElementById('game-results');
                    resultsContainer.innerHTML = '';

                    if (!games || games.length === 0) {
                        resultsContainer.innerHTML = '<p>No games found.</p>';
                        return;
                    }

                    games.forEach((game) => {
                        const gameItem = document.createElement('div');
                        gameItem.classList.add('game-item');

                        const discountPrice = game.has_discount
                            ? `<span class="original-price">Rp ${game.price.toLocaleString('id-ID')}</span>
                               <span class="discount-price">Rp ${game.discount_price.toLocaleString('id-ID')}</span>`
                            : `Rp ${game.price.toLocaleString('id-ID')}`;

                        gameItem.innerHTML = `
                            <a href="game_detail.php?id=${game.id}">
                                <img src="uploads/${game.image}" alt="${game.title}">
                                <div class="game-info">
                                    <div class="game-title">${game.title}</div>
                                    <div class="game-price">${discountPrice}</div>
                                </div>
                            </a>
                        `;
                        resultsContainer.appendChild(gameItem);
                    });
                })
                .catch((error) => {
                    console.error('Error fetching games:', error);
                });
        });
    </script>
</body>

</html>