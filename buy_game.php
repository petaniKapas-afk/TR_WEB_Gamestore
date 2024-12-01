<?php
session_start();
require 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil daftar game yang tersedia
$query_games = "SELECT * FROM games";
$result_games = $conn->query($query_games);

// Proses ketika form dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['payment_method'])) {
    if (isset($_POST['game_ids']) && !empty($_POST['game_ids'])) {
        $game_ids = $_POST['game_ids']; // Daftar ID game yang dipilih
        $payment_method = $_POST['payment_method'];
        $total_amount = 0;

        // Hitung total harga dari game yang dipilih
        foreach ($game_ids as $game_id) {
            $query = "SELECT price FROM games WHERE id = ?";
            $stmt = $conn->prepare($query);
            if ($stmt === false) {
                die('Error preparing query: ' . $conn->error);
            }
            $stmt->bind_param("i", $game_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $game = $result->fetch_assoc();
            $total_amount += $game['price'];
        }

        // Menyimpan transaksi untuk setiap game
        $status = "pending"; // Status sementara
        foreach ($game_ids as $game_id) {
            $query_purchase = "INSERT INTO transactions (user_id, game_id, amount, payment_method, status, total_amount, transaction_date) 
                               VALUES (?, ?, ?, ?, ?, ?, NOW())";
            $stmt_purchase = $conn->prepare($query_purchase);
            if ($stmt_purchase === false) {
                die('Error preparing query: ' . $conn->error);
            }
            $stmt_purchase->bind_param("iiisss", $user_id, $game_id, $total_amount, $payment_method, $status, $total_amount);

            if (!$stmt_purchase->execute()) {
                echo "Error executing purchase query: " . $stmt_purchase->error;
                exit;
            }
        }

        $_SESSION['total_amount'] = $total_amount;
        $_SESSION['payment_method'] = $payment_method;
        $_SESSION['games'] = $game_ids;
        header("Location: confirmation.php"); // Arahkan ke halaman konfirmasi
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
                <?php while ($game = $result_games->fetch_assoc()) { ?>
                    <div class="game-item">
                        <label>
                            <input type="checkbox" name="game_ids[]" value="<?php echo $game['id']; ?>">
                            <?php echo $game['title']; ?> - Rp <?php echo number_format($game['price'], 0, ',', '.'); ?>
                        </label>
                    </div>
                <?php } ?>
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