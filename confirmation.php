<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['games'])) {
    header('Location: login.php');
    exit();
}

$total_amount = $_SESSION['total_amount'];
$payment_method = $_SESSION['payment_method'];
$game_ids = $_SESSION['games'];

// Ambil data game yang dibeli
require 'koneksi.php';
$games = [];
foreach ($game_ids as $game_id) {
    $query = "SELECT title FROM games WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $game_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $games[] = $result->fetch_assoc()['title'];
}

// Konfirmasi pembelian
if (isset($_POST['confirm'])) {
    // Pembelian berhasil, update status transaksi menjadi "completed"
    $user_id = $_SESSION['user_id'];
    $game_ids_str = implode(",", $game_ids);
    $query_update = "UPDATE transactions SET status = 'completed' WHERE user_id = ? AND game_id = ?";
    $stmt_update = $conn->prepare($query_update);
    $stmt_update->bind_param("ii", $user_id, $game_ids_str);
    $stmt_update->execute();

    // Menampilkan struk pembelian
    echo "<link rel='stylesheet' href='confirmation.css'>
    <div class='container'>
            <h1>Struk Pembelian</h1>
            <div class='receipt'>
                <p><strong>Games:</strong> " . implode(", ", $games) . "</p>
                <p><strong>Metode Pembayaran:</strong> $payment_method</p>
                <p><strong>Total Pembayaran:</strong> Rp " . number_format($total_amount, 0, ',', '.') . "</p>
                <div class='button-container'>
                    <a href='dashboard.php' class='back-button'>Kembali ke Dashboard</a>
                </div>
            </div>
          </div>";

    unset($_SESSION['games']); // Hapus data session pembelian
    exit();
} elseif (isset($_POST['cancel'])) {
    // Jika user batal, kembali ke halaman pemilihan game
    header("Location: buy_game.php");
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
        <h1 style="text-align:center;">Konfirmasi Pembelian</h1>
        <p style="text-align:center;">Apakah Anda yakin ingin melanjutkan pembelian?</p>

        <div class="confirmation-list">
            <p><strong>Games:</strong> <?php echo implode(", ", $games); ?></p>
            <p><strong>Metode Pembayaran:</strong> <?php echo $payment_method; ?></p>
            <p><strong>Total Pembayaran:</strong> Rp <?php echo number_format($total_amount, 0, ',', '.'); ?></p>
        </div>

        <form method="POST" action="confirmation.php">
            <button type="submit" name="confirm">Ya, Lanjutkan Pembelian</button>
            <button type="submit" name="cancel">Batal</button>
        </form>
    </div>
</body>

</html>