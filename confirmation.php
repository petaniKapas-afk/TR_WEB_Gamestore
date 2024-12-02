<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['games'])) {
    header('Location: login.php');
    exit();
}

$total_amount = $_SESSION['total_amount'];
$payment_method = $_SESSION['payment_method'];
$game_ids = $_SESSION['games'];

require 'koneksi.php';
$games = [];
foreach ($game_ids as $game_id) {
    $query = "SELECT title FROM games WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $game_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $game = $result->fetch_assoc();
    $games[] = $game['title'];
}

if (isset($_POST['confirm'])) {
    $user_id = $_SESSION['user_id'];
    $status = 'pending';
    $transaction_ids = []; 

    foreach ($game_ids as $game_id) {
        $query_insert = "INSERT INTO transactions (user_id, game_id, amount, payment_method, status, total_amount, transaction_date)
                         VALUES (?, ?, ?, ?, ?, ?, NOW())";
        $stmt_insert = $conn->prepare($query_insert);
        $stmt_insert->bind_param("iiisss", $user_id, $game_id, $total_amount, $payment_method, $status, $total_amount);
        $stmt_insert->execute();
        $transaction_ids[] = $conn->insert_id;
    }

    sort($transaction_ids);

    header('Location: dashboard.php');
    exit();
}

if (isset($_POST['cancel'])) {
    header('Location: buy_game.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pembelian</title>
    <link rel="stylesheet" href="confirmation.css">
</head>

<body>
    <div class="container">
        <h1 style="text-align: center;">Konfirmasi Pembelian</h1>
        <p style="text-align: center;">Apakah Anda yakin ingin melanjutkan pembelian?</p>

        <div class="confirmation-list">
            <p><strong>Games:</strong> <?php echo implode(", ", $games); ?></p>
            <p><strong>Metode Pembayaran:</strong> <?php echo htmlspecialchars($payment_method); ?></p>
            <p><strong>Total Pembayaran:</strong> Rp <?php echo number_format($total_amount, 0, ',', '.'); ?></p>
        </div>

        <form method="POST" action="confirmation.php">
            <button type="submit" name="confirm">Ya, Lanjutkan Pembayaran</button>
            <button type="submit" name="cancel">Batal</button>
        </form>
    </div>
</body>

</html>
