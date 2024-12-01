<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: admin_dashboard.php');
    exit();
}

$event_id = $_GET['id'];

// Fetch the original event data
$query_event = "SELECT * FROM discount_events WHERE id = ?";
$stmt = $conn->prepare($query_event);
$stmt->bind_param('i', $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    die('Discount event not found.');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_name = $_POST['event_name'];
    $discount_type = $_POST['discount_type'];
    $discount_value = $_POST['discount_value'];
    $game_ids = isset($_POST['game_ids']) ? $_POST['game_ids'] : [];

    // Validate and set start date
    if (!empty($_POST['start_date'])) {
        $start_date = $_POST['start_date'];
    } else {
        $start_date = $event['start_date'];
    }

    // Validate and set end date
    if (!empty($_POST['end_date'])) {
        $end_date = $_POST['end_date'];
    } else {
        $end_date = $event['end_date'];
    }

    // Update the event
    $update_event_query = "UPDATE discount_events SET event_name = ?, discount_type = ?, discount_value = ?, start_date = ?, end_date = ? WHERE id = ?";
    $stmt = $conn->prepare($update_event_query);
    $stmt->bind_param('ssdssi', $event_name, $discount_type, $discount_value, $start_date, $end_date, $event_id);
    $stmt->execute();

    // Update associated games
    $delete_game_assoc_query = "DELETE FROM discount_event_games WHERE event_id = ?";
    $stmt = $conn->prepare($delete_game_assoc_query);
    $stmt->bind_param('i', $event_id);
    $stmt->execute();

    foreach ($game_ids as $game_id) {
        $insert_game_assoc_query = "INSERT INTO discount_event_games (event_id, game_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_game_assoc_query);
        $stmt->bind_param('ii', $event_id, $game_id);
        $stmt->execute();
    }

    header('Location: admin_dashboard.php');
    exit();
}

$query_event_games = "SELECT game_id FROM discount_event_games WHERE event_id = ?";
$stmt = $conn->prepare($query_event_games);
$stmt->bind_param('i', $event_id);
$stmt->execute();
$result = $stmt->get_result();
$associated_game_ids = [];
while ($row = $result->fetch_assoc()) {
    $associated_game_ids[] = $row['game_id'];
}

$query_games = "SELECT * FROM games";
$games_result = $conn->query($query_games);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Discount Event</title>
    <link rel="stylesheet" href="admin_dashboard_edit.css">
</head>
<body>
<header class="header">
    <h1>Edit Discount Event</h1>
</header>
<nav class="nav-bar">
    <ul>
        <li><a href="admin_dashboard.php">Dashboard</a></li>
    </ul>
</nav>
<main>
    <div class="form-container">
        <form action="" method="post" class="edit-discount-form">
            <div class="form-group">
                <label for="event_name">Event Name</label>
                <input type="text" name="event_name" id="event_name" value="<?php echo htmlspecialchars($event['event_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="discount_type">Discount Type</label>
                <select name="discount_type" id="discount_type" required>
                    <option value="percentage" <?php if ($event['discount_type'] == 'percentage') echo 'selected'; ?>>Percentage</option>
                    <option value="fixed" <?php if ($event['discount_type'] == 'fixed') echo 'selected'; ?>>Fixed Amount</option>
                </select>
            </div>

            <div class="form-group">
                <label for="discount_value">Discount Value</label>
                <input type="number" step="0.01" name="discount_value" id="discount_value" value="<?php echo htmlspecialchars($event['discount_value']); ?>" required>
            </div>

            <div class="form-group">
                <label for="start_date">Start Date</label>
                <?php
                // Format the start date for datetime-local input
                $start_date_formatted = date('Y-m-d\TH:i', strtotime($event['start_date']));
                ?>
                <input type="datetime-local" name="start_date" id="start_date" value="<?php echo $start_date_formatted; ?>" required>
            </div>

            <div class="form-group">
                <label for="end_date">End Date</label>
                <?php
                // Format the end date for datetime-local input
                $end_date_formatted = date('Y-m-d\TH:i', strtotime($event['end_date']));
                ?>
                <input type="datetime-local" name="end_date" id="end_date" value="<?php echo $end_date_formatted; ?>" required>
            </div>

            <div class="form-group select-games">
                <label>Select Games</label>
                <div class="game-checkboxes">
                    <?php while ($game = $games_result->fetch_assoc()) { ?>
                        <div class="game-option">
                            <input type="checkbox" name="game_ids[]" id="game_<?php echo $game['id']; ?>" value="<?php echo $game['id']; ?>" <?php if (in_array($game['id'], $associated_game_ids)) echo 'checked'; ?>>
                            <label for="game_<?php echo $game['id']; ?>"><?php echo htmlspecialchars($game['title']); ?></label>
                        </div>
                    <?php } ?>
                </div>
            </div>

            <div class="form-buttons">
                <button type="submit" class="btn submit-btn">Update Discount Event</button>
                <button type="button" class="btn delete-btn" onclick="confirmDelete(<?php echo $event_id; ?>)">Delete Discount Event</button>
            </div>
        </form>
    </div>
</main>
<script>
function confirmDelete(eventId) {
    if (confirm('Are you sure you want to delete this discount event?')) {
        window.location.href = 'delete_discount_event.php?id=' + eventId;
    }
}
</script>
</body>
</html>
