<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil daftar game yang tersedia dengan diskon
$query_games = "
    SELECT games.*, 
           discount_events.discount_type, 
           discount_events.discount_value, 
           discount_events.start_date, 
           discount_events.end_date
    FROM games
    LEFT JOIN discount_event_games ON games.id = discount_event_games.game_id
    LEFT JOIN discount_events ON discount_event_games.event_id = discount_events.id
";
$result_games = $conn->query($query_games);

$games = [];
$current_time = date('Y-m-d H:i:s');

// Proses setiap game dan hitung harga diskon jika berlaku
while ($row = $result_games->fetch_assoc()) {
    $game_id = $row['id'];
    if (!isset($games[$game_id])) {
        $games[$game_id] = $row;
        $games[$game_id]['discount_price'] = (float) $row['price'];
    }

    // Cek apakah diskon aktif
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

        $discount_price = max($discount_price, 0); // Pastikan harga tidak negatif

        if ($discount_price < $games[$game_id]['discount_price']) {
            $games[$game_id]['discount_price'] = $discount_price;
            $games[$game_id]['has_discount'] = true;
        }
    }
}

// Proses pembelian
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['payment_method'])) {
    if (isset($_POST['game_ids']) && !empty($_POST['game_ids'])) {
        $game_ids = $_POST['game_ids'];
        $payment_method = $_POST['payment_method'];
        $total_amount = 0;

        // Hitung total harga berdasarkan harga diskon
        foreach ($game_ids as $game_id) {
            if (isset($games[$game_id])) {
                $total_amount += $games[$game_id]['discount_price'];
            }
        }

        // Simpan transaksi
        $status = "pending";
        foreach ($game_ids as $game_id) {
            $query_purchase = "INSERT INTO transactions (user_id, game_id, amount, payment_method, status, total_amount, transaction_date) 
                               VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmt_purchase = $conn->prepare($query_purchase);
            $stmt_purchase->bind_param("iiisss", $user_id, $game_id, $total_amount, $payment_method, $status, $total_amount);

            if (!$stmt_purchase->execute()) {
                echo "Error executing purchase query: " . $stmt_purchase->error;
                exit();
            }
        }

        $_SESSION['total_amount'] = $total_amount;
        $_SESSION['payment_method'] = $payment_method;
        $_SESSION['games'] = $game_ids;
        header("Location: confirmation.php");
        exit();
    } else {
        echo "Silakan pilih game yang ingin dibeli!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembelian Game</title>
    <link rel="stylesheet" href="buy_game.css">
</head>

<body>
    <div class="container">
        <h1>Pembelian Game</h1>
        <form method="POST" action="buy_game.php">
            <h2>Pilih Game yang Ingin Dibeli</h2>
            <div class="game-list">
                <?php foreach ($games as $game): ?>
                    <div class="game-item">
                        <label>
                            <input type="checkbox" name="game_ids[]" value="<?php echo $game['id']; ?>">
                            <?php echo htmlspecialchars($game['title']); ?> -
                            <?php if (isset($game['has_discount']) && $game['has_discount']): ?>
                                <span class="original-price">Rp <?php echo number_format($game['price'], 0, ',', '.'); ?></span>
                                <span class="discount-price">Rp
                                    <?php echo number_format($game['discount_price'], 0, ',', '.'); ?></span>
                            <?php else: ?>
                                Rp <?php echo number_format($game['price'], 0, ',', '.'); ?>
                            <?php endif; ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>



            <h2>Pilih Metode Pembayaran</h2>
            <select name="payment_method" required>
                <option value="" disabled selected>Pilih metode pembayaran</option>
                <option value="credit_card">Kartu Kredit</option>
                <option value="bank_transfer">Transfer Bank</option>
                <option value="e_wallet">Dompet Digital</option>
            </select>

            <div class="button-group">
                <button type="submit">Lanjutkan ke Pembayaran</button>
                <a href="dashboard.php" class="cancel-button">Batal</a>
            </div>
        </form>
    </div>
</body>

</html>