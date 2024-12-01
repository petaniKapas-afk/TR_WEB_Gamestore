<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_name = $_POST['event_name'];
    $discount_type = $_POST['discount_type'];
    $discount_value = $_POST['discount_value'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $game_ids = isset($_POST['game_ids']) ? $_POST['game_ids'] : [];

    if (empty($event_name) || empty($discount_type) || empty($discount_value) || empty($start_date) || empty($end_date) || empty($game_ids)) {
        $error_message = "tolong isi semua dan pilih paling sedikit satu game";
    } else {
        $stmt = $conn->prepare("INSERT INTO discount_events (event_name, discount_type, discount_value, start_date, end_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdss", $event_name, $discount_type, $discount_value, $start_date, $end_date);

        if ($stmt->execute()) {
            $event_id = $stmt->insert_id;
            $stmt_game = $conn->prepare("INSERT INTO discount_event_games (event_id, game_id) VALUES (?, ?)");
            foreach ($game_ids as $game_id) {
                $stmt_game->bind_param("ii", $event_id, $game_id);
                $stmt_game->execute();
            }
            $success_message = "Discount event berhasil!";
        } else {
            $error_message = "Error membuat discount event: " . $stmt->error;
        }
    }
}

$games_result = $conn->query("SELECT * FROM games ORDER BY title ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Buat Discount Event</title>
    <link rel="stylesheet" href="discount_event.css">
</head>
<body>

<header>
    <h1>Buat Discount Event</h1>
    <nav>
       <a href="admin_dashboard.php" class="btn">Back to Dashboard</a>
   </nav>
</header>

<main>
    <?php
    if (isset($error_message)) {
        echo '<div class="error-message">'.$error_message.'</div>';
    }
    if (isset($success_message)) {
        echo '<div class="success-message">'.$success_message.'</div>';
    }
    ?>
    <form method="POST" action="">
        <div class="form-group">
            <label for="event_name">Event Name:</label>
            <input type="text" name="event_name" id="event_name" required>
        </div>
        <div class="form-group">
            <label for="discount_type">Discount Type:</label>
            <select name="discount_type" id="discount_type">
                <option value="percentage">Persen (%)</option>
                <option value="fixed">Fixed Amount (IDR)</option>
            </select>
        </div>
        <div class="form-group">
            <label for="discount_value">Discount:</label>
            <input type="number" step="0.01" name="discount_value" id="discount_value" required>
        </div>
        <div class="form-group">
            <label for="start_date">Mulai:</label>
            <input type="datetime-local" name="start_date" id="start_date" required>
        </div>
        <div class="form-group">
            <label for="end_date">Akhir:</label>
            <input type="datetime-local" name="end_date" id="end_date" required>
        </div>
        <div class="form-group">
            <label>Select Games:</label>
            <div class="games-list">
                <?php while ($game = $games_result->fetch_assoc()) { ?>
                    <div class="game-item">
                        <input type="checkbox" name="game_ids[]" id="game_<?php echo $game['id']; ?>" value="<?php echo $game['id']; ?>">
                        <label for="game_<?php echo $game['id']; ?>">
                            <?php echo htmlspecialchars($game['title']); ?>
                        </label>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="form-group">
            <button type="submit">Create Discount Event</button>
        </div>
    </form>
</main>

</body>
</html>
